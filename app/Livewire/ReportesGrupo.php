<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;

class ReportesGrupo extends Component
{
    // Variables
    public $grupo_id = null;
    public $generacion = null;


     //Obtiene todos los grupos con número de alumnos
     //Lo puedes filtrar por generación

    public function gruposConAlumnos()
    {
        return Grupo::query()
            ->when($this->generacion, fn($q) => $q->where('generacion', $this->generacion))
            ->withCount('alumnos')
            ->orderBy('grado')
            ->orderBy('letra')
            ->get();
    }


    // Obtiene alumnos inscritos en un grupo específico (nombres)
     // Requiere que $grupo_id no sea null

    public function alumnosInscritos()
    {
        if(!$this->grupo_id) return collect();

        return Alumno::whereHas('inscritos', fn($q) => $q->where('grupo_id', $this->grupo_id))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
    }


     //Obtiene las generaciones disponibles

    public function generacionesDisponibles()
    {
        return Grupo::select('generacion')
            ->distinct()
            ->orderBy('generacion', 'desc')
            ->get()
            ->pluck('generacion');
    }


     //Método principal para usar en el blase

    public function render()
    {
        return view('livewire.reportes-grupo', [
            'grupos' => $this->gruposConAlumnos(),
            'alumnos' => $this->alumnosInscritos(),
            'generaciones' => $this->generacionesDisponibles()
        ]);
    }
}
