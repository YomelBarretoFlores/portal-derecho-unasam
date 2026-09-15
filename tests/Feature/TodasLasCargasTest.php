<?php

namespace Tests\Feature;

use App\Models\Articulo;
use App\Models\BlogPost;
use App\Models\Comunicado;
use App\Models\Docente;
use App\Models\Documento;
use App\Models\Organigrama;
use App\Models\Revista;
use App\Models\RevistaAviso;
use App\Models\RevistaDocumento;
use App\Models\RevistaNumero;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Las DOCE casillas de subida del panel, una por una.
 *
 * Existía una prueba de esto, pero solo cubría dos: la imagen del blog y el
 * PDF de un número. Las otras diez se daban por buenas porque «usan el mismo
 * mecanismo», y eso es cierto del transporte y falso de la configuración: el
 * nombre de la colección, el tipo aceptado y las conversiones se escriben a
 * mano en cada formulario, y una errata ahí no la detecta ninguna de las
 * pruebas anteriores. Se descubre el día que alguien intenta publicar.
 *
 * Deliberadamente NO se usa Storage::fake: con un disco simulado, el archivo
 * «se guarda» aunque el disco real esté mal configurado, que es justo el fallo
 * que se quiere detectar. Cada prueba limpia lo suyo.
 */
class TodasLasCargasTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{0: string, 1: string, 2: bool, 3: bool}> */
    public static function casillas(): array
    {
        return [
            // nombre visible => [modelo, colección, ¿es imagen?, ¿declara miniatura?]
            'Comunicados · imagen' => [Comunicado::class, 'imagen', true, true],
            'Blog · imagen' => [BlogPost::class, 'imagen', true, true],
            'Docentes · foto' => [Docente::class, 'foto', true, true],

            // Estas tres son imágenes y NO declaran miniatura. Está recogido
            // aquí porque es el estado real y no un descuido silencioso: el
            // navegador se descarga la imagen entera para pintarla pequeña.
            // Si alguien añade la conversión, este test se lo dirá.
            'Organigrama · imagen' => [Organigrama::class, 'imagen', true, false],
            'Revista · logo' => [Revista::class, 'logo', true, false],
            'Número · portada' => [RevistaNumero::class, 'portada', true, false],

            'Normativa · archivo' => [Documento::class, 'archivo', false, false],
            'Artículos · pdf' => [Articulo::class, 'pdf', false, false],
            'Revista · resolución' => [Revista::class, 'resolucion', false, false],
            'Número · PDF' => [RevistaNumero::class, 'numero_pdf', false, false],
            'Documentos de revista · archivo' => [RevistaDocumento::class, 'archivo', false, false],
            'Avisos de revista · adjunto' => [RevistaAviso::class, 'adjunto', false, false],
        ];
    }

    #[DataProvider('casillas')]
    public function test_the_file_is_attached_and_read_back(string $modelo, string $coleccion, bool $esImagen, bool $esperaMiniatura): void
    {
        $registro = $this->registroDe($modelo);
        $origen = $esImagen ? public_path('img/mascota-volando.webp') : public_path('docs/revista/normas-publicacion-v1.pdf');

        $registro->addMedia($origen)->preservingOriginal()->toMediaCollection($coleccion);
        $registro->refresh();

        $media = $registro->getFirstMedia($coleccion);

        $this->assertNotNull($media, "«{$coleccion}» no guardó ninguna fila: revise el nombre de la colección en el formulario.");
        $this->assertFileExists($media->getPath(), "«{$coleccion}» dejó la fila en la base pero no el archivo en el disco.");
        $this->assertSame(filesize($origen), $media->size, "«{$coleccion}» guardó un archivo de otro tamaño.");
        $this->assertNotSame('', (string) $media->getUrl(), "«{$coleccion}» no produce ninguna dirección para enlazarlo.");

        $registro->clearMediaCollection($coleccion);
    }

    #[DataProvider('casillas')]
    public function test_the_thumbnail_matches_what_the_model_declares(string $modelo, string $coleccion, bool $esImagen, bool $esperaMiniatura): void
    {
        // Las conversiones se declaran en cada modelo por separado. Una imagen
        // que declara miniatura y no la genera deja el listado del panel con
        // el hueco vacío, y nadie sabe por qué.
        $registro = $this->registroDe($modelo);
        $origen = $esImagen ? public_path('img/mascota-volando.webp') : public_path('docs/revista/normas-publicacion-v1.pdf');

        $registro->addMedia($origen)->preservingOriginal()->toMediaCollection($coleccion);
        $registro->refresh();

        $media = $registro->getFirstMedia($coleccion);

        if ($esperaMiniatura) {
            $this->assertTrue($media->hasGeneratedConversion('thumb'), "«{$coleccion}» declara miniatura y no la generó: ¿falta GD en este servidor?");
            $this->assertFileExists($media->getPath('thumb'));
        } else {
            $this->assertFalse($media->hasGeneratedConversion('thumb'), "«{$coleccion}» generó una miniatura que el modelo no declara.");
        }

        $registro->clearMediaCollection($coleccion);
    }

    /**
     * Un registro mínimo válido de cada tipo.
     *
     * Se crean a mano porque el proyecto no tiene factories: los modelos se
     * llenan desde el panel o desde la siembra, no desde pruebas.
     */
    private function registroDe(string $modelo): Model
    {
        $revista = fn (): Revista => Revista::query()->firstOrCreate(
            ['nombre_corto' => 'RX'],
            ['nombre' => 'Revista de prueba', 'estado_editorial' => 'draft'],
        );

        return match ($modelo) {
            Comunicado::class => Comunicado::query()->create([
                'titulo' => 'Comunicado de prueba', 'slug' => 'comunicado-'.uniqid(),
                'extracto' => 'E.', 'contenido' => '<p>C.</p>',
                'fecha' => '2026-01-01', 'publicado' => false, 'estado_editorial' => 'draft',
            ]),
            BlogPost::class => BlogPost::query()->create([
                'tipo' => 'noticia', 'titulo' => 'Entrada de prueba', 'slug' => 'entrada-'.uniqid(),
                'extracto' => 'E.', 'contenido' => '<p>C.</p>', 'autor' => 'R.',
                'fecha' => '2026-01-01', 'publicado' => false, 'estado_editorial' => 'draft',
            ]),
            Docente::class => Docente::query()->create([
                'name' => 'Docente de prueba', 'slug' => 'docente-'.uniqid(),
                'activo' => false, 'estado_editorial' => 'draft',
            ]),
            Organigrama::class => Organigrama::query()->create([
                'titulo' => 'Organigrama de prueba', 'estado_editorial' => 'draft',
            ]),
            Revista::class => $revista(),
            RevistaNumero::class => RevistaNumero::query()->create([
                'revista_id' => $revista()->id, 'volumen' => '1', 'numero' => uniqid(),
                'titulo' => 'Número de prueba', 'slug' => 'numero-'.uniqid(), 'estado_editorial' => 'draft',
            ]),
            Documento::class => Documento::query()->create([
                'titulo' => 'Norma de prueba', 'categoria' => 'general', 'estado_editorial' => 'draft',
            ]),
            Articulo::class => Articulo::query()->create([
                'revista_numero_id' => RevistaNumero::query()->create([
                    'revista_id' => $revista()->id, 'volumen' => '1', 'numero' => uniqid(),
                    'titulo' => 'Número contenedor', 'slug' => 'contenedor-'.uniqid(), 'estado_editorial' => 'draft',
                ])->id,
                'titulo' => 'Artículo de prueba', 'slug' => 'articulo-'.uniqid(),
                'autores' => 'A.', 'estado_editorial' => 'draft',
            ]),
            RevistaDocumento::class => RevistaDocumento::query()->create([
                'revista_id' => $revista()->id, 'titulo' => 'Documento de prueba',
                'categoria' => 'norma', 'estado_editorial' => 'draft',
            ]),
            RevistaAviso::class => RevistaAviso::query()->create([
                'revista_id' => $revista()->id, 'titulo' => 'Aviso de prueba',
                'cuerpo' => 'C.', 'estado_editorial' => 'draft',
            ]),
            default => throw new \InvalidArgumentException("Sin receta para {$modelo}."),
        };
    }
}
