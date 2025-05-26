<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Maestro;
use App\Models\Materia;
use App\Models\Imparte;

use Barryvdh\DomPDF\Facade\Pdf;

class GeneradorHorarios extends Component
{
    public $grupos;
    public $materias;
    public $maestros;
    public $maestrosDisponibles = []; // Nueva propiedad para maestros filtrados
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
    
    // Variable para indicar la celda seleccionada
    public $celdaSeleccionada = null;
    
    public function mount()
    {
        try {
            $this->grupos = Grupo::all();
            $this->materias = collect([]); // Inicializar vacío, se cargará al seleccionar grupo
            $this->maestros = Maestro::all();
            $this->maestrosDisponibles = collect([]); // Inicializar vacío
            
            // Verificar que hay datos básicos
            if ($this->grupos->isEmpty()) {
                session()->flash('error', 'No hay grupos registrados en el sistema');
            }
            if (Materia::count() == 0) {
                session()->flash('error', 'No hay materias registradas en el sistema');
            }
            if ($this->maestros->isEmpty()) {
                session()->flash('error', 'No hay maestros registrados en el sistema');
            }
            
        } catch (\Exception $e) {
            \Log::error('Error al inicializar GeneradorHorarios: ' . $e->getMessage());
            session()->flash('error', 'Error al cargar los datos iniciales. Por favor, recargue la página.');
            
            // Inicializar con colecciones vacías para evitar errores
            $this->grupos = collect([]);
            $this->materias = collect([]);
            $this->maestros = collect([]);
            $this->maestrosDisponibles = collect([]);
        }
    }
    
