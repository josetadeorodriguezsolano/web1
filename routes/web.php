<?php

use App\Http\Controllers\PaseDeListaController;
use App\Http\Controllers\PdfController;


use App\Http\Controllers\HorariosC;

use App\Http\Controllers\PDFInasistenciasController;


use App\Livewire\PaseDeLista;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogPeticion;
use App\Livewire\CatalogoMaestros;
use App\Livewire\Ina;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',//token autentificacion
    config('jetstream.auth_session'),//autentificacion
    'verified',//verificacion de correo electronico
])->group(function () {
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
  Route::get('/maestro/{id}/horario/pdf', [PdfController::class, 'horarioGrupo']);



    
  Route::get('ina',Ina::class);

    Route::get('inasistencia', [PDFInasistenciasController::class,'generar']);


 Route::get('/horarios', [HorariosC::class, 'index'])->name('horarios.index'); // Para mostrar todos los horarios
Route::post('/horarios', [HorariosC::class, 'store'])->name('horarios.store'); // Para guardar un nuevo horario
Route::delete('/horarios/{id}', [HorariosC::class, 'destroy'])->name('horarios.destroy'); // Para eliminar un horario
Route::get('/horarios/maestro/{id_maestro}', [HorariosC::class, 'horariosPorMaestro'])->name('horarios.por_maestro'); // Para obtener los horarios por maestro

});

Route::get("w3css",function(){
    return view("w3css");
});

Route::get("bootstrap",function(){
    return view("bootstrap");
});
