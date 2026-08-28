<?php

namespace App\Http\Controllers;

use App\Models\RevistaEnvio;
use App\Models\RevistaEnvioVersion;
use App\Services\RevistaService;
use App\Services\RevistaSubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RevistaSubmissionController extends Controller
{
    public function store(Request $request, RevistaService $revistas, RevistaSubmissionService $submissions): RedirectResponse
    {
        abort_unless($submissions->available(), 503, 'La recepción de envíos todavía no está habilitada.');
        $revista = $revistas->revistaPublica();
        abort_unless($revista, 404);

        $data = $request->validate([
            'website' => ['nullable', 'max:0'],
            'nombres' => ['required', 'string', 'max:255'],
            'documento_identidad' => ['required', 'string', 'min:6', 'max:20'],
            'afiliacion' => ['required', 'string', 'max:255'],
            'ciudad' => ['required', 'string', 'max:120'],
            'pais' => ['required', 'string', 'max:120'],
            'email_institucional' => ['required', 'email:rfc', 'max:255', function (string $attribute, mixed $value, \Closure $fail): void {
                $domain = Str::lower(Str::afterLast((string) $value, '@'));
                if (in_array($domain, config('submissions.personal_email_domains', []), true)) {
                    $fail('Ingresa un correo institucional, no una cuenta de correo personal.');
                }
            }],
            'whatsapp' => ['required', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'orcid' => ['nullable', 'regex:/^(https:\/\/orcid\.org\/)?\d{4}-\d{4}-\d{4}-[\dX]{4}$/i'],
            'tipo_contribucion' => ['required', Rule::in(array_keys(RevistaEnvio::TIPOS))],
            'revista_linea_investigacion_id' => ['required', Rule::exists('revista_lineas_investigacion', 'id')->where('revista_id', $revista->id)->where('activa', true)],
            'titulo' => ['required', 'string', 'max:255', function (string $attribute, mixed $value, \Closure $fail): void {
                if (str_word_count(strip_tags((string) $value), 0, 'áéíóúÁÉÍÓÚñÑüÜ') > 15) {
                    $fail('El título no debe superar 15 palabras.');
                }
            }],
            'resumen' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (str_word_count(strip_tags((string) $value), 0, 'áéíóúÁÉÍÓÚñÑüÜ') > 200) {
                    $fail('El resumen no debe superar 200 palabras.');
                }
            }],
            'coautores' => ['nullable', 'array', 'max:3'],
            'coautores.*' => ['nullable', 'string', 'max:255'],
            'manuscrito' => ['required', 'file', 'mimes:docx', 'max:'.config('submissions.manuscript_max_kb')],
            'carta' => ['required', 'file', 'mimes:docx,pdf', 'max:'.config('submissions.attachment_max_kb')],
            'declaracion' => ['required', 'file', 'mimes:docx,pdf', 'max:'.config('submissions.attachment_max_kb')],
            'constancia_estilo' => ['required', 'file', 'mimes:pdf', 'max:'.config('submissions.attachment_max_kb')],
            'consentimiento' => ['accepted'],
        ], ['whatsapp.regex' => 'Incluye el código de país, por ejemplo +51950061184.']);

        unset($data['website'], $data['consentimiento'], $data['manuscrito'], $data['carta'], $data['declaracion'], $data['constancia_estilo']);
        $data['coautores'] = array_values(array_filter($data['coautores'] ?? [], fn (?string $value): bool => filled($value))) ?: null;
        $envio = $submissions->create($revista, $data, [
            'manuscrito' => $request->file('manuscrito'), 'carta' => $request->file('carta'),
            'declaracion' => $request->file('declaracion'), 'constancia_estilo' => $request->file('constancia_estilo'),
        ], $request->ip());

        return to_route('revista.envios')->with('submission_success', $envio->codigo_seguimiento);
    }

    public function correction(Request $request, RevistaSubmissionService $submissions): RedirectResponse
    {
        abort_unless($submissions->available(), 503, 'La recepción de envíos todavía no está habilitada.');
        $data = $request->validate([
            'website_correction' => ['nullable', 'max:0'],
            'codigo_seguimiento' => ['required', 'string', 'max:32'],
            'email_institucional_correccion' => ['required', 'email:rfc', 'max:255'],
            'manuscrito_corregido' => ['required', 'file', 'mimes:docx', 'max:'.config('submissions.manuscript_max_kb')],
            'nota_autor' => ['nullable', 'string', 'max:2000'],
        ]);
        $envio = RevistaEnvio::query()->where('codigo_seguimiento', strtoupper($data['codigo_seguimiento']))->first();
        if (! $envio || mb_strtolower($envio->email_institucional) !== mb_strtolower($data['email_institucional_correccion'])) {
            throw ValidationException::withMessages(['codigo_seguimiento' => 'No fue posible validar el código y el correo indicados.']);
        }
        $submissions->addCorrection($envio, $request->file('manuscrito_corregido'), $data['nota_autor'] ?? null);

        return to_route('revista.envios')->with('correction_success', true);
    }

    public function download(Request $request, RevistaEnvio $envio, string $type): StreamedResponse
    {
        Gate::authorize('view', $envio);
        $paths = ['manuscrito' => $envio->manuscrito_path, 'carta' => $envio->carta_path, 'declaracion' => $envio->declaracion_path, 'constancia' => $envio->constancia_estilo_path];
        abort_unless(isset($paths[$type]), 404);

        return Storage::disk(config('submissions.disk'))->download($paths[$type], $envio->codigo_seguimiento.'-'.$type.'.'.pathinfo($paths[$type], PATHINFO_EXTENSION));
    }

    public function downloadVersion(Request $request, RevistaEnvio $envio, RevistaEnvioVersion $version): StreamedResponse
    {
        Gate::authorize('view', $envio);
        abort_unless($version->revista_envio_id === $envio->id, 404);

        return Storage::disk(config('submissions.disk'))->download($version->archivo_path, $envio->codigo_seguimiento.'-version-'.$version->numero.'.docx');
    }
}
