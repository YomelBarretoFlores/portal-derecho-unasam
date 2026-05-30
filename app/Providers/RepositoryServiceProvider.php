<?php

namespace App\Providers;

use App\Repositories\Contracts\ComunicadoRepositoryInterface;
use App\Repositories\Eloquent\ComunicadoRepository;
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
        ComunicadoRepositoryInterface::class => ComunicadoRepository::class,
    ];
}
