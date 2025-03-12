<?php

use App\Http\Controllers\PaseDeListaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('pase_de_lista')->controller(PaseDeListaController::class)
    ->group(function () {
    Route::get('','mostrar');
    Route::get('{$grupo_id}','selectGrupo');
    Route::get('inasistencia/vino/{alumno_id}','vino');
    Route::get('inasistencia/falto/{alumno_id}','falto');
    Route::get('listar/{$numero_de_lista}','listar');
    Route::get('listar/{$numero_de_lista}/vino','listarVino');
    Route::get('listar/{$numero_de_lista}/falto','listarFalto');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get("w3css",function(){
    return view("w3css");
});

Route::get("bootstrap",function(){
    return view("bootstrap");
});
