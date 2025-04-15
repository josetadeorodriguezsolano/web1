<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Maestro;

class Reportes extends Component
{
    // Variables
    public $anio = null;       // Año para filtrar grupos
    public $grado = null;      // (1, 2, 3)
    public $horas_limite = 30; // Horas de sobrecarga
    public $busqueda = '';     // Busquedas

    // Inicio
    public function mount()
    {
        $this->anio = date('Y'); // Año en el que se estará buscando
    }

    // Obtiene los maestros asignados a una materia específica
    public function maestrosPorMateria($materia_id)
    {
        return Materia::with(['grupos.imparte.maestro' => function($q) {
            $q->distinct()->select('maestros.id', 'name', 'apellidos');
        }])->findOrFail($materia_id);
    }

    // Lista de materias sin maestros asignados
    // Puede filtrarse por grado si está especificado
    public function materiasSinMaestro()
    {
        return Materia::whereDoesntHave('grupos.imparte')
            ->when($this->grado, function($query) {
                $query->where('grado', $this->grado);
            })
            ->get();
    }

    // Grupos que no tienen maestro asignado
    // Lo puedes filtrar por año/generación
    public function gruposSinMaestro()
    {
        return Grupo::whereDoesntHave('imparte')
            ->when($this->anio, function($query) {
                $query->where('generacion', $this->anio);
            })
            ->get();
    }

    // Maestros sobrecargados
    // $horas_limite para filtrar como lo había hecho el profe
    public function maestrosSobrecargados()
    {
        return Maestro::withSum('imparte as total_horas', 'horas')
            ->having('total_horas', '>', $this->horas_limite)
            ->get();
    }

    //Maestros disponibles o con pocas horas
    public function maestrosDisponibles()
    {
        return Maestro::whereDoesntHave('imparte')
            ->orWhereHas('imparte', fn($q) => $q->where('horas', '<', 5))
            ->get();
    }

    // Busca un maestro por CURP o email
    public function buscarMaestro()
    {
        return Maestro::where('curp', 'like', "%{$this->busqueda}%")
            ->orWhere('email', 'like', "%{$this->busqueda}%")
            ->with('imparte.grupo.materia')
            ->first();
    }

    // Método principal que muestra la vista
    public function render()
    {
        return view('livewire.reportes', [
            'datos' => [
                // Cantidad de registros para mostrar resumen
                'materias_sin_maestro' => $this->materiasSinMaestro()->count(),
                'grupos_sin_maestro' => $this->gruposSinMaestro()->count(),
                'maestros_sobrecargados' => $this->maestrosSobrecargados()->count(),

                // Año actual, si quieres manejar años anteriores me dices
                'anio_actual' => $this->anio
            ]
        ]);
    }
}
