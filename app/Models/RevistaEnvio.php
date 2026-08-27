<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RevistaEnvio extends Model
{
    public const ESTADOS = ['recibido' => 'Recibido', 'verificacion_documental' => 'Verificación documental', 'observado' => 'Observado', 'correccion_recibida' => 'Corrección recibida', 'revision_editorial' => 'Revisión editorial', 'revision_pares' => 'Revisión por pares', 'aceptado' => 'Aceptado', 'rechazado' => 'Rechazado', 'publicado' => 'Publicado'];

    public const TIPOS = ['articulo_original' => 'Artículo original', 'revision' => 'Revisión', 'ensayo' => 'Ensayo', 'resena' => 'Reseña'];

    protected $table = 'revista_envios';

    protected $fillable = ['revista_id', 'revista_linea_investigacion_id', 'codigo_seguimiento', 'nombres', 'documento_identidad', 'afiliacion', 'ciudad', 'pais', 'email_institucional', 'whatsapp', 'orcid', 'tipo_contribucion', 'titulo', 'resumen', 'coautores', 'manuscrito_path', 'carta_path', 'declaracion_path', 'constancia_estilo_path', 'estado', 'observaciones_internas', 'consentimiento_at', 'ip_hash'];

    protected $casts = ['documento_identidad' => 'encrypted', 'whatsapp' => 'encrypted', 'coautores' => 'array', 'consentimiento_at' => 'datetime'];

    protected $hidden = ['documento_identidad', 'whatsapp', 'manuscrito_path', 'carta_path', 'declaracion_path', 'constancia_estilo_path', 'ip_hash'];

    public function revista(): BelongsTo
    {
        return $this->belongsTo(Revista::class);
    }

    public function lineaInvestigacion(): BelongsTo
    {
        return $this->belongsTo(RevistaLineaInvestigacion::class, 'revista_linea_investigacion_id');
    }

    public function versiones(): HasMany
    {
        return $this->hasMany(RevistaEnvioVersion::class)->orderBy('numero');
    }

    public function getDocumentoEnmascaradoAttribute(): string
    {
        $value = (string) $this->documento_identidad;

        return strlen($value) <= 4 ? str_repeat('*', strlen($value)) : str_repeat('*', strlen($value) - 4).substr($value, -4);
    }

    public function getWhatsappEnmascaradoAttribute(): string
    {
        $value = (string) $this->whatsapp;

        return strlen($value) <= 4 ? str_repeat('*', strlen($value)) : substr($value, 0, 3).str_repeat('*', max(strlen($value) - 6, 3)).substr($value, -3);
    }
}
