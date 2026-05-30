#!/bin/sh
set -e

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
