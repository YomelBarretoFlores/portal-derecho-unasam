# Despliegue en Render

> **Para entregar al área de sistemas hay versiones en página web de esta guía**, con los pasos
> en orden y cada variable explicada. No se versionan aquí —se entregan por correo—, y se
> regeneran desde este documento. Este manda ante cualquier diferencia.

El servicio usa el Dockerfile, Node 22 para compilar los assets y FrankenPHP/PHP 8.4 para ejecutar Laravel.

En cada arranque, `docker/entrypoint.sh` cachea configuración, rutas y vistas, aplica las migraciones pendientes, descarta los planes de consulta que el pooler guardaba de antes, **siembra el contenido institucional**, **crea la cuenta de administrador** si están definidas sus variables, y levanta el servidor.

## Servidor propio con Docker

El repositorio trae lo necesario para instalarlo sin depender de Render:

| Archivo | Para qué |
|---|---|
| `compose.yaml` | Levanta el servicio con el volumen de archivos y la comprobación de salud |
| `docker/actualizar.sh` | Recoge los cambios de `main` y reconstruye. Pensado para `cron` cada cinco minutos |

```bash
git clone https://github.com/YomelBarretoFlores/portal-derecho-unasam.git /opt/portal-derecho
cd /opt/portal-derecho
# crear portal.env con las variables de esta guía; chmod 600
docker compose up -d --build
docker compose logs -f
```

Y para que se mantenga al día sin que nadie intervenga:

```
*/5 * * * * /opt/portal-derecho/docker/actualizar.sh >> /var/log/portal-derecho.log 2>&1
```

**No edite archivos versionados en el servidor.** `docker/actualizar.sh` sincroniza con `git reset --hard`, así que cualquier cambio hecho ahí desaparece en la siguiente actualización —y el fallo aparece horas después, sin relación aparente con la causa. Lo que necesite ajustar va en archivos que git ignora:

| Archivo | Qué configura | Quién lo lee |
|---|---|---|
| `portal.env` | La aplicación: base de datos, administrador, archivos | El contenedor |
| `.env` | El despliegue: `PORTAL_BIND`, `PORTAL_PUERTO` | Docker Compose |

Por defecto el puerto se publica solo en `127.0.0.1`, para que nadie pueda saltarse el proxy de la institución y llegar al sitio sin cifrar. Si no hay proxy delante, un `.env` con `PORTAL_BIND=0.0.0.0` lo expone a la red.

El script no abre ningún puerto ni recibe conexiones: es el servidor quien consulta GitHub. Por eso se prefiere a un *webhook*, que exigiría exponer un punto de entrada más. Solo escribe en el registro cuando hay una actualización de verdad, y comprueba que el sitio responda en `/up` antes de darla por buena.

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
| TRUSTED_PROXIES | IP o CIDR real del proxy, separadas por comas. Vacío es válido solo si no hay proxy delante. **Nunca `*`**: se ignora y queda anotado en el registro |
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

   En un portal que ya lleva tiempo funcionando la línea será `Ya hay un superadministrador; no hace falta crear ninguno`. Es lo normal: las variables `ADMIN_*` solo hacen falta la primera vez.
5. Abrir la portada y comprobar que muestra contenido: cifras, accesos, historia. **Si sale vacía, la siembra no se ejecutó**, y el registro del paso 4 dice por qué.
6. Entrar a `/admin` con la cuenta creada y cambiar la contraseña.
7. Comprobar que los campos de archivos avisan de que las cargas están deshabilitadas.
8. Comprobar que `/revista/envios` no muestra el formulario mientras los tres interruptores estén en `false`.

### Qué contenido aparece solo, y cuál no

La siembra crea la **base institucional**: textos del sitio, historia, misión y visión, objetivos, competencias, áreas laborales, perfil de ingreso, documentos normativos, organigrama, estadísticas, accesos y la ficha de la revista.

**No** crea contenido editorial: comunicados, entradas de blog, perfiles docentes, números ni artículos. Eso se carga desde `/admin`, que es su única fuente de verdad. Véase `docs/CARGA_INICIAL_CMS.md`.

La siembra se repite en cada despliegue y es inofensiva: cada bloque solo actúa si su tabla está vacía, y los ajustes solo crean las claves que falten. **Nunca pisa lo editado desde el panel.**

## Actualizar una instalación ya publicada

Para un servidor que ya está sirviendo el portal y solo necesita la versión nueva
del código. No es lo mismo que la primera publicación: aquí **la base de datos no
se toca** y el contenido cargado desde el panel no corre riesgo.

Antes de empezar, dos avisos que ahorran una tarde:

- **Si editó algún archivo del proyecto a mano en el servidor, `git pull` se va a
  parar en conflicto.** Compruébelo con `git status` antes de nada. Los parches
  hechos en la UNASAM ya están incorporados al repositorio, así que lo correcto
  es descartar la copia local (`git checkout -- <archivo>`), no conservarla.
