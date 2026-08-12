<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\RevistaController;
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
Route::get('/revista/equipo-editorial', [RevistaController::class, 'equipo'])->name('revista.equipo');
Route::get('/revista/normas-para-autores', [RevistaController::class, 'normas'])->name('revista.normas');
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
