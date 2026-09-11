<?php

namespace App\Rules;

use App\Support\OrigenesDeImagen;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * La imagen tiene que poder verse, no solo guardarse.
 *
 * Una dirección de otro dominio pasa SafeUrl sin problema —es https y está
 * bien formada— y aun así el navegador se niega a pintarla, porque la cabecera
 * Content-Security-Policy solo admite los orígenes declarados. El resultado es
 * el peor posible para quien administra: el formulario dice «guardado», la
 * página no muestra nada, y no hay ningún error que seguir.
 *
 * Los mensajes están escritos para quien redacta contenido, no para quien
 * administra el servidor. La primera versión terminaba diciendo «pida que
 * añadan ese dominio a CSP_IMG_HOSTS», y una redactora que se topó con eso
 * entendió, con razón, que el portal solo la dejaba publicar texto.
 */
class ImagenVisible implements ValidationRule
{
    /**
     * Sitios cuyos enlaces son páginas, no archivos de imagen.
     *
     * Es el error más frecuente y el más desconcertante: se copia la dirección
     * de la barra del navegador estando en una publicación, y esa dirección
     * lleva a una página entera —con su cabecera, sus comentarios y su muro de
     * inicio de sesión—, no a la fotografía.
     *
     * @var array<string, string>
     */
    private const PAGINAS_NO_IMAGENES = [
        'instagram.com' => 'una publicación de Instagram',
        'facebook.com' => 'una publicación de Facebook',
        'fb.watch' => 'una publicación de Facebook',
        'x.com' => 'una publicación de X',
        'twitter.com' => 'una publicación de X',
        'tiktok.com' => 'una publicación de TikTok',
        'drive.google.com' => 'un archivo de Google Drive',
        'docs.google.com' => 'un documento de Google',
        'youtube.com' => 'un vídeo de YouTube',
        'youtu.be' => 'un vídeo de YouTube',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $url = trim((string) $value);

        if ($url === '') {
            return;
        }

        if (($queEs = $this->esUnaPaginaYNoUnaImagen($url)) !== null) {
            $fail(
                "Ese enlace lleva a {$queEs}, no a una fotografía. Abra la imagen sola "
                .'—clic derecho sobre ella, «Copiar dirección de la imagen»— y pegue esa otra '
                .'dirección, que suele terminar en .jpg, .png o .webp.'
            );

            return;
        }

        if (OrigenesDeImagen::admite($url)) {
            return;
        }

        $fail(
            'Esa imagen está guardada en otro sitio web y el navegador no la mostraría: la '
            .'página quedaría con un hueco. Use una imagen alojada en el propio portal, o pida '
            .'a quien administra el servidor que autorice ese sitio.'
        );
    }

    /** Devuelve qué es el enlace si no es una imagen, o null si puede serlo. */
    private function esUnaPaginaYNoUnaImagen(string $url): ?string
    {
        $dominio = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($dominio === '') {
            return null;
        }

        foreach (self::PAGINAS_NO_IMAGENES as $sitio => $queEs) {
            if ($dominio === $sitio || str_ends_with($dominio, '.'.$sitio)) {
                return $queEs;
            }
        }

        return null;
    }
}
