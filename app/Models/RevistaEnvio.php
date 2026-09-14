<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RevistaEnvio extends Model
{
    public const ESTADOS = ['recibido' => 'Recibido', 'verificacion_documental' => 'Verificación documental', 'observado' => 'Observado', 'correccion_recibida' => 'Corrección recibida', 'revision_editorial' => 'Revisión editorial', 'revision_pares' => 'Revisión por pares', 'aceptado' => 'Aceptado', 'rechazado' => 'Rechazado', 'publicado' => 'Publicado'];

    /**
     * Estados en los que el autor todavía puede subir una versión corregida.
     * Fuera de esta lista el envío ya salió del circuito de correcciones
     * (aceptado, rechazado o publicado) y no debe volver a la cola editorial.
     *
     * @var array<int, string>
     */
    /**
     * Fases que se enseñan al público, y qué estados internos cubre cada una.
     *
     * La tira del flujo editorial salía de una lista escrita a mano que no
     * coincidía con los estados reales: anunciaba «Respuesta por correo», que
     * no es una fase sino algo que ocurre entre fases, y se dejaba fuera
     * «Revisión editorial», que sí lo es. Un autor cuyo manuscrito estuviera en
     * revisión editorial consultaba su envío, leía esa fase, y no la encontraba
     * en el diagrama.
     *
     * Derivarlo de aquí obliga a que el diagrama y la consulta digan lo mismo
     * siempre, y un estado nuevo sin fase asignada rompe el test.
     *
     * @var array<string, array<int, string>>
     */
    public const FASES_PUBLICAS = [
        'Recepción' => ['recibido'],
        'Verificación documental' => ['verificacion_documental'],
        'Corrección' => ['observado', 'correccion_recibida'],
        'Revisión editorial' => ['revision_editorial'],
        'Revisión por pares' => ['revision_pares'],
        'Decisión' => ['aceptado', 'rechazado', 'publicado'],
    ];

    public const ESTADOS_ADMITEN_CORRECCION = ['observado'];

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
