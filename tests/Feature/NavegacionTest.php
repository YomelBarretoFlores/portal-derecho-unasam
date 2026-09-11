<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as RutaLaravel;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * La navegación es la única forma de llegar al contenido sin conocer la URL.
 *
 * Al reagrupar el menú por audiencia aparecieron dos páginas que llevaban tiempo
 * publicadas pero fuera de todo menú —el organigrama y el plan de estudios 2019—:
 * existían, respondían 200 y estaban en el sitemap, pero ningún visitante podía
 * alcanzarlas navegando. Este test impide que vuelva a ocurrir.
 */
class NavegacionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Páginas públicas sin parámetros que deben ser alcanzables desde la
     * navegación. Se excluyen las de detalle (necesitan un registro), las de la
     * revista —que tiene su propia barra de secciones— y las utilitarias.
     *
     * @return array<int, string>
     */
    private function paginasNavegables(): array
    {
        return collect(Route::getRoutes()->getRoutesByMethod()['GET'] ?? [])
            ->filter(fn (RutaLaravel $r): bool => $r->getName() !== null)
            ->reject(fn (RutaLaravel $r): bool => str_starts_with((string) $r->getName(), 'revista'))
            ->reject(fn (RutaLaravel $r): bool => str_starts_with((string) $r->getName(), 'filament.'))
            ->reject(fn (RutaLaravel $r): bool => str_starts_with((string) $r->getName(), 'preview.'))
            ->reject(fn (RutaLaravel $r): bool => in_array($r->getName(), ['home', 'sitemap', 'storage.local'], true))
            // Las rutas con parámetro obligatorio son páginas de detalle; se llega
            // a ellas desde su índice, no desde el menú. «estadisticas» lleva
            // parámetro pero es un índice con pestañas, así que sí debe estar.
            ->filter(fn (RutaLaravel $r): bool => $r->getName() === 'estadisticas'
                || ! str_contains($r->uri(), '{'))
            ->map(fn (RutaLaravel $r): string => (string) $r->getName())
            ->unique()->values()->all();
    }

    public function test_every_public_page_is_reachable_from_the_navigation(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        $huerfanas = collect($this->paginasNavegables())
            ->reject(function (string $nombre) use ($html): bool {
                $url = $nombre === 'estadisticas' ? route($nombre, 'matriculados') : route($nombre);

                return str_contains($html, 'href="'.$url.'"');
            })
            ->all();

        $this->assertSame([], $huerfanas,
            'Páginas publicadas a las que no se llega navegando: '.implode(', ', $huerfanas));
    }

    public function test_the_navigation_no_longer_has_a_catch_all_group(): void
    {
        // «Más» era el grupo donde caía todo lo que no encajaba en el organigrama.
        // Un menú que necesita un cajón de sastre es un menú mal agrupado.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('>Más<', $html);
        foreach (['La Facultad', 'Estudiantes', 'Investigación'] as $grupo) {
            $this->assertStringContainsString($grupo, $html, "Falta el grupo «{$grupo}» en la navegación.");
        }
    }
}
