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

# Ejecutar migraciones pendientes contra la base de datos (Neon)
php artisan migrate --force

# Enlace público de almacenamiento para los medios subidos (Spatie)
php artisan storage:link || true

echo "→ Iniciando FrankenPHP en el puerto ${PORT}…"
exec frankenphp run --config /etc/caddy/Caddyfile