- **`public/build` no viaja en el repositorio** (está en `.gitignore`). Sin
  `npm run build` la página se sirve con los estilos de la versión anterior.

```bash
git status                    # debe estar limpio; si no, ver el aviso de arriba
git pull

composer install --no-dev --optimize-autoloader
npm ci && npm run build

php artisan migrate --force   # sin migraciones nuevas no hace nada
php artisan storage:link      # inofensivo si el enlace ya existe

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan content:cache:warm

sudo systemctl reload php8.4-fpm   # el nombre exacto varía; ver abajo
```

Esa última línea es la que más se olvida, y la que peor engaña. **OPcache guarda
en memoria los ficheros PHP ya compilados y no vuelve a mirar el disco**, así que
sin recargar PHP-FPM el sitio sigue ejecutando el código anterior mientras
`git log` afirma que la versión nueva está puesta.

Después, comprobar: la portada carga, `/admin` deja entrar, y un archivo subido
antes del cambio sigue viéndose.

### Hacerlo con un solo comando

Todo lo anterior está en `deploy.sh`, en la raíz del proyecto:

```bash
./deploy.sh              # actualiza solo si hay algo nuevo
./deploy.sh --revisar    # dice si hay algo nuevo, sin tocar nada
./deploy.sh --forzar     # rehace el despliegue aunque no haya cambios
```

Hace las mismas órdenes en el mismo orden, y además:

- **No arranca si hay cambios sin guardar en el servidor.** Se detiene y los
  lista, en vez de reventar a mitad del `git pull` con el sitio a medio
  actualizar.
- **Se para en el primer fallo**, dejando el sitio con la versión que ya tenía.
- **Un despliegue a la vez**, con un cerrojo, para que dos ejecuciones de cron
  no se pisen.
- **Busca y recarga PHP-FPM solo**; si no lo consigue, lo dice en vez de
  callarse.
- Deja el registro en `storage/logs/despliegue.log`.

Con `DEPLOY_URL` puesta, al terminar comprueba que la portada responda 200:

```bash
DEPLOY_URL=https://derecho.unasam.edu.pe ./deploy.sh
```

### Que se actualice solo

Lo anterior sigue siendo una orden que alguien tiene que escribir. Para que el
servidor se mantenga al día sin que nadie entre, basta una línea de cron con el
usuario dueño del proyecto:

```cron
*/10 * * * * cd /ruta/al/portal && DEPLOY_URL=https://derecho.unasam.edu.pe ./deploy.sh >> storage/logs/despliegue-cron.log 2>&1
```

Cada diez minutos mira si el repositorio se movió. Si no, termina sin hacer
nada y sin escribir nada. Si se movió, despliega.

Se prefiere esto a que GitHub avise al servidor (un *webhook*, o una acción que
entre por SSH) por dos motivos concretos de esta instalación: **no hace falta
abrir ningún puerto de entrada** —el servidor está detrás de Cloudflare y solo
sale hacia fuera—, y **no hay que guardar credenciales del servidor en GitHub**,
que es un repositorio público. El precio es esperar hasta diez minutos, que para
un portal institucional no es precio.

Si más adelante hiciera falta que fuese inmediato, la pieza que hay que añadir
es una acción de GitHub con una clave SSH de despliegue y el puerto 22 abierto
para las IP de GitHub. No antes de que alguien lo pida.

**Para que `sudo systemctl reload` funcione desde cron sin contraseña**, hay que
autorizar esa orden concreta, y solo esa (`sudo visudo -f /etc/sudoers.d/portal`):

```
www-data ALL=(root) NOPASSWD: /bin/systemctl reload php8.4-fpm
```

Sin eso el despliegue funciona igual, pero avisa de que hay que recargar a mano.

## Si el sitio sale en blanco

| Síntoma | Causa probable | Comprobación |
|---|---|---|
| El sitio no responde, error 500 | La base de datos no conecta | Revisar `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` y `DB_SSLMODE` en el registro |
| Responde, pero sin contenido | No se ejecutó la siembra | Buscar `→ Contenido institucional…` en el registro del despliegue |
| No se puede entrar a `/admin` | No se creó la cuenta | Buscar `⚠ NO se creó el usuario administrador` |
| Las imágenes no se ven | El dominio no está autorizado | Revisar `CSP_IMG_HOSTS` y `AWS_URL`; en el navegador, la consola señala la cabecera CSP |
| Los campos de archivo salen en gris | Es lo esperado | `MEDIA_UPLOADS_ENABLED=false` mientras no haya almacenamiento persistente |
| 500 en todo el sitio justo tras una migración, con `cached plan must not change result type` | El pooler conserva planes de consulta de antes de migrar | El arranque ya lo limpia. Si persiste: `php artisan db:limpiar-planes --conexiones=100` |

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

## Si hay un WAF delante (ModSecurity / OWASP CRS)