    // Nuevo método para cargar materias cuando se selecciona un grupo
    public function updatedGrupoSeleccionado()
    {
        try {
            if ($this->grupoSeleccionado) {
                $grupo = Grupo::find($this->grupoSeleccionado);
                if ($grupo) {
                    // Filtrar materias por el grado del grupo seleccionado
                    $this->materias = Materia::porGrado($grupo->grado);
                    session()->flash('message', "Materias cargadas para grado {$grupo->grado}");
                } else {
                    $this->materias = collect([]);
                }
            } else {
                $this->materias = collect([]);
            }
            
            // Limpiar selecciones relacionadas
            $this->materiaSeleccionada = null;
            $this->maestroSeleccionado = null;
            $this->maestrosDisponibles = collect([]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar materias por grado: ' . $e->getMessage());
            session()->flash('error', 'Error al cargar las materias del grado seleccionado');
            $this->materias = collect([]);
        }
    }
    
    // Nuevo método para cargar maestros que pueden dar la materia seleccionada
    public function cargarMaestros()
    {
        try {
            if (!$this->materiaSeleccionada) {
                session()->flash('error', 'Debe seleccionar una materia primero');
                return;
            }
            
            // Buscar maestros que han impartido esta materia anteriormente
            $maestrosIds = Imparte::where('materia_id', $this->materiaSeleccionada)
                ->distinct()
                ->pluck('maestro_id');
            
            if ($maestrosIds->isNotEmpty()) {
                $this->maestrosDisponibles = Maestro::whereIn('id', $maestrosIds)->get();
                $countMaestros = $this->maestrosDisponibles->count();
                session()->flash('message', "Se encontraron {$countMaestros} maestro(s) que pueden impartir esta materia");
            } else {
                // Si no hay maestros que hayan impartido esta materia, mostrar todos
                $this->maestrosDisponibles = $this->maestros;
                session()->flash('message', 'No se encontraron maestros con experiencia en esta materia. Mostrando todos los maestros disponibles.');
            }
            
            // Limpiar selección de maestro
            $this->maestroSeleccionado = null;
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar maestros para materia: ' . $e->getMessage());
            session()->flash('error', 'Error al cargar los maestros disponibles para esta materia');
            $this->maestrosDisponibles = collect([]);
        }
    }
    
    // Método que se ejecuta cuando cambia la materia seleccionada
    public function updatedMateriaSeleccionada()
    {
        // Limpiar maestros disponibles y selección cuando cambia la materia
        $this->maestrosDisponibles = collect([]);
        $this->maestroSeleccionado = null;
    }
    
    public function cargarHorario()
    {
        try {
            if (!$this->grupoSeleccionado) {
                session()->flash('error', 'Debe seleccionar un grupo primero');
                return;
            }
            
            // Inicializar horario vacío
            $this->horario = [];
            foreach ($this->horasClase as $index => $hora) {
                foreach ($this->diasSemana as $dia) {
                    $this->horario[$index + 1][$dia] = null;
                }
            }
            
            // CORREGIDO: Cargar datos existentes con la consulta correcta
            $horariosDb = Horario::select(
                    'horarios.hora_numero',
                    'horarios.dia_semana',
                    'materias.nombre as materia',
                    'materias.id as materia_id',
                    'maestros.id as maestro_id',
                    \DB::raw('CONCAT(maestros.name, " ", maestros.apellidos) as maestro')
                )
                ->join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
                ->join('materias', 'imparte.materia_id', '=', 'materias.id')
                ->join('maestros', 'imparte.maestro_id', '=', 'maestros.id')
                ->join('grupos', 'imparte.grupo_id', '=', 'grupos.id')  // CORREGIDO: JOIN correcto
                ->where('imparte.grupo_id', $this->grupoSeleccionado)  // CORREGIDO: WHERE correcto
                ->get();
                
            foreach ($horariosDb as $hora) {
                $this->horario[$hora->hora_numero][$hora->dia_semana] = [
                    'materia' => $hora->materia,
                    'maestro' => $hora->maestro,
                    'materia_id' => $hora->materia_id,
                    'maestro_id' => $hora->maestro_id
                ];
            }
            
            session()->flash('message', 'Horario cargado correctamente');
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar horario: ' . $e->getMessage());
            session()->flash('error', 'Error al cargar el horario: ' . $e->getMessage());
            $this->horario = [];
        }
    }
    
    public function seleccionarCelda($hora, $dia)
{
    try {
        if (!$this->grupoSeleccionado) {
            session()->flash('error', 'Debe seleccionar un grupo primero');
            return;
        }
        
        if (!in_array($dia, $this->diasSemana)) {
            session()->flash('error', 'Día inválido seleccionado');
            return;
        }
        
        if ($hora < 1 || $hora > count($this->horasClase)) {
            session()->flash('error', 'Hora inválida seleccionada');
            return;
        }
        
        $this->horaSeleccionada = $hora;
        $this->diaSeleccionado = $dia;
        $this->celdaSeleccionada = $hora . '_' . $dia;
        
        // Si la celda ya tiene una asignación, NO permitir modificación
        if (isset($this->horario[$hora][$dia]) && $this->horario[$hora][$dia] !== null) {
            session()->flash('warning', 'Esta celda ya tiene una asignación. Para cambiarla, primero debe eliminar la asignación actual.');
            
            // Limpiar selecciones para evitar modificaciones accidentales
            $this->materiaSeleccionada = null;
            $this->maestroSeleccionado = null;
            $this->maestrosDisponibles = collect([]);
            return;
        }
        
        // Solo si la celda está vacía, permitir selecciones
        $this->materiaSeleccionada = null;
        $this->maestroSeleccionado = null;
        $this->maestrosDisponibles = collect([]);
        
        session()->flash('message', 'Celda seleccionada: ' . $this->horasClase[$hora - 1] . ' - ' . $dia);
        
    } catch (\Exception $e) {
        \Log::error('Error al seleccionar celda: ' . $e->getMessage());
        session()->flash('error', 'Error al seleccionar la celda. Por favor, intente nuevamente.');
    }
}
    
    public function asignarClase()
{
    try {
        // Validaciones más detalladas
        if (!$this->grupoSeleccionado) {
            session()->flash('error', 'Debe seleccionar un grupo');
            return;
        }
        
        if (!$this->horaSeleccionada) {
            session()->flash('error', 'Debe seleccionar una hora');
            return;
        }
        
        if (!$this->diaSeleccionado) {
            session()->flash('error', 'Debe seleccionar un día');
            return;
        }
        
        if (!$this->materiaSeleccionada) {
            session()->flash('error', 'Debe seleccionar una materia');
            return;
        }
        
        if (!$this->maestroSeleccionado) {
            session()->flash('error', 'Debe seleccionar un maestro');
            return;
        }
        
        // NUEVA VALIDACIÓN: Verificar que la celda esté vacía
        if (isset($this->horario[$this->horaSeleccionada][$this->diaSeleccionado]) && 
            $this->horario[$this->horaSeleccionada][$this->diaSeleccionado] !== null) {
            session()->flash('error', 'No se puede sobreescribir una asignación existente. Primero elimine la asignación actual.');
            return;
        }
        
        // Verificar que la materia existe
        $materia = Materia::find($this->materiaSeleccionada);
        if (!$materia) {
            session()->flash('error', 'La materia seleccionada no existe');
            return;
        }
        
        // Verificar que el maestro existe
        $maestro = Maestro::find($this->maestroSeleccionado);
        if (!$maestro) {
            session()->flash('error', 'El maestro seleccionado no existe');
            return;
        }
        
        // Verificar que el grupo existe
        $grupo = Grupo::find($this->grupoSeleccionado);
        if (!$grupo) {
            session()->flash('error', 'El grupo seleccionado no existe');
            return;
        }
        
        // Verificar que la materia corresponde al grado del grupo
        if ($materia->grado != $grupo->grado) {
            session()->flash('error', 'La materia seleccionada no corresponde al grado del grupo');
            return;
        }
        
        // Verificar conflictos de horario para el maestro
        $conflictoMaestro = Horario::join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
            ->where('horarios.hora_numero', $this->horaSeleccionada)
            ->where('horarios.dia_semana', $this->diaSeleccionado)
            ->where('imparte.maestro_id', $this->maestroSeleccionado)
            ->where('imparte.grupo_id', '!=', $this->grupoSeleccionado)
            ->exists();
            
        if ($conflictoMaestro) {
            session()->flash('error', 'El maestro ya tiene una clase asignada en este horario con otro grupo');
            return;
        }
        
        \DB::beginTransaction();
        
        // Solo crear nuevos registros, no actualizar
        $imparte = Imparte::firstOrCreate([
            'materia_id' => $this->materiaSeleccionada,
            'grupo_id' => $this->grupoSeleccionado,
            'maestro_id' => $this->maestroSeleccionado
        ]);
        
        Horario::create([
            'hora_numero' => $this->horaSeleccionada,
            'dia_semana' => $this->diaSeleccionado,
            'imparte_id' => $imparte->id
        ]);
        
        \DB::commit();
        
        // Recargar el horario
        $this->cargarHorario();
        
        session()->flash('message', "Clase asignada correctamente: {$materia->nombre} con {$maestro->name} {$maestro->apellidos}");
        
    } catch (\Illuminate\Database\QueryException $e) {
        \DB::rollBack();
        \Log::error('Error de base de datos al asignar clase: ' . $e->getMessage());
        session()->flash('error', 'Error de base de datos: ' . $e->getMessage());
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Error al asignar clase: ' . $e->getMessage());
        session()->flash('error', 'Error inesperado al asignar la clase: ' . $e->getMessage());
    }
}
    
    public function limpiarSeleccion()
    {
        $this->horaSeleccionada = null;
        $this->diaSeleccionado = null;
        $this->materiaSeleccionada = null;
        $this->maestroSeleccionado = null;
        $this->celdaSeleccionada = null;
        $this->maestrosDisponibles = collect([]);
    }
    
    public function eliminarAsignacion($hora, $dia)
    {
        try {
            if (!$this->grupoSeleccionado) {
                session()->flash('error', 'Debe seleccionar un grupo primero');
                return;
            }
            
            // CORREGIDO: Buscar el horario correctamente
            $horario = Horario::join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
                ->where('horarios.hora_numero', $hora)
                ->where('horarios.dia_semana', $dia)
                ->where('imparte.grupo_id', $this->grupoSeleccionado)
                ->select('horarios.*')
                ->first();
                
            if (!$horario) {
                session()->flash('error', 'No se encontró la asignación a eliminar');
                return;
            }
            
            \DB::beginTransaction();
            
            // Obtener información antes de eliminar para el mensaje
            $imparte = $horario->imparte;
            $materia = $imparte->materia ?? null;
            $maestro = $imparte->maestro ?? null;
            
            $horario->delete();
            
            // OPCIONAL: También eliminar el registro imparte si no tiene más horarios
            $otrosHorarios = Horario::where('imparte_id', $imparte->id)->count();
            if ($otrosHorarios == 0) {
                $imparte->delete();
            }
            
            \DB::commit();
            
            $this->cargarHorario();
            
            // Si se eliminó la celda que estaba seleccionada, actualizar los selectores
            if ($this->celdaSeleccionada === $hora . '_' . $dia) {
                $this->materiaSeleccionada = null;
                $this->maestroSeleccionado = null;
                $this->maestrosDisponibles = collect([]);
            }
            
            $mensajeMateria = $materia ? $materia->nombre : 'Clase';
            $mensajeMaestro = $maestro ? " de {$maestro->name} {$maestro->apellidos}" : '';
            session()->flash('message', "Asignación eliminada correctamente: {$mensajeMateria}{$mensajeMaestro}");
            
        } catch (\Illuminate\Database\QueryException $e) {
            \DB::rollBack();
            \Log::error('Error de base de datos al eliminar asignación: ' . $e->getMessage());
            session()->flash('error', 'Error de base de datos al eliminar la asignación: ' . $e->getMessage());
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error al eliminar asignación: ' . $e->getMessage());
            session()->flash('error', 'Error inesperado al eliminar la asignación: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        return view('livewire.generador-horarios');
    }

    public function exportarPDF()
    {
        try {
            if (!$this->grupoSeleccionado) {
                session()->flash('error', 'Primero selecciona un grupo para generar el PDF');
                return;
            }
            
            $grupo = \App\Models\Grupo::find($this->grupoSeleccionado);
            if (!$grupo) {
                session()->flash('error', 'El grupo seleccionado no existe');
                return;
            }
            
            // Verificar que hay datos en el horario
            $tieneHorarios = false;
            foreach ($this->horario as $horas) {
                foreach ($horas as $celda) {
                    if ($celda !== null) {
                        $tieneHorarios = true;
                        break 2;
                    }
                }
            }
            
            if (!$tieneHorarios) {
                session()->flash('error', 'No hay horarios asignados para generar el PDF');
                return;
            }
            
            $horarioData = [
                'grupo' => $grupo,
                'horario' => $this->horario,
                'diasSemana' => $this->diasSemana,
                'horasClase' => $this->horasClase
            ];
            
            $pdf = PDF::loadView('pdf.horario', $horarioData);
            
            session()->flash('message', 'PDF generado correctamente');
            
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, "horario_grupo_{$grupo->letra}.pdf");
            
        } catch (\Exception $e) {
            \Log::error('Error al generar PDF: ' . $e->getMessage());
            session()->flash('error', 'Error al generar el PDF: ' . $e->getMessage());
            return;
        }
    }
}