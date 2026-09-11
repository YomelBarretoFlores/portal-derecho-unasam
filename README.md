# Portal de Derecho y Ciencias Políticas — UNASAM

Portal institucional y CMS para el Programa de Estudios de Derecho y Ciencias Políticas. Incluye páginas académicas, estadísticas, documentos, comunicados, blog, plana docente y la revista “Derecho y Cultura” organizada por volúmenes y números.

## Stack

- PHP 8.3+ y Laravel 13 (la CI compila y prueba con 8.4)
- Filament 5 con autenticación administrativa por correo y contraseña
- Blade, Livewire, Tailwind CSS 4 y Vite 8
- Spatie Media Library sobre almacenamiento público local
- PostgreSQL en producción; SQLite en pruebas

## Instalación local

    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan storage:link
    php artisan migrate --seed
    npm ci
    npm run build
    composer run dev

El sitio queda en http://localhost:8000 y el CMS en http://localhost:8000/admin.
El comando de desarrollo inicia PHP con límites locales de 25 MB por archivo y 30 MB por petición, suficientes para los documentos permitidos por `MEDIA_MAX_PDF_KB`.

En Windows (PowerShell o Warp), usa el comando configurado sin Laravel Pail, ya que Pail requiere la extensión `pcntl`:

    .\vendor\_runtime\php84\php.exe .codex\composer.phar run dev:windows

El seeder no contiene credenciales predeterminadas. Para crear el primer superadministrador, define temporalmente:

    ADMIN_NAME="Nombre del responsable"
    ADMIN_EMAIL="responsable@unasam.edu.pe"
    ADMIN_PASSWORD="Una-clave-segura-2026"

Después ejecuta php artisan db:seed --class=AdminUserSeeder y retira la contraseña del entorno. La contraseña debe tener al menos 12 caracteres, mayúsculas, minúsculas, números y símbolos. El administrador puede actualizar su nombre y contraseña desde el perfil del panel.

## Contenido y publicación

- super_admin: administra usuarios, seguridad, contenido y auditoría.
- editor: administra contenido; no puede administrar usuarios ni seguridad.
- Blog, comunicados, números y artículos admiten borradores y programación por fecha.
- Un artículo solo es visible si pertenece a un número público y tiene contenido web o PDF.
- No existen seeders demostrativos ni registros demostrativos. Los seeders restantes contienen únicamente configuración e información institucional heredada; el contenido editorial se crea desde `/admin` y puede revisarse mediante una vista previa privada antes de publicarse.
- El contenido heredado muestra su procedencia y estado de revisión.
- La ficha de “Derecho y Cultura” procede de la RCF N.° 063-2026-UNASAM-FDCCPP/D. e incluye enfoque, políticas, equipo editorial y normas para autores.
- El ISSN permanece vacío hasta que exista una asignación oficial; no se ha configurado una URL OJS ni un logo propio.
- El equipo editorial se administra como registros estructurados desde Filament. Los números y artículos se publican de forma independiente.
- Los perfiles docentes documentales se conservan pendientes de revisión y se administran exclusivamente desde Filament. El flujo editorial y los criterios de privacidad se describe en [Gestión del contenido docente](docs/CONTENIDO_DOCENTE.md).

Rutas editoriales públicas:

- /revista
- /revista/equipo-editorial
- /revista/normas-para-autores
- /revista/{numero}
- /revista/{numero}/{articulo}

## Archivos

Las cargas usan storage/app/public y requieren php artisan storage:link.

    MEDIA_UPLOADS_ENABLED=true
    MEDIA_MAX_IMAGE_KB=5120
    MEDIA_MAX_PDF_KB=20480

En Render, MEDIA_UPLOADS_ENABLED=false: los campos de carga quedan deshabilitados porque el disco es efímero. No se debe activar hasta disponer de un disco persistente u object storage y un procedimiento de respaldo. El PDF institucional del RCF 063 se conserva en `docs/sources/revista` y debe adjuntarse manualmente desde Filament.

La recepción pública de manuscritos usa almacenamiento privado y tres interruptores independientes. En producción deben permanecer en `false` hasta contar con privacidad aprobada y almacenamiento persistente: `SUBMISSIONS_ENABLED`, `SUBMISSIONS_PRIVACY_APPROVED` y `SUBMISSIONS_STORAGE_PERSISTENT`. En local pueden activarse junto con `SUBMISSIONS_DISK=local`. Se rechazan proveedores de correo personal configurados en `SUBMISSIONS_PERSONAL_EMAIL_DOMAINS`. Cada envío o corrección genera una notificación persistente para editores y superadministradores en Filament; el recurso **Envíos de manuscritos** muestra además un contador de casos que requieren atención.

## Saneamiento y caché

`php artisan content:purge-demo` inspecciona únicamente los identificadores demostrativos conocidos. Tras crear un respaldo con `php artisan content:backup`, agrega `--force` para eliminarlos. `php artisan content:cache:warm` precarga los datos públicos normalizados después de un despliegue o una carga editorial.

## Calidad

    php artisan test
    ./vendor/bin/pint --test
    npm run build
    composer audit
    npm audit --omit=dev

La CI ejecuta estas comprobaciones con PHP 8.4 y Node 22. El health check es /up y el sitemap público está en /sitemap.xml.
