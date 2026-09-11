<?php

namespace Tests\Feature;

use App\Filament\Pages\AjustesSitio;
use App\Http\Controllers\HomeController;
use App\Models\Comunicado;
use App\Models\Setting;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Nada de lo que se ve en el sitio puede quedar fuera del panel.
 *
 * La comprobación a mano no sirve: se hace una vez, sale bien, y seis meses
 * después alguien añade un modelo y nadie vuelve a mirarlo. Estos tests
 * recorren el inventario entero en cada ejecución.
 *
 * Van en los dos sentidos a propósito. Falta algo en el panel: el contenido
 * existe en el sitio y no hay forma de cambiarlo. Sobra algo en el panel: se
 * edita un campo, se guarda sin queja, y no aparece en ninguna página —que es
 * peor, porque parece que funciona.
 */
class PanelCompletoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Modelos que no son contenido público y por eso no llevan recurso propio
     * ni entran en la invalidación de la caché del sitio.
     *
     * @var array<int, string>
     */
    private const NO_SON_CONTENIDO = [
        'Setting',              // se edita en la página «Ajustes del sitio»
        'User',                 // cuentas, no contenido
        'ContentAudit',         // registro de cambios, solo lectura
        'RevistaEnvio',         // manuscritos recibidos, no se publican
        'RevistaEnvioVersion',
    ];

    /** @return array<int, string> */
    private function modelos(): array
    {
        return collect(File::files(app_path('Models')))
            ->map(fn ($f): string => $f->getFilenameWithoutExtension())
            ->sort()->values()->all();
    }

    /** @return array<int, string> */
    private function modelosDeContenido(): array
    {
        $constante = (new ReflectionClass(AppServiceProvider::class))->getConstant('CONTENT_MODELS');

        return collect($constante)->map(fn (string $c): string => class_basename($c))->sort()->values()->all();
    }

    public function test_every_content_model_invalidates_the_public_cache(): void
    {
        // Un modelo fuera de CONTENT_MODELS se puede editar en el panel y el
        // sitio sigue enseñando lo anterior hasta que la caché caduque sola.
        // El administrador ve su cambio guardado y no lo ve publicado.
        $esperados = collect($this->modelos())
            ->reject(fn (string $m): bool => in_array($m, self::NO_SON_CONTENIDO, true))
            ->values()->all();

        $this->assertSame(
            $esperados,
            $this->modelosDeContenido(),
            'CONTENT_MODELS no cuadra con app/Models. Un modelo que falte ahí guarda bien y no se publica.',
        );
    }

    public function test_every_content_model_can_be_edited_in_the_panel(): void
    {
        $sinRecurso = [];

        foreach ($this->modelosDeContenido() as $modelo) {
            $plural = File::directories(app_path('Filament/Resources'));

            $existe = collect($plural)->contains(function (string $dir) use ($modelo): bool {
                return File::exists($dir.'/'.$modelo.'Resource.php');
            });

            if (! $existe) {
                $sinRecurso[] = $modelo;
            }
        }

        $this->assertSame([], $sinRecurso, 'Modelos de contenido sin recurso en el panel: '.implode(', ', $sinRecurso));
    }

    /**
     * Claves de «settings» que alguna página lee de verdad.
     *
     * @return array<int, string>
     */
    private function clavesUsadas(): array
    {
        // Los dos mapas con valores por defecto. Se leen por reflexión y no
        // desde los datos compartidos de la vista, que están vacíos mientras
        // no se haya renderizado ninguna página: eso daría por huérfanas las
        // once claves del pie y del SEO, que sí se usan.
        $globales = array_keys($this->invocar(AppServiceProvider::class, 'ajustesGlobales'));
        $portada = array_keys($this->invocar(HomeController::class, 'textosInicio'));

        // Y las que se piden sueltas con Setting::get('...').
        $fuentes = collect([
            ...File::allFiles(app_path('Http/Controllers')),
            ...File::allFiles(resource_path('views')),
        ])->map(fn ($f): string => $f->getContents())->implode("\n");

        preg_match_all("/Setting::get\('([a-z0-9_]+)'/", $fuentes, $coincidencias);

        return collect([...$globales, ...$portada, ...$coincidencias[1]])->unique()->sort()->values()->all();
    }

    /**
     * Invoca un método privado que devuelve un mapa de ajustes.
     *
     * @return array<string, mixed>
     */
    private function invocar(string $clase, string $metodo): array
    {
        $reflexion = new ReflectionMethod($clase, $metodo);
        $reflexion->setAccessible(true);

        $instancia = $clase === AppServiceProvider::class
            ? new AppServiceProvider($this->app)
            : new $clase;

        return (array) $reflexion->invoke($instancia);
    }

    /** @return array<int, string> */
    private function clavesEditables(): array
    {
        $metodo = new ReflectionMethod(AjustesSitio::class, 'claves');
        $metodo->setAccessible(true);

        return collect($metodo->invoke(new AjustesSitio))->unique()->sort()->values()->all();
    }

    public function test_every_text_shown_on_the_site_can_be_edited(): void
    {
        $huerfanas = array_values(array_diff($this->clavesUsadas(), $this->clavesEditables()));

        $this->assertSame(
            [],
            $huerfanas,
            'Textos que salen en el sitio y no se pueden cambiar desde «Ajustes del sitio»: '.implode(', ', $huerfanas),
        );
    }

    public function test_no_editable_text_disappears_into_the_void(): void
    {
        $inservibles = array_values(array_diff($this->clavesEditables(), $this->clavesUsadas()));

        $this->assertSame(
            [],
            $inservibles,
            'Campos del panel que no se muestran en ninguna página: '.implode(', ', $inservibles)
            .'. Se editan, se guardan, y no cambian nada.',
        );
    }

    public function test_editing_content_reaches_the_site_without_waiting_for_the_cache(): void
    {
        // El caso que más desconcierta a quien administra: guarda el cambio,
        // el panel dice que sí, abre la página y sigue viendo lo de antes.
        // Aquí se pide la página ANTES de editar para dejar la caché caliente,
        // que es la situación en la que el fallo aparece.
        $comunicado = Comunicado::query()->create([
            'titulo' => 'Título original',
            'slug' => 'titulo-original',
            'resumen' => 'Resumen.',
            'contenido' => '<p>Cuerpo.</p>',
            'publicado' => true,
            'fecha_publicacion' => now()->subDay(),
            'estado_editorial' => 'published',
        ]);

        $this->get(route('comunicados'))->assertOk()->assertSee('Título original');

        $comunicado->update(['titulo' => 'Título corregido']);

        $this->get(route('comunicados'))->assertOk()
            ->assertSee('Título corregido')
            ->assertDontSee('Título original');
    }

    public function test_the_settings_page_saves_and_the_site_shows_it(): void
    {
        // El circuito completo, sin confiar en la inspección estática.
        Setting::query()->updateOrCreate(['clave' => 'lema'], ['valor' => 'Lema comprobado por el test']);

        $this->get('/')->assertOk()->assertSee('Lema comprobado por el test');
    }
}
