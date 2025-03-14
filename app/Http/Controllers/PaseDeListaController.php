<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Falta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Date;
use App\Models\Maestro;
use Illuminate\Support\Facades\Auth;
use App\Models\Grupo;
use App\Models\Inscrito;

class PaseDeListaController extends Controller
{
    public const PASAR_LISTA_GRUPO_ID = 'pasar_lista_grupo_id';

    public function mostrar(){
        $maestro = Auth::user();
        $generacion = date('Y')-1;
        $gruposImpartidos = $maestro->gruposImpartidos($generacion);
        $grupo = Grupo::find(1);
        return view('pase_de_lista',['gruposImpartidos'=>$gruposImpartidos,
                    'grupo'=>$grupo,'generacion'=>$generacion]);
    }

    public function selectGrupo($grupo_id){

    }

    public function falto($alumno_id) {
        $grupo_id = Session::get(PaseDeListaController::PASAR_LISTA_GRUPO_ID);
        $result = Falta::insertar($grupo_id, $alumno_id);
        if ($result !== true)
            return redirect()->back()->withErrors($result)->withInput();
        return 'false';
        //return view('pase_de_lista');
    }

    public function vino($alumno_id){
        $grupo_id = Session::get(PaseDeListaController::PASAR_LISTA_GRUPO_ID);
        $result = Falta::eliminar($grupo_id, $alumno_id);
        return 'true';
        //return view('pase_de_lista');
    }

    public function listar($numero_de_lista){}

    public function listarVino($numero_de_lista){}

    public function listarFalto($numero_de_lista){}
}
