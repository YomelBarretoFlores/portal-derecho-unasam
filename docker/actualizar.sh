#!/bin/sh
#
# Actualiza el portal cuando hay cambios en la rama main del repositorio.
#
# Pensado para que el área de sistemas lo programe una vez y no tenga que
# volver a intervenir. Quien desarrolla publica sus cambios en GitHub; el
# servidor los recoge solo. Nadie necesita credenciales de acceso al servidor
# para publicar una corrección.
#
#   INSTALACIÓN
#
#   1. Clonar el repositorio (es público, no hacen falta credenciales):
#
#        git clone https://github.com/YomelBarretoFlores/portal-derecho-unasam.git /opt/portal-derecho
#
#   2. Crear /opt/portal-derecho/portal.env con las variables de entorno.
#      Ese archivo contiene contraseñas: chmod 600 y dueño el usuario que
#      ejecuta el servicio.
#
#   3. Levantarlo una primera vez a mano y comprobar el registro:
#
#        cd /opt/portal-derecho && docker compose up -d --build && docker compose logs -f
#
#   4. Programarlo en cron, cada cinco minutos:
#
#        */5 * * * * /opt/portal-derecho/docker/actualizar.sh >> /var/log/portal-derecho.log 2>&1
#
# Se puede ejecutar a mano en cualquier momento para forzar la comprobación.
#
# No abre ningún puerto ni recibe conexiones: es el servidor quien consulta
# GitHub. Por eso se prefiere a un webhook, que exigiría exponer un punto de
# entrada más hacia Internet.

set -e

# Directorio del proyecto. Se deduce de la ubicación de este script, así que
# funciona igual esté donde esté clonado el repositorio.
PROYECTO="$(cd "$(dirname "$0")/.." && pwd)"
cd "$PROYECTO"

RAMA="${PORTAL_RAMA:-main}"

# Un solo proceso a la vez.
#
# Con cron cada cinco minutos y una construcción de imagen que puede tardar
# más que eso, dos ejecuciones podrían solaparse: la segunda encontraría el
# directorio a medio actualizar. El bloqueo evita ese cruce; si ya hay una en
# marcha, esta se retira sin hacer nada.
BLOQUEO="/tmp/portal-derecho-actualizar.lock"
if ! mkdir "$BLOQUEO" 2>/dev/null; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Ya hay una actualización en curso; se omite."
    exit 0
fi
trap 'rmdir "$BLOQUEO" 2>/dev/null || true' EXIT INT TERM

registrar() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

git fetch --quiet origin "$RAMA"

ACTUAL="$(git rev-parse HEAD)"
REMOTO="$(git rev-parse "origin/$RAMA")"

if [ "$ACTUAL" = "$REMOTO" ]; then
    # Silencio deliberado en el caso normal: con cron cada cinco minutos, un
    # mensaje por cada comprobación llenaría el registro de ruido y escondería
    # las actualizaciones de verdad.
    exit 0
fi

registrar "Cambios detectados en origin/$RAMA."
registrar "  de  $(git log -1 --format='%h %s' "$ACTUAL")"
registrar "  a   $(git log -1 --format='%h %s' "$REMOTO")"

# reset --hard y no merge: el servidor no debe tener trabajo propio que
# conservar. Si alguien editó archivos ahí, esa edición se pierde, y es lo
# correcto: el repositorio es la única fuente de verdad del código.
git reset --hard --quiet "$REMOTO"

registrar "Reconstruyendo la imagen y reiniciando…"
docker compose up -d --build

# El contenedor aplica migraciones y siembra el contenido al arrancar, así que
# tarda unos segundos en responder. Se comprueba que llegue a estar sano en
# lugar de dar por bueno el despliegue solo porque el comando no falló.
PUERTO="${PORTAL_PUERTO:-8080}"
INTENTOS=30

while [ "$INTENTOS" -gt 0 ]; do
    if curl -fsS -o /dev/null "http://127.0.0.1:${PUERTO}/up" 2>/dev/null; then
        registrar "Actualizado y respondiendo correctamente."
        exit 0
    fi
    INTENTOS=$((INTENTOS - 1))
    sleep 2
done

registrar "ATENCIÓN: se actualizó pero el sitio no responde en /up después de 60 s."
registrar "Revise el registro del contenedor: docker compose logs --tail=100"
exit 1
