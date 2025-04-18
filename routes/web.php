<?php

use App\Http\Controllers\PaseDeListaController;
use App\Http\Controllers\PdfController;
use App\Livewire\PaseDeLista;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogPeticion;
use App\Livewire\CatalogoMaestros;

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
    Route::get('lista/{grupo_id}',[PdfController::class, 'lista']);
});

Route::get("w3css",function(){
    return view("w3css");
});

Route::get("bootstrap",function(){
    return view("bootstrap");
});
