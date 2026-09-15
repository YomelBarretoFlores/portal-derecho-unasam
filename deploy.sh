#!/usr/bin/env bash
#
# Actualiza el portal a la última versión publicada en el repositorio.
#
# Pensado para ejecutarse sin vigilancia (desde cron) o a mano. Es idempotente:
# si no hay nada nuevo, no toca nada y termina.
#
#   ./deploy.sh              actualiza si hay algo nuevo
#   ./deploy.sh --forzar     rehace el despliegue aunque no haya cambios
#   ./deploy.sh --revisar    dice si hay algo nuevo y no hace nada más
#
# Cualquier fallo detiene el proceso y deja el sitio como estaba.

set -Eeuo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")"

RAMA="${DEPLOY_RAMA:-main}"
REGISTRO="storage/logs/despliegue.log"
CERROJO="storage/framework/despliegue.lock.d"

FORZAR=false
SOLO_REVISAR=false
for arg in "$@"; do
    case "$arg" in
        --forzar)  FORZAR=true ;;
        --revisar) SOLO_REVISAR=true ;;
        *) echo "Opción desconocida: $arg" >&2; exit 2 ;;
    esac
done

mkdir -p "$(dirname "$REGISTRO")" "$(dirname "$CERROJO")"

decir() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*" | tee -a "$REGISTRO"; }
morir() { decir "ERROR: $*"; exit 1; }

trap 'morir "falló en la línea $LINENO. El sitio sigue con la versión anterior."' ERR

# Un solo despliegue a la vez. Sin esto, dos ejecuciones solapadas de cron
# pueden dejar las dependencias a medias mientras el sitio está sirviendo.
#
# Se usa mkdir en vez de flock porque mkdir es atómico en cualquier sistema y
# flock no está en todas partes. Un cerrojo que no existe es peor que ninguno:
# la primera versión de esto decía «ya hay un despliegue en curso» cuando lo
# que pasaba era que faltaba el programa.
if ! mkdir "$CERROJO" 2>/dev/null; then
    # Un despliegue interrumpido (corte de luz, sesión cerrada) deja el cerrojo
    # puesto para siempre. Pasada media hora se da por muerto.
    if [ -d "$CERROJO" ] && [ -z "$(find "$CERROJO" -maxdepth 0 -mmin -30 2>/dev/null)" ]; then
        echo "Cerrojo antiguo de un despliegue interrumpido; se descarta."
        rm -rf "$CERROJO"
        mkdir "$CERROJO" || { echo "No se pudo tomar el cerrojo."; exit 1; }
    else
        echo "Ya hay un despliegue en curso."
        exit 0
    fi
fi

trap 'rm -rf "$CERROJO"' EXIT

# ---------------------------------------------------------------------------
# 1. ¿Hay algo nuevo?
# ---------------------------------------------------------------------------

git fetch --quiet origin "$RAMA"

ACTUAL="$(git rev-parse HEAD)"
NUEVA="$(git rev-parse "origin/$RAMA")"

if [ "$ACTUAL" = "$NUEVA" ] && [ "$FORZAR" = false ]; then
    [ "$SOLO_REVISAR" = true ] && echo "Al día: $(git log -1 --format='%h %s')"
    exit 0
fi

if [ "$SOLO_REVISAR" = true ]; then
    echo "Hay versión nueva:"
    git --no-pager log --oneline "$ACTUAL..$NUEVA"
    exit 0
fi

# ---------------------------------------------------------------------------
# 2. Comprobaciones antes de tocar nada
# ---------------------------------------------------------------------------

if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
    decir "Hay cambios sin guardar en el servidor. El despliegue se detiene."
    git --no-pager status --short --untracked-files=no | tee -a "$REGISTRO"
    morir "Descártalos con «git checkout -- <archivo>» o guárdalos, y vuelve a lanzarlo."
fi

for programa in php composer git; do
    command -v "$programa" >/dev/null || morir "falta «$programa» en el PATH."
done

# El frontend se compila aquí porque public/build no viaja en el repositorio.
# Sin Node, el sitio se quedaría sirviendo los estilos de la versión anterior
# sin decir nada, que es peor que fallar.
command -v npm >/dev/null || morir "falta «npm». Sin él los estilos no se actualizan."

decir "=== Despliegue: ${ACTUAL:0:7} -> ${NUEVA:0:7} ==="
git --no-pager log --oneline "$ACTUAL..$NUEVA" | tee -a "$REGISTRO"

# ---------------------------------------------------------------------------
# 3. Actualizar
# ---------------------------------------------------------------------------

decir "Trayendo el código…"
git merge --ff-only "origin/$RAMA" >>"$REGISTRO" 2>&1

decir "Dependencias de PHP…"
composer install --no-dev --optimize-autoloader --no-interaction >>"$REGISTRO" 2>&1

decir "Compilando estilos y scripts…"
npm ci --silent >>"$REGISTRO" 2>&1
npm run build >>"$REGISTRO" 2>&1

decir "Migraciones…"
php artisan migrate --force >>"$REGISTRO" 2>&1

decir "Enlace de archivos públicos…"
php artisan storage:link >>"$REGISTRO" 2>&1 || true

decir "Recacheando configuración…"
php artisan config:cache >>"$REGISTRO" 2>&1
php artisan route:cache >>"$REGISTRO" 2>&1
php artisan view:cache >>"$REGISTRO" 2>&1
php artisan content:cache:warm >>"$REGISTRO" 2>&1

# ---------------------------------------------------------------------------
# 4. Recargar PHP-FPM
# ---------------------------------------------------------------------------
#
# Sin esto el despliegue parece correcto y el sitio sigue ejecutando el código
# viejo: OPcache guarda en memoria los ficheros PHP ya compilados y no vuelve a
# leer el disco. Es el fallo más desconcertante de todos, porque «git log» dice
# que la versión nueva está puesta.

if command -v systemctl >/dev/null; then
    SERVICIO="$(systemctl list-units --type=service --no-legend 2>/dev/null \
        | awk '{print $1}' | grep -E '^php.*fpm\.service$' | head -1 || true)"

    if [ -n "$SERVICIO" ]; then
        if systemctl reload "$SERVICIO" >>"$REGISTRO" 2>&1; then
            decir "PHP-FPM recargado ($SERVICIO)."
        else
            decir "AVISO: no se pudo recargar $SERVICIO. Hazlo a mano:"
            decir "  sudo systemctl reload $SERVICIO"
        fi
    else
        decir "AVISO: no se encontró el servicio de PHP-FPM. Si el sitio sigue"
        decir "       mostrando la versión anterior, recárgalo a mano."
    fi
fi

# ---------------------------------------------------------------------------
# 5. Comprobar que sigue en pie
# ---------------------------------------------------------------------------

if [ -n "${DEPLOY_URL:-}" ]; then
    CODIGO="$(curl -s -o /dev/null -w '%{http_code}' --max-time 20 "$DEPLOY_URL" || echo 000)"
    if [ "$CODIGO" = "200" ]; then
        decir "La portada responde 200."
    else
        decir "AVISO: la portada respondió $CODIGO. Revisa storage/logs/laravel.log."
    fi
fi

decir "=== Listo: $(git log -1 --format='%h %s') ==="
