<?php

namespace App\Providers;

use App\Repositories\Contracts\AreaLaboralRepositoryInterface;
use App\Repositories\Contracts\ArticuloRepositoryInterface;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use App\Repositories\Contracts\CompetenciaRepositoryInterface;
use App\Repositories\Contracts\CursoRepositoryInterface;
use App\Repositories\Contracts\DocenteRepositoryInterface;
use App\Repositories\Contracts\DocumentoRepositoryInterface;
use App\Repositories\Contracts\EstadisticaRepositoryInterface;
use App\Repositories\Contracts\HitoRepositoryInterface;
use App\Repositories\Contracts\ObjetivoRepositoryInterface;
use App\Repositories\Contracts\PerfilIngresoAreaRepositoryInterface;
use App\Repositories\Eloquent\AreaLaboralRepository;
use App\Repositories\Eloquent\ArticuloRepository;
use App\Repositories\Eloquent\BlogPostRepository;
use App\Repositories\Eloquent\ComunicadoRepository;
use App\Repositories\Eloquent\CompetenciaRepository;
use App\Repositories\Eloquent\CursoRepository;
use App\Repositories\Eloquent\DocenteRepository;
use App\Repositories\Eloquent\DocumentoRepository;
use App\Repositories\Eloquent\EstadisticaRepository;
use App\Repositories\Eloquent\HitoRepository;
use App\Repositories\Eloquent\ObjetivoRepository;
use App\Repositories\Eloquent\PerfilIngresoAreaRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Punto único de acoplamiento entre contratos e implementaciones.
 *
 * Para migrar de tecnología de persistencia (otro ORM, una API externa,
 * etc.) basta apuntar cada interfaz a una nueva implementación AQUÍ.
 * Ni los Services ni los controladores cambian.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Mapa interfaz => implementación.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // Fase 1 — colecciones
        ComunicadoRepositoryInterface::class => ComunicadoRepository::class,
        BlogPostRepositoryInterface::class => BlogPostRepository::class,
        ArticuloRepositoryInterface::class => ArticuloRepository::class,
        DocenteRepositoryInterface::class => DocenteRepository::class,
        EstadisticaRepositoryInterface::class => EstadisticaRepository::class,

        // Fase 2 — páginas institucionales
        HitoRepositoryInterface::class => HitoRepository::class,
        ObjetivoRepositoryInterface::class => ObjetivoRepository::class,
        CompetenciaRepositoryInterface::class => CompetenciaRepository::class,
        AreaLaboralRepositoryInterface::class => AreaLaboralRepository::class,
        PerfilIngresoAreaRepositoryInterface::class => PerfilIngresoAreaRepository::class,
        DocumentoRepositoryInterface::class => DocumentoRepository::class,

        // Fase 3 — malla curricular
        CursoRepositoryInterface::class => CursoRepository::class,
    ];
}
