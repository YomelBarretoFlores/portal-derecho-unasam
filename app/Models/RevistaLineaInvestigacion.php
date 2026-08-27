<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RevistaLineaInvestigacion extends Model
{
    protected $table = 'revista_lineas_investigacion';

    protected $fillable = ['revista_id', 'nombre', 'descripcion', 'orden', 'activa'];

    protected $casts = ['orden' => 'integer', 'activa' => 'boolean'];

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function envios(): HasMany
    {
        return $this->hasMany(RevistaEnvio::class);
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->where('activa', true)->orderBy('orden')->orderBy('nombre');
    }
}
