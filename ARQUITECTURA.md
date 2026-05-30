# Portal Derecho UNASAM — Guía de arquitectura

Stack: **Laravel 13 · Filament 5 (CMS) · Livewire · Blade + Tailwind v4 · Spatie Media Library · NeonDB (Postgres)**

## Cómo correr el proyecto

```bash
# 1. Backend
php artisan serve

# 2. Frontend (en otra terminal) — compila Tailwind y recarga en vivo
npm run dev
```

- Sitio público: http://localhost:8000
- Panel de administración (CMS): http://localhost:8000/admin
  - Usuario: `barretofloresyomeljair@gmail.com` · Contraseña temporal: `cambiar123` (cámbiala)

## Arquitectura por capas (clave para migrar de tecnología)

```
Controllers / Filament   → reciben la petición, NO tienen lógica
        ↓
Services                 → lógica de negocio. NO conocen Eloquent ni SQL
        ↓
Repositories\Contracts   → interfaces (el "qué")
        ↓
Repositories\Eloquent    → implementación (el "cómo"). ÚNICA capa que toca la BD
        ↓
Models (Eloquent)        → compartidos con Filament
```

**Regla de oro:** nada fuera de `app/Repositories/Eloquent/` puede importar Eloquent o escribir SQL.
Para cambiar de BD/ORM se reescribe solo esa carpeta y se reapunta el binding en
`app/Providers/RepositoryServiceProvider.php`. Cambiar solo de proveedor Postgres = editar `.env`.

## Cómo agregar una nueva entidad (ej. Docente)

Replica el patrón de **Comunicado**, que ya está implementado en todas las capas:

1. `php artisan make:model Docente -m` → define la migración y `php artisan migrate`
2. `app/Repositories/Contracts/DocenteRepositoryInterface.php` (extiende `RepositoryInterface`)
3. `app/Repositories/Eloquent/DocenteRepository.php` (extiende `BaseRepository`)
4. Registra el binding en `RepositoryServiceProvider::$bindings`
5. `app/Services/DocenteService.php` (inyecta la interfaz, no la implementación)
6. `php artisan make:filament-resource Docente --generate` → CRUD en el admin
7. Controlador + ruta + vista Blade que consuman el Service

## Archivos de referencia (entidad Comunicado)

| Capa | Archivo |
|---|---|
| Modelo | `app/Models/Comunicado.php` |
| Contrato | `app/Repositories/Contracts/ComunicadoRepositoryInterface.php` |
| Repositorio | `app/Repositories/Eloquent/ComunicadoRepository.php` |
| Servicio | `app/Services/ComunicadoService.php` |
| Binding | `app/Providers/RepositoryServiceProvider.php` |
| CMS | `app/Filament/Resources/Comunicados/` |
| Público | `app/Http/Controllers/HomeController.php` + `resources/views/home.blade.php` |

Los prototipos de diseño (React/JSX, solo referencia visual) están en `../design_handoff/` y `../Web Derecho UI/`.
