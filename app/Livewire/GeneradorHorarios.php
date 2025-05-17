<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Maestro;
use App\Models\Materia;
use App\Models\Imparte;

class GeneradorHorarios extends Component
{
    public $grupos;
    public $materias;
    public $maestros;
    public $grupoSeleccionado;
    public $horario = [];
    public $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    public $horasClase = [
        '07:00 - 07:50',
        '07:50 - 08:40',
        '08:40 - 09:30',
        '09:30 - 10:20',
        '10:20 - 11:10',
        '11:10 - 12:00',
        '12:00 - 12:50'
    ];
    
    // Variables para la asignación
    public $horaSeleccionada;
    public $diaSeleccionado;
    public $materiaSeleccionada;
    public $maestroSeleccionado;
    
    public function mount()
    {
        $this->grupos = Grupo::all();
        $this->materias = Materia::all();
        $this->maestros = Maestro::all();
    }
    
    public function cargarHorario()
    {
        if (!$this->grupoSeleccionado) {
            return;
        }
        
        // Inicializar horario vacío
        $this->horario = [];
        foreach ($this->horasClase as $index => $hora) {
            foreach ($this->diasSemana as $dia) {
                $this->horario[$index + 1][$dia] = null;
            }
        }
        
        // Cargar datos existentes
        $horariosDb = Horario::join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
            ->join('materias', 'imparte.materia_id', '=', 'materias.id')
            ->join('grupos', 'imparte.grupo_id', '=', 'grupos.id')
            ->join('maestros', 'imparte.maestro_id', '=', 'maestros.id')
            ->where('grupos.id', $this->grupoSeleccionado)
            ->select(
                'horarios.hora_numero',
                'horarios.dia_semana',
                'materias.nombre as materia',
                'materias.id as materia_id',
                'maestros.id as maestro_id',
                \DB::raw('CONCAT(maestros.name, " ", maestros.apellidos) as maestro')
            )
            ->get();
            
        foreach ($horariosDb as $hora) {
            $this->horario[$hora->hora_numero][$hora->dia_semana] = [
                'materia' => $hora->materia,
                'maestro' => $hora->maestro,
                'materia_id' => $hora->materia_id,
                'maestro_id' => $hora->maestro_id
            ];
        }
    }
    
    public function asignarClase()
    {
        if (!$this->grupoSeleccionado || !$this->horaSeleccionada || !$this->diaSeleccionado || 
            !$this->materiaSeleccionada || !$this->maestroSeleccionado) {
            session()->flash('error', 'Todos los campos son requeridos');
            return;
        }
        
        // Buscar o crear registro en la tabla imparte
        $imparte = Imparte::firstOrCreate([
            'materia_id' => $this->materiaSeleccionada,
            'grupo_id' => $this->grupoSeleccionado,
            'maestro_id' => $this->maestroSeleccionado
        ]);
        
        // Crear o actualizar registro en la tabla horarios
        $horario = Horario::updateOrCreate(
            [
                'hora_numero' => $this->horaSeleccionada,
                'dia_semana' => $this->diaSeleccionado,
                'imparte_id' => $imparte->id
            ],
            [
                'imparte_id' => $imparte->id
            ]
        );
        
        // Recargar el horario
        $this->cargarHorario();
        
        // Limpiar selecciones
        $this->horaSeleccionada = null;
        $this->diaSeleccionada = null;
        $this->materiaSeleccionada = null;
        $this->maestroSeleccionada = null;
        
        session()->flash('message', 'Clase asignada correctamente');
    }
    
    public function eliminarAsignacion($hora, $dia)
    {
        $horario = Horario::where('hora_numero', $hora)
            ->where('dia_semana', $dia)
            ->whereHas('imparte', function($query) {
                $query->where('grupo_id', $this->grupoSeleccionado);
            })
            ->first();
            
        if ($horario) {
            $horario->delete();
            $this->cargarHorario();
            session()->flash('message', 'Asignación eliminada correctamente');
        }
    }
    
    public function render()
    {
        return view('livewire.generador-horarios');
    }
}