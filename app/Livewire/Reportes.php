<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Maestro;

class Reportes extends Component
{
    public $anio = null;
    public $grado = null;
    public $horas_limite = 30;
    public $busqueda = '';


    public function mount()
    {
        $this->anio = date('Y');
    }


    public function maestrosPorMateria($materia_id)
    {
        return Materia::with(['grupos.imparte.maestro' => function($q) {
            $q->distinct()->select('maestros.id', 'name', 'apellidos');
        }])->findOrFail($materia_id);
    }


    public function materiasSinMaestro()
    {
        return Materia::whereDoesntHave('grupos.imparte')
            ->when($this->grado, function($query) {
                $query->where('grado', $this->grado);
            })
            ->get();
    }


    public function gruposSinMaestro()
    {
        return Grupo::whereDoesntHave('imparte')
            ->when($this->anio, function($query) {
                $query->where('generacion', $this->anio);
            })
            ->get();
    }


    public function maestrosSobrecargados()
    {
        return Maestro::withSum('imparte as total_horas', 'horas')
            ->having('total_horas', '>', $this->horas_limite)
            ->get();
    }


    public function maestrosDisponibles()
    {
        return Maestro::whereDoesntHave('imparte')
            ->orWhereHas('imparte', fn($q) => $q->where('horas', '<', 5))
            ->get();
    }


    public function buscarMaestro()
    {
        return Maestro::where('curp', 'like', "%{$this->busqueda}%")
            ->orWhere('email', 'like', "%{$this->busqueda}%")
            ->with('imparte.grupo.materia')
            ->first();
    }

    public function materiasConMaestro()
{
    return Materia::whereHas('grupos.imparte')
        ->when($this->grado, function($query) {
            $query->where('grado', $this->grado);
        })
        ->with(['grupos' => function($q) {
            $q->with('imparte.maestro');
        }])
        ->get();
}


    public function render()
    {
        return view('livewire.reportes', [
            'datos' => [

                'materias_sin_maestro' => $this->materiasSinMaestro()->count(),
                'grupos_sin_maestro' => $this->gruposSinMaestro()->count(),
                'maestros_sobrecargados' => $this->maestrosSobrecargados()->count(),


                'anio_actual' => $this->anio
            ]
        ]);
    }

}
