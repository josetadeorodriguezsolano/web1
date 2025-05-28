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
    public $maestroAutoSeleccionado = false; 
    public $maestroOptimo = null; 
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
    public $maestroNombre = '';
    
    // Variable para indicar la celda seleccionada
    public $celdaSeleccionada = null;
    
    public function mount()
    {
        try {
            $this->grupos = Grupo::all();
            $this->materias = collect([]);
            $this->maestros = Maestro::all();
            
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
            
            $this->grupos = collect([]);
            $this->materias = collect([]);
            $this->maestros = collect([]);
        }
    }
    
    public function updatedGrupoSeleccionado()
    {
        try {
            if ($this->grupoSeleccionado) {
                $grupo = Grupo::find($this->grupoSeleccionado);
                if ($grupo) {
                    $this->materias = Materia::porGrado($grupo->grado);
                    session()->flash('message', "Materias cargadas para grado {$grupo->grado}");
                } else {
                    $this->materias = collect([]);
                }
            } else {
                $this->materias = collect([]);
            }
            
            // Limpiar selecciones relacionadas
            $this->limpiarSeleccion();
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar materias por grado: ' . $e->getMessage());
            session()->flash('error', 'Error al cargar las materias del grado seleccionado');
            $this->materias = collect([]);
        }
    }

    // ✅ SOLUCIÓN AL PROBLEMA 1: Actualizar maestro automáticamente cuando se selecciona materia
    public function updatedMateriaSeleccionada()
    {
        \Log::info('=== updatedMateriaSeleccionada EJECUTADO ===');
        \Log::info("Materia seleccionada: {$this->materiaSeleccionada}");
        \Log::info("Hora seleccionada: {$this->horaSeleccionada}");
        \Log::info("Día seleccionado: {$this->diaSeleccionado}");
        
        // Limpiar maestro anterior
        $this->maestroSeleccionado = null;
        $this->maestroNombre = '';
        $this->maestroAutoSeleccionado = false;
        $this->maestroOptimo = null;
        
        // Si hay materia, hora y día seleccionados, cargar maestro automáticamente
        if ($this->materiaSeleccionada && $this->horaSeleccionada && $this->diaSeleccionado) {
            \Log::info('Condiciones cumplidas, cargando maestros automáticamente...');
            $this->cargarMaestros();
        }
        
        // Forzar actualización de la vista
        $this->dispatch('maestro-updated');
    }
    
    public function cargarMaestros()
{
   try {
       \Log::info('=== INICIANDO cargarMaestros ===');
       \Log::info("Materia: {$this->materiaSeleccionada}");
       \Log::info("Hora: {$this->horaSeleccionada}");
       \Log::info("Día: {$this->diaSeleccionado}");
       
       if (!$this->materiaSeleccionada) {
           session()->flash('error', 'Debe seleccionar una materia primero');
           return;
       }
       
       if (!$this->horaSeleccionada || !$this->diaSeleccionado) {
           session()->flash('error', 'Debe seleccionar una hora y día primero');
           return;
       }
       
       // Auto-seleccionar maestro basado en criterios
       $maestroOptimo = $this->seleccionarMaestroOptimo($this->materiaSeleccionada);
       
       if ($maestroOptimo) {
           // ✅ SOLUCIÓN AL PROBLEMA 2: Asegurar consistencia de datos
           $this->maestroSeleccionado = $maestroOptimo->id;
           $this->maestroNombre = $maestroOptimo->name . ' ' . $maestroOptimo->apellidos;
           $this->maestroAutoSeleccionado = true;
           $this->maestroOptimo = $maestroOptimo;
           
           // ✅ NUEVO: Forzar actualización del componente select
           $this->dispatch('actualizarMaestroSelector', [
               'maestroId' => $this->maestroSeleccionado,
               'maestroNombre' => $this->maestroNombre
           ]);
           
           \Log::info("=== MAESTRO ÓPTIMO SELECCIONADO CORRECTAMENTE ===");
           \Log::info("ID asignado: {$this->maestroSeleccionado}");
           \Log::info("Nombre asignado: {$this->maestroNombre}");
           \Log::info("Nombre real: {$maestroOptimo->name} {$maestroOptimo->apellidos}");
           \Log::info("Coinciden: " . ($this->maestroNombre === ($maestroOptimo->name . ' ' . $maestroOptimo->apellidos) ? 'SÍ' : 'NO'));
           
           session()->flash('message', "Maestro óptimo seleccionado automáticamente: {$this->maestroNombre}");
           
           // Forzar actualización de la vista
           $this->dispatch('maestro-selected', [
               'maestroId' => $this->maestroSeleccionado,
               'maestroNombre' => $this->maestroNombre
           ]);
           
       } else {
           $this->maestroSeleccionado = null;
           $this->maestroNombre = '';
           $this->maestroAutoSeleccionado = false;
           $this->maestroOptimo = null;
           session()->flash('error', 'No se encontró un maestro disponible para esta materia en este horario');
       }
       
       \Log::info('=== cargarMaestros COMPLETADO ===');
       
   } catch (\Exception $e) {
       \Log::error('Error al cargar maestro: ' . $e->getMessage());
       session()->flash('error', 'Error al seleccionar maestro automáticamente');
       $this->maestroSeleccionado = null;
       $this->maestroNombre = '';
       $this->maestroAutoSeleccionado = false;
       $this->maestroOptimo = null;
   }
}

    public function updatedMaestroSeleccionado()
    {
        \Log::info('=== updatedMaestroSeleccionado EJECUTADO ===');
        \Log::info("Nuevo maestro seleccionado: {$this->maestroSeleccionado}");
        \Log::info("Auto-seleccionado actualmente: " . ($this->maestroAutoSeleccionado ? 'SÍ' : 'NO'));
        
        if ($this->maestroSeleccionado && !$this->maestroAutoSeleccionado) {
            // Usuario seleccionó manualmente
            $maestro = Maestro::find($this->maestroSeleccionado);
            if ($maestro) {
                $this->maestroNombre = $maestro->name . ' ' . $maestro->apellidos;
                \Log::info("Usuario seleccionó manualmente: {$this->maestroNombre}");
            }
        }
        
        // Reset del flag de auto-selección si el usuario cambió la selección
        if ($this->maestroAutoSeleccionado && $this->maestroOptimo && 
            $this->maestroSeleccionado != $this->maestroOptimo->id) {
            \Log::info("Usuario cambió selección automática, reseteando flag");
            $this->maestroAutoSeleccionado = false;
            $this->maestroOptimo = null;
        }
    }
    
    private function seleccionarMaestroOptimo($materiaId)
    {
        try {
            \Log::info("=== BUSCANDO MAESTRO ÓPTIMO ===");
            \Log::info("Materia ID: {$materiaId}");
            \Log::info("Grupo ID: {$this->grupoSeleccionado}");
            \Log::info("Horario: {$this->horaSeleccionada} - {$this->diaSeleccionado}");
            
            // 1. Verificar si ya hay un maestro asignado a esta materia para este grupo
            $maestroExistente = Imparte::where('materia_id', $materiaId)
                ->where('grupo_id', $this->grupoSeleccionado)
                ->first();
            
            if ($maestroExistente) {
                $maestro = Maestro::find($maestroExistente->maestro_id);
                \Log::info("Maestro existente encontrado: " . ($maestro ? $maestro->name : 'NULL'));
                
                if ($maestro && !$this->maestroOcupadoEnHorario($maestro->id, $this->horaSeleccionada, $this->diaSeleccionado)) {
                    \Log::info("Manteniendo maestro existente: {$maestro->name} {$maestro->apellidos}");
                    return $maestro;
                }
                
                \Log::info("Maestro existente está ocupado, buscando alternativas");
            }
            
            // 2. Buscar maestros que pueden dar esta materia
            $maestrosDisponibles = Maestro::whereHas('imparte', function($query) use ($materiaId) {
                $query->where('materia_id', $materiaId);
            })->get();
            
            \Log::info("Maestros con experiencia en esta materia: " . $maestrosDisponibles->count());
            
            // 3. Si no hay maestros con experiencia, usar todos
            if ($maestrosDisponibles->isEmpty()) {
                $maestrosDisponibles = $this->maestros;
                \Log::info("Sin maestros con experiencia, usando todos: " . $maestrosDisponibles->count());
            }
            
            // 4. Filtrar maestros libres en este horario
            $maestrosLibres = $maestrosDisponibles->filter(function($maestro) {
                $ocupado = $this->maestroOcupadoEnHorario($maestro->id, $this->horaSeleccionada, $this->diaSeleccionado);
                \Log::info("Maestro {$maestro->name}: " . ($ocupado ? 'OCUPADO' : 'LIBRE'));
                return !$ocupado;
            });
            
            \Log::info("Maestros libres encontrados: " . $maestrosLibres->count());
            
            if ($maestrosLibres->isEmpty()) {
                \Log::info("No hay maestros disponibles");
                return null;
            }
            
            // 5. Priorizar maestros con experiencia en esta materia
            $maestrosConExperiencia = $maestrosLibres->filter(function($maestro) use ($materiaId) {
                return Imparte::where('maestro_id', $maestro->id)
                    ->where('materia_id', $materiaId)
                    ->exists();
            });
            
            if ($maestrosConExperiencia->isNotEmpty()) {
                $maestroOptimo = $maestrosConExperiencia->sortBy(function($maestro) {
                    return Imparte::where('maestro_id', $maestro->id)->count();
                })->first();
                
                \Log::info("Maestro óptimo con experiencia: {$maestroOptimo->name} {$maestroOptimo->apellidos}");
                return $maestroOptimo;
            }
            
            // 6. Si no hay maestros con experiencia, elegir el de menos carga
            $maestroOptimo = $maestrosLibres->sortBy(function($maestro) {
                return Imparte::where('maestro_id', $maestro->id)->count();
            })->first();
            
            \Log::info("Maestro óptimo sin experiencia: " . ($maestroOptimo ? $maestroOptimo->name . ' ' . $maestroOptimo->apellidos : 'NULL'));
            return $maestroOptimo;
            
        } catch (\Exception $e) {
            \Log::error('Error en seleccionarMaestroOptimo: ' . $e->getMessage());
            return null;
        }
    }
    
    private function maestroOcupadoEnHorario($maestroId, $hora, $dia)
    {
        if (!$hora || !$dia) {
            return false;
        }
        
        $ocupado = Horario::join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
            ->where('horarios.hora_numero', $hora)
            ->where('horarios.dia_semana', $dia)
            ->where('imparte.maestro_id', $maestroId)
            ->exists();
            
        \Log::info("Maestro {$maestroId} en {$hora}-{$dia}: " . ($ocupado ? 'OCUPADO' : 'LIBRE'));
        return $ocupado;
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
            
            // Cargar datos existentes
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
                ->where('imparte.grupo_id', $this->grupoSeleccionado)
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
            
            \Log::info("=== SELECCIONANDO CELDA ===");
            \Log::info("Hora: {$hora}, Día: {$dia}");
            
            $this->horaSeleccionada = $hora;
            $this->diaSeleccionado = $dia;
            $this->celdaSeleccionada = $hora . '_' . $dia;
            
            // Limpiar selecciones de materia y maestro
            $this->materiaSeleccionada = null;
            $this->maestroSeleccionado = null;
            $this->maestroNombre = '';
            $this->maestroAutoSeleccionado = false;
            $this->maestroOptimo = null;
            
            session()->flash('message', 'Celda seleccionada: ' . $this->horasClase[$hora - 1] . ' - ' . $dia);
            
        } catch (\Exception $e) {
            \Log::error('Error al seleccionar celda: ' . $e->getMessage());
            session()->flash('error', 'Error al seleccionar la celda. Por favor, intente nuevamente.');
        }
    }
    
    public function asignarClase()
    {   
        try {
            \Log::info('=== INICIANDO ASIGNACIÓN DE CLASE ===');
            \Log::info('Maestro seleccionado ID: ' . $this->maestroSeleccionado);
            \Log::info('Maestro nombre: ' . $this->maestroNombre);
            \Log::info('Auto-seleccionado: ' . ($this->maestroAutoSeleccionado ? 'SÍ' : 'NO'));

            // Validaciones básicas
            if (!$this->grupoSeleccionado) {
                session()->flash('error', 'Debe seleccionar un grupo');
                return;
            }
            
            if (!$this->horaSeleccionada || !$this->diaSeleccionado) {
                session()->flash('error', 'Debe seleccionar una hora y día');
                return;
            }
            
            if (!$this->materiaSeleccionada) {
                session()->flash('error', 'Debe seleccionar una materia');
                return;
            }
            
            // Si no hay maestro seleccionado, intentar selección automática
            if (!$this->maestroSeleccionado) {
                \Log::info('No hay maestro seleccionado, intentando selección automática...');
                $this->cargarMaestros();
                
                if (!$this->maestroSeleccionado) {
                    session()->flash('error', 'No se pudo asignar un maestro para esta materia y horario');
                    return;
                }
            }
            
            // ✅ VERIFICACIÓN CRÍTICA: Validar que el maestro existe y los datos coinciden
            $maestroReal = Maestro::find($this->maestroSeleccionado);
            if (!$maestroReal) {
                \Log::error("MAESTRO NO ENCONTRADO EN BD: ID {$this->maestroSeleccionado}");
                session()->flash('error', 'El maestro seleccionado no existe en la base de datos');
                return;
            }

            $nombreReal = $maestroReal->name . ' ' . $maestroReal->apellidos;
            
            // ✅ CORRECCIÓN AUTOMÁTICA DE INCONSISTENCIAS
            if ($this->maestroNombre !== $nombreReal) {
                \Log::warning("CORRIGIENDO INCONSISTENCIA:");
                \Log::warning("Nombre mostrado: '{$this->maestroNombre}'");
                \Log::warning("Nombre real: '{$nombreReal}'");
                $this->maestroNombre = $nombreReal;
            }
            
            \Log::info("DATOS FINALES PARA ASIGNACIÓN:");
            \Log::info("ID: {$this->maestroSeleccionado}");
            \Log::info("Nombre: {$this->maestroNombre}");
            
            // Validar otros datos
            $materia = Materia::find($this->materiaSeleccionada);
            $grupo = Grupo::find($this->grupoSeleccionado);
            
            if (!$materia || !$grupo) {
                \Log::error('Datos inválidos - Materia: ' . ($materia ? 'OK' : 'NULL') . 
                           ', Grupo: ' . ($grupo ? 'OK' : 'NULL'));
                session()->flash('error', 'Datos inválidos seleccionados');
                return;
            }

            // Verificar que la materia corresponde al grado del grupo
            if ($materia->grado != $grupo->grado) {
                session()->flash('error', 'La materia seleccionada no corresponde al grado del grupo');
                return;
            }
            
            // Verificar disponibilidad del maestro (por si cambió)
            if ($this->maestroOcupadoEnHorario($this->maestroSeleccionado, $this->horaSeleccionada, $this->diaSeleccionado)) {
                session()->flash('error', 'El maestro seleccionado ya no está disponible en este horario');
                
                // Intentar encontrar otro maestro automáticamente
                $nuevoMaestro = $this->seleccionarMaestroOptimo($this->materiaSeleccionada);
                if ($nuevoMaestro && $nuevoMaestro->id != $this->maestroSeleccionado) {
                    $this->maestroSeleccionado = $nuevoMaestro->id;
                    $this->maestroNombre = $nuevoMaestro->name . ' ' . $nuevoMaestro->apellidos;
                    session()->flash('message', "Maestro reasignado automáticamente: {$this->maestroNombre}");
                } else {
                    return;
                }
            }
            
            \DB::beginTransaction();
            
            // Buscar o crear registro Imparte
            $imparteExistente = Imparte::where('materia_id', $this->materiaSeleccionada)
                ->where('grupo_id', $this->grupoSeleccionado)
                ->first();
            
            $mensaje = '';
            
            if ($imparteExistente) {
                if ($imparteExistente->maestro_id == $this->maestroSeleccionado) {
                    $imparte = $imparteExistente;
                    $mensaje = "Manteniendo maestro existente: {$this->maestroNombre} para {$materia->nombre}";
                } else {
                    $maestroAnterior = Maestro::find($imparteExistente->maestro_id);
                    $nombreAnterior = $maestroAnterior ? $maestroAnterior->name . ' ' . $maestroAnterior->apellidos : 'maestro anterior';
                    
                    $imparteExistente->update(['maestro_id' => $this->maestroSeleccionado]);
                    $imparte = $imparteExistente;
                    $mensaje = "Maestro actualizado: {$nombreAnterior} → {$this->maestroNombre} para {$materia->nombre}";
                }
            } else {
                $imparte = Imparte::create([
                    'materia_id' => $this->materiaSeleccionada,
                    'grupo_id' => $this->grupoSeleccionado,
                    'maestro_id' => $this->maestroSeleccionado
                ]);
                $mensaje = "Nueva asignación creada: {$materia->nombre} con {$this->maestroNombre}";
            }
            
            // Crear o actualizar horario
            $horarioExistente = Horario::join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
                ->where('horarios.hora_numero', $this->horaSeleccionada)
                ->where('horarios.dia_semana', $this->diaSeleccionado)
                ->where('imparte.grupo_id', $this->grupoSeleccionado)
                ->select('horarios.*')
                ->first();
            
            if ($horarioExistente) {
                $horarioExistente->update(['imparte_id' => $imparte->id]);
            } else {
                Horario::create([
                    'hora_numero' => $this->horaSeleccionada,
                    'dia_semana' => $this->diaSeleccionado,
                    'imparte_id' => $imparte->id
                ]);
            }
            
            \DB::commit();
            
            \Log::info('=== ASIGNACIÓN COMPLETADA EXITOSAMENTE ===');
            \Log::info("Maestro final: ID {$this->maestroSeleccionado} - {$this->maestroNombre}");
            
            // Recargar el horario
            $this->cargarHorario();
            
            session()->flash('message', $mensaje . " - Horario: {$this->horasClase[$this->horaSeleccionada - 1]} - {$this->diaSeleccionado}");
            
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
        $this->maestroNombre = '';
        $this->maestroAutoSeleccionado = false; 
        $this->maestroOptimo = null;
        $this->celdaSeleccionada = null;
    }
    
    public function eliminarAsignacion($hora, $dia)
    {
        try {
            if (!$this->grupoSeleccionado) {
                session()->flash('error', 'Debe seleccionar un grupo primero');
                return;
            }
            
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
            
            $imparte = $horario->imparte;
            $materia = $imparte->materia ?? null;
            $maestro = $imparte->maestro ?? null;
            
            $horario->delete();
            
            // Eliminar registro imparte si no tiene más horarios
            $otrosHorarios = Horario::where('imparte_id', $imparte->id)->count();
            if ($otrosHorarios == 0) {
                $imparte->delete();
            }
            
            \DB::commit();
            
            $this->cargarHorario();
            
            // Si se eliminó la celda seleccionada, limpiar selecciones
            if ($this->celdaSeleccionada === $hora . '_' . $dia) {
                $this->limpiarSeleccion();
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