El área de sistemas de la UNASAM tiene ModSecurity con el juego de reglas OWASP
CRS, y da falsos positivos sobre el panel. Conviene saberlo antes de instalar
esto detrás de un cortafuegos de aplicación.

**El síntoma.** Una acción del panel deja de responder: la carga de un archivo
falla, o el cuadro que pide confirmación para borrar no llega a abrirse. A veces
aparece una página «Forbidden — You don't have permission to access this
resource». Y cuando ocurre, el JavaScript de la página se queda a medias: el
recuadro de «arrastra tu archivo» se degrada a un campo básico que no sube nada.
Es intermitente, porque depende de lo que lleve dentro cada petición.

**La causa.** Todo el panel se comunica por una sola dirección, `POST
/livewire-xxxxxxxx/update`, y el cuerpo va en JSON con cadenas en base64. A
libinjection —el detector de inyección SQL del CRS— esas cadenas le parecen a
veces un ataque. En el registro se ve así:

```
[id "980170"] [msg "Anomaly Scores: (Inbound Scores: blocking=5, detection=5,
per_pl=5-0-0-0, threshold=5) ... (SQLI=5, XSS=0, ...)"]
[uri "/livewire-51ce454c/update"]
```

La regla 980170 solo **informa** del total; no es la que bloqueó. Los 5 puntos
los puso una regla de la familia 942xxx, y como el umbral son 5, una sola
coincidencia crítica basta para cortar la petición. Para ver cuál fue, hay que
buscar en el registro las demás líneas de esa misma transacción:

```bash
grep "aql10qvx4BmAPkAdfHSHuwAAAAo" /var/log/modsec_audit.log   # el unique_id
```

**Por qué excluir esa ruta no abre un agujero.** El endpoint no queda
desprotegido: exige una URL firmada con la `APP_KEY` (sin firma, 401), el token
CSRF (sin él, 419), sesión iniciada con permiso sobre el panel, y tiene un
límite de 60 peticiones por minuto. Las consultas a la base van por Eloquent con
parámetros ligados, así que una cadena del cuerpo no puede convertirse en SQL.
La regla estaba duplicando —peor— una protección que ya existe.

**La exclusión.** En `REQUEST-900-EXCLUSION-RULES-BEFORE-CRS.conf`, quitando
solo la regla que salte, y solo en esa ruta:

```apache
SecRule REQUEST_URI "@rx ^/livewire-[0-9a-f]{8}/(update|upload-file)" \
    "id:1000100,phase:1,pass,nolog,t:none,\
     ctl:ruleRemoveById=942100"
```

Si después salta otra regla de la misma familia, es preferible excluir la
categoría entera pero **solo en esas dos rutas**, en vez de desactivarla en todo
el sitio:

```apache
SecRule REQUEST_URI "@rx ^/livewire-[0-9a-f]{8}/(update|upload-file)" \
    "id:1000101,phase:1,pass,nolog,t:none,\
     ctl:ruleRemoveByTag=attack-sqli"
```

**El detalle que rompe esto meses después.** Ese `livewire-xxxxxxxx` no es fijo:
sale de la `APP_KEY`, en concreto de los ocho primeros caracteres de
`sha256(APP_KEY.'livewire-endpoint')`. Si algún día se regenera la clave, la
ruta cambia y una exclusión escrita con el valor literal deja de aplicarse, sin
avisar: el panel vuelve a fallar a ratos y nada apunta al WAF. Por eso los
ejemplos de arriba usan `[0-9a-f]{8}` y no el valor concreto.

Para ver las rutas reales de una instalación:

```bash
php artisan route:list --path=livewire
```

## Recuperación administrativa

No hay recuperación por correo mientras no exista SMTP. Si queda otro
superadministrador con acceso, lo más rápido es que restablezca la contraseña
desde el panel.

Cuando no queda ninguno —la cuenta existe y nadie recuerda su contraseña—, el
panel se cierra para siempre con el contenido dentro. **Cambiar `ADMIN_PASSWORD`
en el `.env` y volver a desplegar no lo arregla**: la siembra crea la cuenta
inicial una sola vez y, si el usuario ya existe, no le toca la contraseña.

Para eso está este comando, en el servidor:

```bash
php artisan admin:restablecer correo@de-la-cuenta
```

Pide la contraseña por teclado, dos veces, y no la muestra. Si la cuenta no
existe, la crea; si existe, le cambia la contraseña y se asegura de dejarla como
superadministrador, porque una cuenta sin rol pasa el login y después no ve
nada, que desde fuera parece que el panel está roto. Cuando el correo no
coincide con ninguna cuenta, lista las que hay antes de crear nada.

La contraseña **no se acepta como argumento** a propósito: un argumento queda
escrito en el historial del intérprete de órdenes y, en muchos servidores, es
visible en la lista de procesos para cualquier otro usuario mientras el comando
corre. Nunca se deben introducir contraseñas en registros ni en commits.
