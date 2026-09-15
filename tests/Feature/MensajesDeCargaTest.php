<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Cuando una carga falla, el panel tiene que decirlo en castellano.
 *
 * Se vio en el servidor de la UNASAM al subir un PDF de 9 MB por encima del
 * límite del servidor web. El panel contestaba:
 *
 *     The data.archivo.f62d80ec-ef13-4afd-8508-64a02aec3577 failed to upload.
 *
 * Livewire construye ese texto él mismo cuando «validation.uploaded» no está
 * traducido. No es un error del servidor: es lo que ve el personal
 * administrativo, en inglés y con el identificador interno del campo.
 */
class MensajesDeCargaTest extends TestCase
{
    public function test_the_upload_failure_message_is_translated(): void
    {
        $mensaje = trans('validation.uploaded');

        $this->assertNotSame('validation.uploaded', $mensaje, 'Falta la traducción: Livewire caerá al texto en inglés.');
        $this->assertStringNotContainsString('failed to upload', $mensaje);
        $this->assertStringContainsString('archivo', $mensaje);
    }

    public function test_the_message_says_what_to_do_about_it(): void
    {
        // Un mensaje traducido que solo diga «error» no sirve de nada: la causa
        // casi siempre es el tamaño, y eso tiene solución para quien lo lee.
        $this->assertMatchesRegularExpression(
            '/pesa|tamaño|ligero/iu',
            trans('validation.uploaded'),
            'El mensaje no orienta sobre la causa más probable.',
        );
    }

    public function test_the_size_limit_message_is_translated_too(): void
    {
        $mensaje = trans('validation.max.file', ['max' => 20480]);

        $this->assertStringNotContainsString('validation.max', $mensaje);
        $this->assertStringContainsString('20480', $mensaje);
    }
}
