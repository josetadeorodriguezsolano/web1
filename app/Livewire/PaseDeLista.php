<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Grupo;
use App\Models\Falta;

class PaseDeLista extends Component
{
    public $total;
    public $grupo; //id = 1
    public $gruposImpartidos;
    public $checkbox = true;

    public function mount(){
        $this->total = 10;
        $grupo = Grupo::with('alumnos')->first();
        //$this->grupo = $grupo->only(['id','alumnos']);
        $alumnos = $grupo->alumnos->map(function($alumno){
            $alumno->falto = ($alumno->falto(date('Y-m-d'))!=null);
            return  $alumno->only(['id','nombres','apellidos','falto']);
        });
        $grupo->alumnos = $alumnos;
        $this->grupo = $grupo->only(['id','alumnos']);
        $this->gruposImpartidos = Auth::user()->gruposImpartidos(2024);
    }

    public function render()
    {
        return view('livewire.pase-de-lista');
    }

    public function faltas($key){
        if ($this->grupo['alumnos'][$key]['falto'])
            Falta::eliminar($this->grupo['alumnos'][$key]['id']);
        else
            Falta::insertar($this->grupo['alumnos'][$key]['id']);
    }

}
