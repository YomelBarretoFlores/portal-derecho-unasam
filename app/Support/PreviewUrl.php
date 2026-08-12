<?php

namespace App\Support;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Revista;
use App\Models\RevistaNumero;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use InvalidArgumentException;

class PreviewUrl
{
    public static function for(Model $record): string
    {
        [$route, $parameter] = match (true) {
            $record instanceof BlogPost => ['preview.blog', 'post'],
            $record instanceof Comunicado => ['preview.comunicado', 'comunicado'],
            $record instanceof Docente => ['preview.docente', 'docente'],
            $record instanceof Revista => ['preview.revista', 'revista'],
            $record instanceof RevistaNumero => ['preview.revista-numero', 'numero'],
            $record instanceof Articulo => ['preview.articulo', 'articulo'],
            default => throw new InvalidArgumentException('Este contenido no admite vista previa.'),
        };

        // Las vistas previas siempre se abren en el mismo sitio que el panel.
        // Firmar solo la ruta evita que el esquema/host reescrito por el proxy de
        // Render invalide el enlace al pasar de HTTP interno a HTTPS público.
        return URL::temporarySignedRoute(
            $route,
            now()->addMinutes(15),
            [$parameter => $record],
            absolute: false,
        );
    }

    public static function available(Model $record): bool
    {
        return $record->exists && ! ($record instanceof Articulo && blank($record->revista_numero_id));
    }

    public static function publicUrl(Model $record): ?string
    {
        return match (true) {
            $record instanceof BlogPost && $record->publicado => route('blog.show', $record),
            $record instanceof Comunicado && $record->publicado => route('comunicados.show', $record),
            $record instanceof Docente && $record->activo && $record->estado_revision === 'verified' => route('docentes.show', $record),
            $record instanceof Revista && $record->activo && $record->publicado => route('revista'),
            $record instanceof RevistaNumero && $record->publicado => route('revista.numero', $record),
            $record instanceof Articulo && $record->publicado && $record->numero?->publicado => route('revista.articulo', [$record->numero, $record]),
            default => null,
        };
    }
}
