<?php

namespace App\Services;

use App\Models\Competencia;
use Illuminate\Support\Collection;

class CompetenciaService
{
    /**
     * Estructura por grupo (Generales/Específicas) y plan, compatible con la vista:
     * [['grupo','prefijo','planes'=>[['titulo','destacado','items'=>[['nombre','texto'],...]]]]].
     */
    /**
     * Devuelve arrays planos, nunca colecciones anidadas.
     *
     * PublicContentCache guarda este resultado en la caché. Con el almacén de
     * ficheros —o cualquiera que serialice— una Collection anidada volvía como
     * __PHP_Incomplete_Class, y al recorrerla la vista recibía la cadena
     * «Illuminate\Support\Collection» donde esperaba un array. Efecto: la
     * página de competencias respondía 200 la primera vez tras vaciar la caché
     * y 500 en todas las siguientes, hasta el próximo vaciado.
     */
    public function grupos(): Collection
    {
        return Competencia::query()->orderBy('orden')->get()
            ->groupBy('grupo')
            ->map(fn ($filas, $grupo) => [
                'grupo' => 'Competencias '.$grupo,
                'prefijo' => str_starts_with($grupo, 'Espec') ? 'CE' : 'CG',
                'planes' => $filas->groupBy('plan')
                    ->map(fn ($items, $plan) => [
                        'titulo' => $plan,
                        // Cualquier fila marcada como vigente marca el plan: el panel ofrece el
                        // interruptor en todas, así que leer solo la primera lo volvía decorativo.
                        'destacado' => $items->contains(fn ($item): bool => (bool) $item->vigente),
                        'items' => $items->map(fn ($c) => [
                            'nombre' => $c->nombre,
                            'texto' => $c->texto,
                        ])->all(),
                    ])
                    ->values()->all(),
            ])
            ->values();
    }
}
