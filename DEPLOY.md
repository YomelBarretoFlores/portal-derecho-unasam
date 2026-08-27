# Despliegue en Render

El servicio usa el Dockerfile, Node 22 para compilar assets y FrankenPHP/PHP 8.4 para ejecutar Laravel. docker/entrypoint.sh cachea configuración, rutas y vistas, aplica migraciones y arranca el servidor.

## Variables obligatorias

| Variable | Descripción |
|---|---|
| APP_KEY | Resultado de php artisan key:generate --show |
| APP_URL | URL HTTPS pública |
| DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD | PostgreSQL/Neon |
| DB_PERSISTENT | false en producción |
| TRUSTED_PROXIES | IP o CIDR real del proxy, separado por comas |
| SESSION_SECURE_COOKIE | true |
| MEDIA_UPLOADS_ENABLED | false en Render |
| SUBMISSIONS_ENABLED | false hasta aprobar privacidad y almacenamiento |
| SUBMISSIONS_DISK | Disco privado persistente; `local` solo en desarrollo |
| SUBMISSIONS_PRIVACY_APPROVED | false mientras la declaración esté en preparación |
| SUBMISSIONS_STORAGE_PERSISTENT | false en Render sin volumen u object storage |

No uses TRUSTED_PROXIES=*. Laravel rechazará esa configuración en producción.

## Primera publicación

1. Crear un respaldo de la base y de storage/app/public.
2. Configurar las variables del Blueprint render.yaml.
3. Desplegar y comprobar /up.
4. Revisar que las migraciones hayan finalizado.
5. Confirmar en los logs que `content:cache:warm` terminó correctamente.
6. Crear el superadministrador desde un entorno seguro con AdminUserSeeder y variables temporales.
7. Entrar a /admin y verificar el acceso con la cuenta administrativa creada.
8. Confirmar que los campos de archivos indican que las cargas están deshabilitadas.
9. Confirmar que `/revista/envios` no muestra el formulario en Render.

## Archivos y copias de seguridad

El filesystem de Render es efímero. En esta fase:

- Los textos y metadatos sí se pueden editar.
- No se permiten nuevas cargas desde Filament.
- No se garantiza la permanencia de archivos escritos durante la ejecución.

Antes de habilitar cargas en producción se debe conectar almacenamiento persistente, migrar los medios existentes, validar URLs y establecer copias de seguridad/restauración.

Los envíos de manuscritos se habilitan únicamente cuando las tres variables de seguridad (`SUBMISSIONS_ENABLED`, `SUBMISSIONS_PRIVACY_APPROVED` y `SUBMISSIONS_STORAGE_PERSISTENT`) están en `true`. Sus archivos no deben usar el disco público.

## Recuperación administrativa

No hay recuperación por correo mientras no exista SMTP. Si se pierde el acceso, otro superadministrador puede restablecer la contraseña; nunca se deben introducir contraseñas en logs o commits.
