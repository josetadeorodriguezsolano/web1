<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AlumnosController;
use App\Http\Controllers\PaseDeListaController;
use App\Http\Controllers\PdfController;
use App\Livewire\PaseDeLista;
use App\Livewire\ReportesGrupo;
use App\Livewire\CatalogoMaestros;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogPeticion;
use App\Http\Livewire\InasistenciasPase;

// Ruta principal ahora usando el componente Livewire
Route::get('/', ReportesGrupo::class)->name('home');

// Rutas de autenticación
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::prefix('pase_de_lista')->controller(PaseDeListaController::class)
    ->group(function () {
        Route::get('','mostrar');
        Route::get('{grupo_id}','selectGrupo');
        Route::get('inasistencia/vino/{alumno_id}','vino');
        Route::get('inasistencia/falto/{alumno_id}','falto');
        Route::get('listar/{numero_de_lista}','listar');
        Route::get('listar/{numero_de_lista}/vino','listarVino');
        Route::get('listar/{numero_de_lista}/falto','listarFalto');
    });
    Route::get('pase_lista',PaseDeLista::class)->middleware(LogPeticion::class);
    Route::get('catalogo/maestros',CatalogoMaestros::class);
    Route::get('lista/{grupo_id}',[PdfController::class, 'lista']);
    Route::resource('alumnos', AlumnoController::class); //placeholder para acceder a las funciones CRUD de Alumno

    // Rutas de pase de lista
    Route::prefix('pase_de_lista')
        ->controller(PaseDeListaController::class)
        ->group(function () {
            Route::get('', 'mostrar');
            Route::get('{grupo_id}', 'selectGrupo');
            Route::get('inasistencia/vino/{alumno_id}', 'vino');
            Route::get('inasistencia/falto/{alumno_id}', 'falto');
            Route::get('listar/{numero_de_lista}', 'listar');
            Route::get('listar/{numero_de_lista}/vino', 'listarVino');
            Route::get('listar/{numero_de_lista}/falto', 'listarFalto');
        });

    // Componentes Livewire
    Route::get('pase_lista', PaseDeLista::class)->middleware(LogPeticion::class);
    Route::get('catalogo/maestros', CatalogoMaestros::class);

    // Generación de PDFs
    Route::get('lista/{grupo_id}', [PdfController::class, 'lista']);
});

// Rutas de prueba de estilos (puedes eliminarlas en producción)
Route::get("w3css", function () {
    return view("w3css");
});

Route::get("bootstrap", function () {
    return view("bootstrap");
});


//Ruta de Inasistencias (Alumnos x Materias)

Route::get('/inasistencias', function () {
    return view('inasistencias.index');
})->name('inasistencias.index');



