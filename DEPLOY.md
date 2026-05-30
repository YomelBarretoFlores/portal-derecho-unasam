# Despliegue en Render

El portal se despliega como **servicio web Docker** en Render usando el `Dockerfile`
(multi-etapa: Node compila los assets, FrankenPHP sirve la app) y el Blueprint `render.yaml`.

## Pasos

1. **Crear el servicio**
   - Render Dashboard → **New → Blueprint** → seleccionar este repositorio.
   - Render detecta `render.yaml` y crea el servicio `portal-derecho` (región Virginia, plan Starter).

2. **Completar las variables marcadas `sync: false`** (en *Environment*):
   | Variable | Valor |
   |---|---|
   | `APP_KEY` | salida de `php artisan key:generate --show` (incluye el prefijo `base64:`) |
   | `APP_URL` | la URL pública de Render (ej. `https://portal-derecho.onrender.com`) |
   | `DB_HOST` | endpoint **-pooler** de Neon |
   | `DB_DATABASE` | `neondb` |
   | `DB_USERNAME` | usuario de Neon |
   | `DB_PASSWORD` | contraseña de Neon |

3. **Deploy.** Render construye la imagen y arranca. El `entrypoint` cachea config/rutas/vistas,
   corre `migrate --force` y `storage:link`, y levanta FrankenPHP en `$PORT`.

4. Cada `git push` a la rama vuelve a desplegar automáticamente (`autoDeploy: true`).

## Persistencia de imágenes del CMS (importante)

El disco de Render es **efímero**: las imágenes que el coordinador suba se perderían en cada
redeploy. Dos soluciones:

- **Disco persistente de Render**: añadir un *Disk* montado en `/app/storage/app/public`.
- **Object storage** (recomendado a futuro): mover los medios a Cloudflare R2 / S3
  (config del disco `s3` en `config/filesystems.php` + credenciales en variables de entorno).

## Notas

- La región **Virginia** es la más cercana a Neon (São Paulo). Para mínima latencia de BD,
  considerar crear el proyecto Neon en `us-east` o usar Postgres de Render en Virginia.
- Crear el usuario admin de Filament tras el primer deploy:
  `php artisan make:filament-user` (vía *Shell* de Render).
