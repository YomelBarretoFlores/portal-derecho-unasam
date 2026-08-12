<?php

namespace App\Providers;

use App\Models\Acceso;
use App\Models\AreaLaboral;
use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Competencia;
use App\Models\Comunicado;
use App\Models\ContentAudit;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Documento;
use App\Models\Estadistica;
use App\Models\Hito;
use App\Models\Objetivo;
use App\Models\Organigrama;
use App\Models\PerfilIngresoArea;
use App\Models\Revista;
use App\Models\RevistaMiembro;
use App\Models\RevistaNumero;
use App\Models\Setting;
use App\Models\User;
use App\Policies\ContentAuditPolicy;
use App\Policies\ContentPolicy;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Modelos de contenido cuya edición debe invalidar la caché pública.
     *
     * @var array<int, class-string<Model>>
     */
    private const CONTENT_MODELS = [
        Acceso::class,
        BlogPost::class,
        Articulo::class,
        Docente::class,
        Estadistica::class,
        Comunicado::class,
        Hito::class,
        Objetivo::class,
        Competencia::class,
        AreaLaboral::class,
        PerfilIngresoArea::class,
        Documento::class,
        Curso::class,
        Organigrama::class,
        Revista::class,
        RevistaMiembro::class,
        RevistaNumero::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        $contentPolicyModels = array_merge(self::CONTENT_MODELS, [Setting::class]);
        foreach ($contentPolicyModels as $model) {
            Gate::policy($model, ContentPolicy::class);
        }
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ContentAudit::class, ContentAuditPolicy::class);

        // Comparte los ajustes globales (footer, SEO, contacto, lema) con el layout,
        // el footer y la nav. Setting::map() está cacheado: 0 consultas a Neon por
        // petición salvo cuando la caché está fría.
        View::composer(['layouts.app', 'components.footer', 'components.nav'], function ($view) {
            $view->with('ajustes', $this->ajustesGlobales());
        });

        // Cualquier cambio de contenido en el panel invalida la caché pública:
        // se incrementa la "versión de contenido" que forma parte de la clave
        // de la portada normalizada (ver PublicContentCache).
        $invalidar = static function (): void {
            Cache::add('content.version', 1); // inicializa si falta
            Cache::increment('content.version');
        };

        foreach (self::CONTENT_MODELS as $modelo) {
            $modelo::saved($invalidar);
            $modelo::deleted($invalidar);
            $this->registrarAuditoria($modelo);
        }

        Setting::saved($invalidar);
        Setting::deleted($invalidar);
        $this->registrarAuditoria(Setting::class);
        $this->registrarAuditoria(User::class);
        $this->registrarAuditoria(Media::class);

        // Las subidas/borrados de archivos (Spatie Media) también invalidan.
        Media::saved($invalidar);
        Media::deleted($invalidar);
    }

    /** @param class-string<Model> $model */
    private function registrarAuditoria(string $model): void
    {
        $persist = static function (Model $record, string $event): void {
            if (app()->runningInConsole() && ! app()->environment('testing')) {
                return;
            }

            if (! Schema::hasTable('content_audits')) {
                return;
            }

            $excluded = ['password', 'remember_token', 'app_authentication_secret', 'app_authentication_recovery_codes', 'updated_at'];
            $newValues = $event === 'deleted' ? [] : $record->getAttributes();
            $oldValues = $event === 'created' ? [] : $record->getOriginal();

            if ($event === 'updated') {
                $changed = array_keys($record->getChanges());
                $newValues = array_intersect_key($newValues, array_flip($changed));
                $oldValues = array_intersect_key($oldValues, array_flip($changed));
            }

            $newValues = array_diff_key($newValues, array_flip($excluded));
            $oldValues = array_diff_key($oldValues, array_flip($excluded));

            ContentAudit::query()->create([
                'user_id' => auth()->id(),
                'auditable_type' => $record::class,
                'auditable_id' => $record->getKey(),
                'event' => $event,
                'old_values' => $oldValues ?: null,
                'new_values' => $newValues ?: null,
                'ip_address' => app()->bound('request') ? request()->ip() : null,
                'created_at' => now(),
            ]);
        };

        $model::created(fn (Model $record) => $persist($record, 'created'));
        $model::updated(fn (Model $record) => $persist($record, 'updated'));
        $model::deleted(fn (Model $record) => $persist($record, 'deleted'));
    }

    /**
     * Ajustes globales con valores por defecto razonables (desde la caché).
     *
     * @return array<string, string>
     */
    private function ajustesGlobales(): array
    {
        $defaults = [
            'seo_title' => 'Derecho y Ciencias Políticas — UNASAM',
            'seo_description' => 'Programa de Estudios de Derecho y Ciencias Políticas de la Universidad Nacional Santiago Antúnez de Mayolo — Huaraz, Áncash, Perú.',
            'footer_marca' => 'Derecho y Ciencias Políticas',
            'footer_descripcion' => 'Programa de Estudios de la Universidad Nacional Santiago Antúnez de Mayolo.',
            'contacto_direccion' => 'Ciudad Universitaria, Huaraz, Áncash',
            'contacto_telefono' => '(043) 640-020',
            'contacto_email' => 'mesadepartesdigital@unasam.edu.pe',
            'footer_cta_texto' => 'Portal UNASAM',
            'footer_cta_url' => 'https://unasam.edu.pe',
            'lema' => 'Orabunt Causas Melius',
            'home_marquee' => 'Derecho Civil · Derecho Penal · Derecho Constitucional · Derecho Laboral · Derecho Administrativo · Derecho Procesal · Derecho Internacional · Derecho Comercial',
        ];

        try {
            $guardados = Setting::map();
        } catch (\Throwable $e) {
            $guardados = []; // p. ej. antes de migrar
        }

        $resultado = [];
        foreach ($defaults as $clave => $default) {
            $valor = $guardados[$clave] ?? null;
            $resultado[$clave] = ($valor === null || $valor === '') ? $default : $valor;
        }

        return $resultado;
    }
}
