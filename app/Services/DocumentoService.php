<?php

namespace App\Services;

use App\Repositories\Contracts\DocumentoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DocumentoService
{
    public function __construct(
        private readonly DocumentoRepositoryInterface $documentos,
    ) {
    }

    public function listado(): Collection
    {
        return $this->documentos->ordenados();
    }
}
