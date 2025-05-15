<?php

use App\Http\Controllers\PaseDeListaController;
use App\Http\Controllers\PdfController;
use App\Livewire\PaseDeLista;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogPeticion;
use App\Livewire\CatalogoMaestros;
use App\Http\Controllers\InscripcionesController;
use App\Livewire\Calificaciones;
use App\Livewire\InfoAlumno;
use App\Livewire\Inscribir;
use App\Livewire\Inscritos;


Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/calificaciones', function () {
    return view('calificaciones');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->name('calificaciones');

Route::get('/alumno/{alumnoId}', InfoAlumno::class)
    ->name('alumno.info');

// Nueva ruta para la vista estática de detalle de alumno, apuntando al archivo en livewire
Route::get('/info-alumno/{id?}', function ($id = null) {
    return view('livewire.info-alumno');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->name('detalle.alumno');

Route::get('/inscribir', function () {
    return view('inscribir');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->name('inscribir');

Route::get('/inscritos', function () {
    return view('inscritos');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->name('inscritos');




Route::middleware([
    'auth:sanctum', //token autentificacion
    config('jetstream.auth_session'), //autentificacion
    'verified', //verificacion de correo electronico
])->group(function () {


    Route::get('/admin/index', [InscripcionesController::class, 'index'])->name('admin.inscripciones');


    Route::prefix('pase_de_lista')->controller(PaseDeListaController::class)
        ->group(function () {
            Route::get('', 'mostrar');
            Route::get('{grupo_id}', 'selectGrupo');
            Route::get('inasistencia/vino/{alumno_id}', 'vino');
            Route::get('inasistencia/falto/{alumno_id}', 'falto');
            Route::get('listar/{numero_de_lista}', 'listar');
            Route::get('listar/{numero_de_lista}/vino', 'listarVino');
            Route::get('listar/{numero_de_lista}/falto', 'listarFalto');
        });
    Route::get('pase_lista', PaseDeLista::class)->middleware(LogPeticion::class);
    Route::get('catalogo/maestros', CatalogoMaestros::class);
    Route::get('lista/{grupo_id}', [PdfController::class, 'lista']);
});

Route::get('/menu', function () {
    return view('menu');
})->name('menu');


Route::get("w3css", function () {
    return view("w3css");
});

Route::get("bootstrap", function () {
    return view("bootstrap");
});
