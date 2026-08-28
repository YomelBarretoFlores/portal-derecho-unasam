# syntax=docker/dockerfile:1

# ============================================================
#  Etapa 1 — Compilar los assets de frontend (Vite + Tailwind)
# ============================================================
FROM node:22-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
COPY resources ./resources
RUN npm run build


# ============================================================
#  Etapa 2 — Runtime PHP con FrankenPHP (servidor + PHP)
# ============================================================
FROM dunglas/frankenphp:1-php8.4 AS app

# Extensiones que necesitan Laravel 13, Filament, Spatie Media Library y Neon (pgsql)
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    gd \
    intl \
    zip \
    bcmath \
    exif \
    opcache \
    pcntl

WORKDIR /app

# Composer (binario oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependencias PHP de producción (capa cacheable con solo los manifiestos)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copiar el código de la aplicación
COPY . .

# Permitir los cuatro adjuntos del formulario dentro de los límites validados por Laravel.
COPY docker/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Assets ya compilados desde la etapa 1
COPY --from=assets /app/public/build ./public/build

# Autoload optimizado + descubrir paquetes
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

# Quitar las file-capabilities del binario de FrankenPHP.
# Render ejecuta como no-root y recorta el bounding set de capabilities, lo que
# impediría ejecutar un binario con cap_net_bind_service (exec: Operation not
# permitted). No las necesitamos: escuchamos en $PORT (puerto alto), no en 80/443.
RUN setcap -r /usr/local/bin/frankenphp || true

# Permisos de escritura para storage y caché
RUN chmod -R ug+rw storage bootstrap/cache

# Configuración del servidor y arranque
COPY docker/Caddyfile /etc/caddy/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# Render inyecta el puerto en $PORT (lo lee el Caddyfile)
ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["entrypoint"]
