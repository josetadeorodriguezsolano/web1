<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Grupo;
use App\Models\Falta;
use App\Models\Materia;
use App\Models\Maestro;
use Livewire\Attributes\Locked;

class PaseDeLista extends Component
{
    private $año = 2024;

    #[Locked]
    public $gruposImpartidos;

    public $grupo; // id = 1
    public $selectGrupo;
    public $buscar;
    public $palabras = [];

    // Nuevas propiedades para manejar materias ()

    public $materias;
    public $materiaSeleccionada;

    public function mount()
    {
        // Obtener todas las materias del maestro
        $this->materias = Materia::materiasPorMaestro(Auth::id(), $this->año);
        $this->materiaSeleccionada = $this->materias->first()->id ?? null;

        // Obtener grupos impartidos
        $impartidos = Auth::user()->gruposImpartidos($this->año);
        $this->gruposImpartidos = $impartidos->map(function($imparte) {
            $imparte->grupo = $imparte->grupo->only(['id', 'letra']);
            $imparte->materia = $imparte->materia->only(['id', 'grado', 'nombre']);
            return $imparte->only(['grupo', 'materia']);
        })->values();

        $this->selectGrupo = $this->gruposImpartidos[0]['grupo']['id'] ?? null;
        $this->cambiarGrupo();
    }

    public function render()
    {
        return view('livewire.pase-de-lista');
    }

    private function cambiarGrupo()
    {
        if (!$this->selectGrupo) return;

        $grupo = Grupo::with('alumnos')->find($this->selectGrupo);
        $alumnos = $grupo->alumnos->map(function($alumno) {
            $alumno->falto = ($alumno->falto(date('Y-m-d')) == null;
            return $alumno->only(['id', 'nombres', 'apellidos', 'falto']);
        });

        $this->grupo = $grupo->only(['id', 'alumnos']);
    }

    // Método para filtrar grupos por materia seleccionada (JUAN)
    public function updatedMateriaSeleccionada()
    {
        $this->gruposImpartidos = Auth::user()
            ->gruposImpartidos($this->año)
            ->filter(function($imparte) {
                return $imparte->materia_id == $this->materiaSeleccionada;
            })
            ->map(function($imparte) {
                return [
                    'grupo' => $imparte->grupo->only(['id', 'letra']),
                    'materia' => $imparte->materia->only(['id', 'grado', 'nombre'])
                ];
            })->values();

        if ($this->gruposImpartidos->isNotEmpty()) {
            $this->selectGrupo = $this->gruposImpartidos->first()['grupo']['id'];
            $this->cambiarGrupo();
        } else {
            $this->grupo = null;
        }
    }

    // Métodos originales
    public function faltas($key)
    {
        if ($this->grupo['alumnos'][$key]['falto']) {
            Falta::insertar($this->grupo['alumnos'][$key]['id']);
        } else {
            Falta::eliminar($this->grupo['alumnos'][$key]['id']);
        }
    }

    public function updatedSelectGrupo()
    {
        $this->cambiarGrupo();
    }

    public function updatedBuscar()
    {
        $this->palabras[] = $this->buscar;
    }
}
