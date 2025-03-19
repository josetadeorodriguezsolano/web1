<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Grupo;
use App\Models\Falta;
use App\Models\Maestro;
use Livewire\Attributes\Locked;

class PaseDeLista extends Component
{
    private $año = 2024;
    #[locked]
    public $gruposImpartidos;
    public $grupo; //id = 1
    public $selectGrupo;

    public function mount(){
        $impartidos = Auth::user()->gruposImpartidos($this->año);
        $this->gruposImpartidos = $impartidos->map(function($imparte){
            $imparte->grupo = $imparte->grupo->only(['id','letra']);
            $imparte->materia = $imparte->materia->only(['id','grado','nombre']);
            return  $imparte->only(['grupo','materia']);
        })->values();
        //dd($this->gruposImpartidos);
        $this->selectGrupo = $this->gruposImpartidos[0]['grupo']['id'];
        $this->cambiarGrupo();
    }

    public function render()
    {
        //$this->gruposImpartidos = Auth::user()->gruposImpartidos($this->año);
        return view('livewire.pase-de-lista');//,['gruposImpartidos' => $this->gruposImpartidos]);
    }

    public function cambiarGrupo(){
        $grupo = Grupo::with('alumnos')->find($this->selectGrupo);
        $alumnos = $grupo->alumnos->map(function($alumno){
            $alumno->falto = ($alumno->falto(date('Y-m-d'))==null);
            return  $alumno->only(['id','nombres','apellidos','falto']);
        });
        $grupo->alumnos = $alumnos;
        $this->grupo = $grupo->only(['id','alumnos']);
    }

    public function faltas($key){
        if ($this->grupo['alumnos'][$key]['falto'])
            Falta::eliminar($this->grupo['alumnos'][$key]['id']);
        else
            Falta::insertar($this->grupo['alumnos'][$key]['id']);
    }

    public function updatedSelectGrupo(){
        //dd($this->selectGrupo);
        $this->cambiarGrupo();
    }
}
