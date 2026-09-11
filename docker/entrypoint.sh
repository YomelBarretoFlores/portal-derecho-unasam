#!/bin/sh
set -e

# Render provee la URL pública del servicio en RENDER_EXTERNAL_URL.
# La usamos como APP_URL para evitar errores de tipeo (un espacio o carácter
# inválido en APP_URL rompe el arranque al construir la Request de Livewire).
if [ -n "$RENDER_EXTERNAL_URL" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
fi

echo "→ APP_URL = ${APP_URL}"
echo "→ Preparando la aplicación…"

# Cachear configuración, rutas y vistas para producción (más rápido)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones pendientes contra la base de datos (PostgreSQL)
php artisan migrate --force

# Descartar los planes de consulta que el pooler guarda de antes de migrar.
#
# Sin esto, una migración que añada una columna deja el sitio entero en 500 con
# «cached plan must not change result type», y redesplegar no lo arregla: los
# planes viven en el servidor, no en el contenedor. Ver el comando para el
# detalle. Si falla, no se impide el arranque: el aviso queda en el registro y
# se puede repetir a mano con más conexiones.
echo "→ Planes de consulta…"
php artisan db:limpiar-planes || echo "⚠  Quedan planes obsoletos. Repita: php artisan db:limpiar-planes --conexiones=100"

# Contenido institucional inicial.
#
# Sin esto, un despliegue nuevo levanta con las tablas creadas y vacías: el
# sitio responde 200 y no muestra absolutamente nada. Ya pasó una vez, y desde
# fuera parecía un problema de base de datos.
#
# Es seguro repetirlo en cada despliegue: cada bloque solo actúa si su tabla
# está vacía, y los ajustes solo crean las claves que falten. No pisa nada de
# lo que se haya editado desde el panel.
echo "→ Contenido institucional…"
php artisan db:seed --force --class=Database\\Seeders\\ContenidoInstitucionalSeeder

# Usuario administrador inicial.
#
# Va aparte y tolera el fallo a propósito. Necesita ADMIN_NAME, ADMIN_EMAIL y
# ADMIN_PASSWORD; si faltan, es mejor un portal en pie sin cuenta creada que un
# contenedor que no arranca. El aviso queda en el registro de despliegue.
echo "→ Usuario administrador…"
if ! php artisan db:seed --force --class=Database\\Seeders\\AdminUserSeeder; then
    echo "⚠  NO se creó el usuario administrador."
    echo "⚠  Defina ADMIN_NAME, ADMIN_EMAIL y ADMIN_PASSWORD (mínimo 12 caracteres,"
    echo "⚠  con mayúscula, minúscula, número y símbolo) y vuelva a desplegar."
    echo "⚠  El sitio público funciona; /admin no tendrá con qué entrar."
fi

# Enlace público de almacenamiento para los medios subidos (Spatie)
php artisan storage:link || true

echo "→ Iniciando FrankenPHP en el puerto ${PORT}…"
exec frankenphp run --config /etc/caddy/Caddyfile
