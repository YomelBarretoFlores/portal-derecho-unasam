<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevistaContacto extends Model
{
    public const TIPOS = ['persona' => 'Persona', 'correo' => 'Correo editorial', 'telefono' => 'Teléfono', 'red_social' => 'Red social'];

    protected $table = 'revista_contactos';

    protected $fillable = ['revista_id', 'nombre', 'cargo', 'email', 'telefono', 'tipo', 'orden', 'visible'];

    protected $casts = ['orden' => 'integer', 'visible' => 'boolean'];

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function scopePublicos(Builder $query): Builder
    {
        return $query->where('visible', true)->orderBy('orden')->orderBy('nombre');
    }
}
