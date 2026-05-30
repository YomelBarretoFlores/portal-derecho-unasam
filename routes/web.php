<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RevistaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Programa ---
Route::get('/presentacion', [PageController::class, 'presentacion'])->name('presentacion');
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
Route::get('/blog', [BlogController::class, 'index'])->name('blog');

// --- Más ---
Route::get('/docentes', [DocenteController::class, 'index'])->name('docentes');
Route::get('/estadisticas/{tipo}', [EstadisticaController::class, 'show'])
    ->where('tipo', 'matriculados|egresados|graduados|titulados')
    ->name('estadisticas');
Route::get('/organigrama', [PageController::class, 'organigrama'])->name('organigrama');
Route::get('/documentos', [PageController::class, 'documentos'])->name('documentos');
