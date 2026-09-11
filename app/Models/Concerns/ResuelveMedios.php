<?php

namespace App\Models\Concerns;

/**
 * Resuelve la dirección de un archivo publicado a partir de tres fuentes.
 *
 * El orden es el mismo en todo el portal, y no es arbitrario:
 *
 *   1. El archivo SUBIDO desde el panel. Es un acto deliberado de quien
 *      administra y manda sobre lo demás. Solo cuenta si se puede comprobar
 *      que sigue ahí: en un alojamiento sin disco persistente las filas de la
 *      tabla media sobreviven al despliegue y los archivos no, así que una
 *      fila suelta produciría una imagen rota en vez de una ausencia limpia.
 *   2. La URL de RESPALDO escrita en el panel. Es el camino previsto mientras
 *      las cargas estén deshabilitadas.
 *   3. El archivo DOCUMENTADO que viaja en el repositorio, donde lo haya.
 *      Es la red de seguridad: no se administra, pero nunca desaparece.
 *
 * Antes cada modelo llevaba su propia versión de esto y no todas coincidían.
 */
trait ResuelveMedios
{
    /**
     * @param  string  $coleccion  colección de Media Library
     * @param  string  $respaldo  URL escrita en el panel
     * @param  string  $documentado  ruta pública que viaja con la aplicación
     */
    protected function resolverMedia(string $coleccion, string $respaldo = '', string $documentado = ''): string
    {
        if ($this->archivoSubidoDisponible($coleccion)) {
            return $this->getFirstMediaUrl($coleccion);
        }

        return $respaldo !== '' ? $respaldo : $documentado;
    }

    /**
     * ¿Existe de verdad el archivo subido, o solo su fila en la base de datos?
     *
     * En un disco local se comprueba. En uno remoto no se comprueba: cada
     * consulta al proveedor costaría una petición de red por imagen y por
     * visita, y un disco remoto no pierde los archivos al desplegar, que es
     * justo el caso del que esta comprobación protege.
     */
    protected function archivoSubidoDisponible(string $coleccion): bool
    {
        if (! config('media.uploads_enabled')) {
            return false;
        }

        $media = $this->getFirstMedia($coleccion);

        if ($media === null) {
            return false;
        }

        $disco = config('filesystems.disks.'.$media->disk.'.driver');

        if ($disco !== 'local') {
            return true;
        }

        $ruta = $media->getPath();

        return is_file($ruta) && filesize($ruta) > 0;
    }
}
