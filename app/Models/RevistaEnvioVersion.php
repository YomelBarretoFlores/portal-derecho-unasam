<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevistaEnvioVersion extends Model
{
    protected $table = 'revista_envio_versiones';

    protected $fillable = ['revista_envio_id', 'numero', 'archivo_path', 'nota_autor', 'recibida_at'];

    protected $casts = ['numero' => 'integer', 'recibida_at' => 'datetime'];

    protected $hidden = ['archivo_path'];

    public function envio(): BelongsTo
    {
        return $this->belongsTo(RevistaEnvio::class, 'revista_envio_id');
    }
}
