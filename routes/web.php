<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\RevistaController;
use App\Http\Controllers\RevistaSubmissionController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'signed:relative'])->prefix('preview')->name('preview.')->group(function (): void {
    Route::get('/blog/{post}', [PreviewController::class, 'blog'])->name('blog');
    Route::get('/comunicados/{comunicado}', [PreviewController::class, 'comunicado'])->name('comunicado');
    Route::get('/docentes/{docente}', [PreviewController::class, 'docente'])->name('docente');
    Route::get('/revista/{revista}', [PreviewController::class, 'revista'])->name('revista');
    Route::get('/revista-numeros/{numero}', [PreviewController::class, 'numero'])->name('revista-numero');
    Route::get('/articulos/{articulo}', [PreviewController::class, 'articulo'])->name('articulo');
});

// --- Programa ---
Route::get('/presentacion', [PageController::class, 'presentacion'])->name('presentacion');
Route::get('/resumen', [PageController::class, 'resumen'])->name('resumen');
Route::get('/historia', [PageController::class, 'historia'])->name('historia');
Route::get('/mision', [PageController::class, 'mision'])->name('mision');
Route::get('/campo-laboral', [PageController::class, 'campoLaboral'])->name('campo-laboral');
Route::get('/objetivos', [PageController::class, 'objetivos'])->name('objetivos');

// --- Académico ---
Route::get('/plan-2023', [PageController::class, 'planEstudios2023'])->name('plan-2023');
Route::get('/plan-2019', [PageController::class, 'planEstudios2019'])->name('plan-2019');
Route::get('/competencias', [PageController::class, 'competencias'])->name('competencias');
Route::get('/perfil-ingreso', [PageController::class, 'perfilIngreso'])->name('perfil-ingreso');
Route::get('/perfil-egreso', [PageController::class, 'perfilEgreso'])->name('perfil-egreso');

// --- Publicaciones ---
Route::get('/revista', [RevistaController::class, 'index'])->name('revista');
Route::get('/revista/actual', [RevistaController::class, 'actual'])->name('revista.actual');
Route::get('/revista/archivos', [RevistaController::class, 'archivos'])->name('revista.archivos');
Route::get('/revista/politicas-editoriales', [RevistaController::class, 'politicas'])->name('revista.politicas');
Route::get('/revista/comite-editorial', [RevistaController::class, 'comiteEditorial'])->name('revista.comite-editorial');
Route::get('/revista/comite-cientifico', [RevistaController::class, 'comiteCientifico'])->name('revista.comite-cientifico');
Route::get('/revista/avisos', [RevistaController::class, 'avisos'])->name('revista.avisos');
Route::get('/revista/envios', [RevistaController::class, 'envios'])->name('revista.envios');
Route::post('/revista/envios', [RevistaSubmissionController::class, 'store'])->middleware('throttle:5,60')->name('revista.envios.store');
Route::post('/revista/envios/correccion', [RevistaSubmissionController::class, 'correction'])->middleware('throttle:5,60')->name('revista.envios.correction');
Route::get('/revista/sobre-la-revista', [RevistaController::class, 'sobre'])->name('revista.sobre');
Route::get('/revista/indexacion', [RevistaController::class, 'indexacion'])->name('revista.indexacion');
Route::get('/revista/contacto', [RevistaController::class, 'contacto'])->name('revista.contacto');
Route::get('/revista/declaracion-privacidad', [RevistaController::class, 'privacidad'])->name('revista.privacidad');
Route::get('/revista/preservacion-digital', [RevistaController::class, 'preservacion'])->name('revista.preservacion');
Route::get('/revista/formatos-y-plantillas', [RevistaController::class, 'formatos'])->name('revista.formatos');
Route::get('/revista/equipo-editorial', [RevistaController::class, 'equipo'])->name('revista.equipo');
Route::get('/revista/normas-para-autores', [RevistaController::class, 'normas'])->name('revista.normas');
Route::middleware('auth')->prefix('admin/revista-envios')->name('revista.envios.admin.')->group(function (): void {
    Route::get('/{envio}/archivos/{type}', [RevistaSubmissionController::class, 'download'])->name('download');
    Route::get('/{envio}/versiones/{version}', [RevistaSubmissionController::class, 'downloadVersion'])->name('version');
});
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/revista/{numero:slug}', [RevistaController::class, 'numero'])->name('revista.numero');
Route::get('/revista/{numero:slug}/{articulo:slug}', [RevistaController::class, 'articulo'])->name('revista.articulo');

// --- Más ---
Route::get('/docentes', [DocenteController::class, 'index'])->name('docentes');
Route::get('/docentes/{docente:slug}', [DocenteController::class, 'show'])->name('docentes.show');
Route::get('/comunicados', [ComunicadoController::class, 'index'])->name('comunicados');
Route::get('/comunicados/{comunicado:slug}', [ComunicadoController::class, 'show'])->name('comunicados.show');
Route::get('/estadisticas/{tipo}', [EstadisticaController::class, 'show'])
    ->where('tipo', 'matriculados|egresados|graduados|titulados')
    ->name('estadisticas');
Route::get('/organigrama', [PageController::class, 'organigrama'])->name('organigrama');
Route::get('/documentos', [PageController::class, 'documentos'])->name('documentos');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
