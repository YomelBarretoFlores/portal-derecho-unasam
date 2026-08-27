<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RevistaService
{
    public function revistaPublica(): ?Revista
    {
        return Revista::query()
            ->publica()
            ->with([
                'media',
                'contactos',
                'miembros' => fn ($query) => $query->activos()->ordenados(),
            ])
            ->first();
    }

    public function numeros(?string $q = null): LengthAwarePaginator
    {
        return RevistaNumero::query()->publicados()->with('media')
            ->when($q, fn ($query) => $query->where(fn ($search) => $search
                ->where('titulo', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%")))
            ->orderByDesc('fecha_publicacion')->orderByDesc('orden')
            ->paginate(12)->withQueryString();
    }

    public function articulos(RevistaNumero $numero, ?string $q = null, ?string $categoria = null): LengthAwarePaginator
    {
        return Articulo::query()->publicados()
            ->where('revista_numero_id', $numero->id)
            ->with('media')
            ->when($categoria, fn ($query) => $query->where('categoria', $categoria))
            ->when($q, fn ($query) => $query->where(fn ($search) => $search
                ->where('titulo', 'like', "%{$q}%")
                ->orWhere('resumen', 'like', "%{$q}%")))
            ->orderBy('orden')->orderBy('fecha')
            ->paginate(12)->withQueryString();
    }
}
