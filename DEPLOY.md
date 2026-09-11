# Despliegue en Render

El servicio usa el Dockerfile, Node 22 para compilar los assets y FrankenPHP/PHP 8.4 para ejecutar Laravel.

En cada arranque, `docker/entrypoint.sh` cachea configuración, rutas y vistas, aplica las migraciones pendientes, **siembra el contenido institucional**, **crea la cuenta de administrador** si están definidas sus variables, y levanta el servidor.

## Lo que hay que preparar ANTES de desplegar

El portal no crea su propia base de datos ni se inventa una contraseña de administrador. Sin estos dos pasos previos el despliegue termina sin errores y el sitio sale en blanco.

### 1. Una base de datos PostgreSQL

Versión 14 o superior. Sirve cualquiera: Neon, Supabase, Render PostgreSQL o un servidor de la universidad. Hay que crearla vacía y anotar cinco datos: servidor, puerto, nombre, usuario y contraseña.

No hace falta crear ninguna tabla ni importar ningún volcado: las tablas las crean las migraciones en el primer arranque.

Si el proveedor exige TLS —Neon lo exige—, `DB_SSLMODE=require`.

### 2. La contraseña del primer administrador

La elige quien despliega y se pasa en `ADMIN_PASSWORD`. Debe tener **12 caracteres como mínimo, con mayúscula, minúscula, número y símbolo**; si no los cumple, la cuenta no se crea. Conviene cambiarla desde el panel después del primer acceso, porque queda guardada como variable de entorno.

## Variables obligatorias

| Variable | Descripción |
|---|---|
| APP_KEY | Salida de `php artisan key:generate --show` |
| APP_URL | URL HTTPS pública del sitio |
| DB_CONNECTION | `pgsql` |
| DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD | Datos de la base PostgreSQL creada arriba |
| DB_SSLMODE | `require` si el proveedor exige TLS |
| DB_PERSISTENT | `false` en producción |
| ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD | Cuenta de administrador inicial |
| TRUSTED_PROXIES | IP o CIDR real del proxy, separadas por comas. **Nunca `*`**: Laravel lo rechaza en producción |
| SESSION_SECURE_COOKIE | `true` |

### Variables de archivos

| Variable | Valor | Descripción |
|---|---|---|
| MEDIA_DISK | `public` o `medios` | Disco del servidor, o proveedor compatible con S3 |
| MEDIA_UPLOADS_ENABLED | `false` | Ponerlo en `true` solo con almacenamiento que sobreviva a un despliegue |
| CSP_IMG_HOSTS | vacío | Dominios externos autorizados a servir imágenes, separados por comas |
| SUBMISSIONS_DISK | `local` o `manuscritos` | Disco **privado**. `public` lo rechaza la aplicación |
| SUBMISSIONS_ENABLED | `false` | Recepción pública de manuscritos |
| SUBMISSIONS_PRIVACY_APPROVED | `false` | Hasta que la declaración de privacidad esté aprobada |
| SUBMISSIONS_STORAGE_PERSISTENT | `false` | Declaración manual de que el disco persiste |

Los tres interruptores de manuscritos se ponen en `true` a la vez, y solo después de configurar un disco privado persistente y comprobar una carga y una descarga autenticadas. Detalle en «Almacenamiento», más abajo.

## Primera publicación

1. Crear la base de datos PostgreSQL vacía.
2. Rellenar las variables del Blueprint `render.yaml`, incluidas las tres `ADMIN_*`.
3. Desplegar y comprobar que `/up` responde.
4. **Leer el registro del despliegue.** Tienen que aparecer estas tres líneas:
   - `→ Contenido institucional…`
   - `→ Usuario administrador…`
   - `Usuario superadministrador creado: …` (o `El usuario admin ya existe`)

   Si en su lugar sale `⚠ NO se creó el usuario administrador`, faltan las variables `ADMIN_*` o la contraseña no cumple los requisitos. El sitio público funciona igual; `/admin` no tendrá con qué entrar.
5. Abrir la portada y comprobar que muestra contenido: cifras, accesos, historia. **Si sale vacía, la siembra no se ejecutó**, y el registro del paso 4 dice por qué.
6. Entrar a `/admin` con la cuenta creada y cambiar la contraseña.
7. Comprobar que los campos de archivos avisan de que las cargas están deshabilitadas.
8. Comprobar que `/revista/envios` no muestra el formulario mientras los tres interruptores estén en `false`.

### Qué contenido aparece solo, y cuál no

La siembra crea la **base institucional**: textos del sitio, historia, misión y visión, objetivos, competencias, áreas laborales, perfil de ingreso, documentos normativos, organigrama, estadísticas, accesos y la ficha de la revista.

**No** crea contenido editorial: comunicados, entradas de blog, perfiles docentes, números ni artículos. Eso se carga desde `/admin`, que es su única fuente de verdad. Véase `docs/CARGA_INICIAL_CMS.md`.

La siembra se repite en cada despliegue y es inofensiva: cada bloque solo actúa si su tabla está vacía, y los ajustes solo crean las claves que falten. **Nunca pisa lo editado desde el panel.**

## Si el sitio sale en blanco

| Síntoma | Causa probable | Comprobación |
|---|---|---|
| El sitio no responde, error 500 | La base de datos no conecta | Revisar `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` y `DB_SSLMODE` en el registro |
| Responde, pero sin contenido | No se ejecutó la siembra | Buscar `→ Contenido institucional…` en el registro del despliegue |
| No se puede entrar a `/admin` | No se creó la cuenta | Buscar `⚠ NO se creó el usuario administrador` |
| Las imágenes no se ven | El dominio no está autorizado | Revisar `CSP_IMG_HOSTS` y `AWS_URL`; en el navegador, la consola señala la cabecera CSP |
| Los campos de archivo salen en gris | Es lo esperado | `MEDIA_UPLOADS_ENABLED=false` mientras no haya almacenamiento persistente |

Para comprobar el almacenamiento desde el servidor:

```bash
php artisan almacenamiento:verificar
```

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
