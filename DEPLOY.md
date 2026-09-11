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

Para activar la recepción, configura un disco privado persistente —por ejemplo S3 o un servicio compatible—, verifica una carga y descarga autenticada, y recién entonces establece los tres interruptores en `true`. `SUBMISSIONS_DISK=public` es rechazado por la aplicación.

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
10. Al habilitarlo en una infraestructura persistente, enviar un manuscrito de prueba y comprobar la campana de notificaciones y el contador de **Envíos de manuscritos** en Filament.

## Archivos y copias de seguridad

El filesystem de Render es efímero. En esta fase:

- Los textos y metadatos sí se pueden editar.
- No se permiten nuevas cargas desde Filament.
- No se garantiza la permanencia de archivos escritos durante la ejecución.

Antes de habilitar cargas en producción se debe conectar almacenamiento persistente, migrar los medios existentes, validar URLs y establecer copias de seguridad/restauración.

Los envíos de manuscritos se habilitan únicamente cuando las tres variables de seguridad (`SUBMISSIONS_ENABLED`, `SUBMISSIONS_PRIVACY_APPROVED` y `SUBMISSIONS_STORAGE_PERSISTENT`) están en `true`. Sus archivos no deben usar el disco público.

## Almacenamiento: especificación para el área de infraestructura (ETI)

El portal usa **dos almacenamientos distintos, con requisitos opuestos**. No basta con "un disco": hay que atender a los dos.

| Uso | Disco | Ruta por defecto | Requisito |
|---|---|---|---|
| Medios públicos: portadas y PDF de números, logo de la revista, PDF de artículos, fotos de docentes, adjuntos de avisos y documentos | `MEDIA_DISK`: `public` o `medios` | `storage/app/public` | Persistente **y servido por web** |
| Manuscritos recibidos de autores | `SUBMISSIONS_DISK`: `local` o `manuscritos` | `storage/app/private` | Persistente y **nunca alcanzable por web** |

Cada variable admite dos valores según la infraestructura: el primero usa el disco del propio servidor (escenario A), el segundo un proveedor de objetos (escenario B). **Los dos funcionan sin cambiar código.**

Los manuscritos contienen datos personales (nombre, documento de identidad, WhatsApp, afiliación). Deben descargarse solo a través de `/admin/revista-envios/...`, que exige sesión autenticada y rol editor. La aplicación **rechaza** `SUBMISSIONS_DISK=public` y cualquier disco cuya raíz caiga dentro de `public/`.

### Escenario A — Servidor propio o VPS con disco

El escenario más simple: no hace falta object storage. Basta con que `storage/` viva en un volumen persistente y sobreviva a los despliegues.

```
MEDIA_UPLOADS_ENABLED=true
SUBMISSIONS_DISK=local            # storage/app/private, fuera de la raíz web
SUBMISSIONS_ENABLED=true
SUBMISSIONS_PRIVACY_APPROVED=true # solo cuando la declaración esté aprobada
SUBMISSIONS_STORAGE_PERSISTENT=true
```

Requisitos en el servidor:

1. `storage/` en un volumen persistente, escribible por el usuario del proceso web.
2. Ejecutar `php artisan storage:link` (crea `public/storage`). Sin ese enlace los medios se guardan pero devuelven 404.
3. La raíz web debe apuntar a `public/`, **nunca** al directorio del proyecto: si `storage/` queda accesible por URL, los manuscritos quedan expuestos.
4. Copia de seguridad periódica de `storage/app/public` y `storage/app/private`.

### Escenario B — Object storage (S3 o compatible)

Vale cualquier proveedor compatible con S3: Cloudflare R2, el almacenamiento de objetos de Neon, MinIO, AWS S3 o un servidor propio. Lo único que cambia entre ellos es `AWS_ENDPOINT`. La librería necesaria (`league/flysystem-aws-s3-v3`) ya está instalada.

Los dos discos ya están definidos en `config/filesystems.php` — `medios` y `manuscritos` — y **no hay que tocar código**: solo rellenar variables.

```
# Bucket de medios: con lectura pública
MEDIA_DISK=medios
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
AWS_ENDPOINT=...                  # vacío en AWS S3; obligatorio en el resto
AWS_URL=...                       # URL pública de lectura del bucket
MEDIA_UPLOADS_ENABLED=true

# Bucket de manuscritos: SIN acceso público, y distinto del anterior
SUBMISSIONS_DISK=manuscritos
AWS_SUBMISSIONS_BUCKET=...
SUBMISSIONS_ENABLED=true
SUBMISSIONS_PRIVACY_APPROVED=true
SUBMISSIONS_STORAGE_PERSISTENT=true
```

Las credenciales del bucket de manuscritos caen a las `AWS_*` de arriba si se dejan vacías. Si su proveedor permite emitir credenciales separadas por bucket, use `AWS_SUBMISSIONS_ACCESS_KEY_ID` y `AWS_SUBMISSIONS_SECRET_ACCESS_KEY`: así una filtración de la clave pública de medios no alcanza a los manuscritos.

**`AWS_URL` no es opcional.** Además de formar los enlaces, alimenta la cabecera `Content-Security-Policy`. Sin ella las imágenes se guardan correctamente en el bucket y aun así el navegador se niega a mostrarlas, sin error visible en la página.

### Imágenes alojadas fuera del portal

La cabecera `Content-Security-Policy` declara `img-src 'self'`: el navegador **bloquea toda imagen servida desde otro dominio**. Es deliberado, y es lo que impide que alguien cuele contenido ajeno en las páginas del portal.

El dominio del bucket de medios entra solo, deducido de `AWS_URL`. Para cualquier otro —el repositorio institucional, por ejemplo— hay que declararlo:

```
CSP_IMG_HOSTS=repositorio.unasam.edu.pe,otro.dominio.edu.pe
```

Nunca `*`: el portal lo descarta, igual que descarta los orígenes sin cifrar.

Cuando se pega en el panel la URL de una portada alojada en un dominio no autorizado, **el formulario la rechaza en el momento** y dice qué dominios admite. Sin esa comprobación el registro se guardaba sin queja y la portada salía como un hueco en la página, sin nada que apuntara a la causa.

Deben ser **dos buckets distintos**: el de medios con lectura pública, el de manuscritos con acceso denegado por completo. Compartir un bucket público para los dos expondría los manuscritos. El portal rechaza `SUBMISSIONS_DISK=medios` por ese motivo.

### Verificación

En el servidor ya desplegado:

```bash
php artisan almacenamiento:verificar
```

Comprueba, para cada disco: que existe, su driver y raíz, si es alcanzable por web, que el enlace `public/storage` está presente, y que se puede escribir, leer y borrar de verdad. Además explica, si la recepción está cerrada, **qué interruptor concreto falta**.

**Lo que el comando no puede comprobar:** la persistencia entre despliegues. Un contenedor sin volumen montado supera todas las pruebas y aun así pierde los archivos en el siguiente despliegue. Esa confirmación es responsabilidad de quien administra la infraestructura, y es la razón de que `SUBMISSIONS_STORAGE_PERSISTENT` sea una declaración manual y no una detección automática.

Recomendación: antes de poner los interruptores en `true`, subir un archivo de prueba, **forzar un redespliegue** y comprobar que el archivo sigue ahí.

## Recuperación administrativa

No hay recuperación por correo mientras no exista SMTP. Si se pierde el acceso, otro superadministrador puede restablecer la contraseña; nunca se deben introducir contraseñas en logs o commits.
