<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class HorarioGrupo extends Component
{
    public $grupoId = 1; // puedes cambiarlo dinámicamente más adelante
    public $horarios = [];

    public function mount($grupoId = 1)
    {
        $this->grupoId = $grupoId;
        $this->cargarHorario();
    }

    public function cargarHorario()
    {
        $datos = DB::table('horarios')
            ->join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
            ->join('materias', 'imparte.materia_id', '=', 'materias.id')
            ->join('grupos', 'imparte.grupo_id', '=', 'grupos.id')
            ->join('maestros', 'imparte.maestro_id', '=', 'maestros.id')
            ->select(
                'horarios.hora_numero',
                'horarios.dia_semana',
                'materias.nombre as materia',
                DB::raw("CONCAT(maestros.name, ' ', maestros.apellidos) as maestro")
            )
            ->where('grupos.id', $this->grupoId)
            ->orderBy('horarios.dia_semana')
            ->orderBy('horarios.hora_numero')
            ->get();

        // Agrupar por hora y día para construir la tabla
        $this->horarios = [];
        foreach ($datos as $registro) {
            $this->horarios[$registro->hora_numero][$registro->dia_semana] = $registro;
        }
    }

    public function render()
    {
        return view('livewire.horario-grupo');
    }
}
