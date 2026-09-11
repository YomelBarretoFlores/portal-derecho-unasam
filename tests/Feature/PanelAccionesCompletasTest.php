<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Crear sin poder borrar no es administrar.
 *
 * Cinco recursos de la revista dejaban dar de alta filas —avisos, contactos,
 * documentos, líneas de investigación y los 37 miembros de los comités— y no
 * ofrecían ninguna forma de quitarlas: ni acción de fila ni acción en lote. Un
 * aviso equivocado se quedaba publicado para siempre, y a un miembro que deja
 * el comité no había manera de retirarlo salvo tocando la base de datos.
 *
 * Se revisa aquí y no a mano porque es justo el descuido que reaparece: el
 * recurso siguiente se escribe copiando uno de estos, y hereda el hueco.
 */
class PanelAccionesCompletasTest extends TestCase
{
    /**
     * Recursos que NO deben permitir borrar, y por qué.
     *
     * Cada excepción es una decisión, no un olvido. Si alguien añade un
     * recurso nuevo sin borrado tendrá que justificarlo aquí.
     *
     * @var array<string, string>
     */
    private const SIN_BORRADO_A_PROPOSITO = [
        'ContentAudits' => 'Registro de cambios: si se pudiera borrar, no serviría como registro.',
        'Organigramas' => 'Fila única; la página existe siempre. Tampoco se crea.',
        'Revistas' => 'Fila única: borrarla dejaría el micrositio de la revista sin cabecera.',
        'RevistaEnvios' => 'Manuscritos recibidos de autores externos: son constancia de lo enviado.',
        'RevistaEnvioVersiones' => 'Versiones de esos manuscritos, por la misma razón.',
    ];

    /** @return array<int, string> */
    private function recursos(): array
    {
        return collect(File::directories(app_path('Filament/Resources')))
            ->map(fn (string $ruta): string => basename($ruta))
            ->values()
            ->all();
    }

    private function codigoDe(string $recurso): string
    {
        return collect(File::allFiles(app_path("Filament/Resources/{$recurso}")))
            ->reduce(fn (string $acc, $f): string => $acc.File::get($f->getPathname()), '');
    }

    public function test_every_content_resource_can_be_deleted_from_the_panel(): void
    {
        $sinBorrado = [];

        foreach ($this->recursos() as $recurso) {
            if (array_key_exists($recurso, self::SIN_BORRADO_A_PROPOSITO)) {
                continue;
            }

            $codigo = $this->codigoDe($recurso);

            if (! str_contains($codigo, 'DeleteAction::make') && ! str_contains($codigo, 'DeleteBulkAction::make')) {
                $sinBorrado[] = $recurso;
            }
        }

        $this->assertSame([], $sinBorrado, 'Se puede crear pero no borrar en: '.implode(', ', $sinBorrado)
            .'. Añada DeleteAction/DeleteBulkAction, o declare la excepción con su motivo en SIN_BORRADO_A_PROPOSITO.');
    }

    public function test_every_content_resource_can_be_edited_from_the_panel(): void
    {
        $sinEdicion = [];

        foreach ($this->recursos() as $recurso) {
            if ($recurso === 'ContentAudits') {
                continue;   // registro de solo lectura
            }

            $codigo = $this->codigoDe($recurso);

            if (! str_contains($codigo, 'EditAction::make') && ! str_contains($codigo, 'EditRecord')) {
                $sinEdicion[] = $recurso;
            }
        }

        $this->assertSame([], $sinEdicion, 'No se puede editar: '.implode(', ', $sinEdicion));
    }

    public function test_the_declared_exceptions_still_exist(): void
    {
        // Una excepción que nombra un recurso borrado o renombrado deja de
        // proteger nada y encima tapa el hueco de otro: el test seguiría en
        // verde por la razón equivocada.
        foreach (array_keys(self::SIN_BORRADO_A_PROPOSITO) as $recurso) {
            $this->assertDirectoryExists(
                app_path("Filament/Resources/{$recurso}"),
                "La excepción «{$recurso}» apunta a un recurso que ya no existe.",
            );
        }
    }
}
