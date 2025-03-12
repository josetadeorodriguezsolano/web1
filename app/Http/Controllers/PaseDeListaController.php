<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Inasistencia;
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
        $gruposImpartidos = null;
        $maestro = Auth::user();
        $grupo = Grupo::find(1);

        // $año = 2024;//Configuracion::añoActual();
        // $gruposImpartidos = Grupo::join('imparte','grupos.id','=','imparte.grupo_id')
        //             ->where([['grupos.generacion',$año],
        //                     ['imparte.maestro_id',$maestro->id]])->get();
        $inscritos = Inscrito::where('grupo_id',$grupo->id)->get();
        return view('pase_de_lista',['gruposImpartidos'=>$gruposImpartidos,
                    'grupo'=>$grupo]);
    }

    public function selectGrupo($grupo_id){

    }

    public function falto($alumno_id) {
        $grupo_id = Session::get(PaseDeListaController::PASAR_LISTA_GRUPO_ID);
        $result = Inasistencia::insertar($grupo_id, $alumno_id);
        if ($result !== true)
            return redirect()->back()->withErrors($result)->withInput();
        return view('pase_de_lista');
    }

    public function vino($alumno_id){
        $grupo_id = Session::get(PaseDeListaController::PASAR_LISTA_GRUPO_ID);
        $result = Inasistencia::eliminar($grupo_id, $alumno_id);
        return view('pase_de_lista');
    }

    public function listar($numero_de_lista){}

    public function listarVino($numero_de_lista){}

    public function listarFalto($numero_de_lista){}
}
