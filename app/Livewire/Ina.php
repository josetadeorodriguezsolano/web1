<?php

namespace App\Livewire;

use App\Models\Imparte;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use App\Models\Inasistencia;
use App\Models\Maestro;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Horario;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class Ina extends Component
{
    // Propiedades principales
    public $inasistencias;
    public $mostrarFormulario = false;
    public $seleccionado = -1;
    
    // Datos para el formulario
    public $maestros;
    public $materias = [];
    public $grupos;
    public $impartes;
    
    // Filtros y selecciones
    public $maestroSeleccionado = null;
    public $grupoSeleccionado = null;
    public $materiaSeleccionada = null;
    public $fechaSeleccionada;
    
    // Horarios disponibles
    public $horariosDisponibles = [];
    public $horarioSeleccionado = null;
    
    // Datos de la inasistencia
    public $inasistencia = [
        'fecha' => null,
        'imparte_id' => null,
        'horario_id' => null,
        'justificacion' => ''
    ];
    
    // Configuración de horarios
    public $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    public $horasClase = [
        1 => '07:00 - 07:50',
        2 => '07:50 - 08:40',
        3 => '08:40 - 09:30',
        4 => '09:30 - 10:20',
        5 => '10:20 - 11:10',
        6 => '11:10 - 12:00',
        7 => '12:00 - 12:50'
    ];
    
    // Estado de la UI
    public $diaCalculado = 'Lunes';
    public $cargandoHorarios = false;
    
    /**
     * Inicialización del componente
     */
    public function mount()
    {
        $this->inicializarComponente();
        $this->recargarDatos();
    }
    
    /**
     * Inicializar valores por defecto
     */
    private function inicializarComponente()
    {
        $this->inasistencia = [
            'fecha' => now()->toDateString(),
            'imparte_id' => null,
            'horario_id' => null,
            'justificacion' => ''
        ];
        
        $this->fechaSeleccionada = now()->toDateString();
        $this->calcularDiaSemana();
    }
    
    /**
     * Reglas de validación
     */
    protected function rules()
    {
        return [
            'maestroSeleccionado' => 'required',
            'grupoSeleccionado' => 'required',
            'materiaSeleccionada' => 'required',
            'horarioSeleccionado' => 'required',
            'inasistencia.fecha' => 'required|date',
            'inasistencia.justificacion' => 'nullable|string|max:1000',
        ];
    }
    
    /**
     * Mensajes de validación personalizados
     */
    protected function messages()
    {
        return [
            'maestroSeleccionado.required' => 'Seleccione un maestro.',
            'grupoSeleccionado.required' => 'Seleccione un grupo.',
            'materiaSeleccionada.required' => 'Seleccione una materia.',
            'horarioSeleccionado.required' => 'Seleccione un horario.',
            'inasistencia.fecha.required' => 'Seleccione una fecha.',
            'inasistencia.fecha.date' => 'La fecha no es válida.',
            'inasistencia.justificacion.max' => 'La justificación no puede superar los 1000 caracteres.',
        ];
    }
    
    /**
     * Listener: Cuando cambia el maestro seleccionado
     */
    public function updatedMaestroSeleccionado()
    {
        Log::info("Maestro seleccionado: {$this->maestroSeleccionado}");
        
        $this->grupoSeleccionado = null;
        $this->materiaSeleccionada = null;
        $this->horarioSeleccionado = null;
        $this->materias = [];
        $this->horariosDisponibles = [];
        
        if ($this->maestroSeleccionado) {
            $this->cargarGruposDelMaestro();
        }
    }
    
    /**
     * Listener: Cuando cambia el grupo seleccionado
     */
    public function updatedGrupoSeleccionado()
    {
        Log::info("Grupo seleccionado: {$this->grupoSeleccionado}");
        
        $this->materiaSeleccionada = null;
        $this->horarioSeleccionado = null;
        $this->horariosDisponibles = [];
        
        if ($this->grupoSeleccionado && $this->maestroSeleccionado) {
            $this->cargarMateriasDelMaestroYGrupo();
        }
    }
    
    /**
     * Listener: Cuando cambia la materia seleccionada
     */
    public function updatedMateriaSeleccionada()
    {
        Log::info("Materia seleccionada: {$this->materiaSeleccionada}");
        
        $this->horarioSeleccionado = null;
        $this->horariosDisponibles = [];
        
        if ($this->materiaSeleccionada && $this->maestroSeleccionado && $this->grupoSeleccionado) {
            $this->cargarHorariosDisponibles();
        }
    }
    
    /**
     * Listener: Cuando cambia la fecha
     */
    public function updatedFechaSeleccionada()
    {
        $this->inasistencia['fecha'] = $this->fechaSeleccionada;
        $this->calcularDiaSemana();
        
        // Recargar horarios si ya hay selecciones
        if ($this->materiaSeleccionada && $this->maestroSeleccionado && $this->grupoSeleccionado) {
            $this->cargarHorariosDisponibles();
        }
    }
    
    /**
     * Calcular el día de la semana basado en la fecha
     */
    private function calcularDiaSemana()
    {
        if (!$this->fechaSeleccionada) {
            $this->diaCalculado = 'Lunes';
            return;
        }
        
        try {
            $fecha = Carbon::createFromFormat('Y-m-d', $this->fechaSeleccionada);
            $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
            $diaIndex = (int)$fecha->format('w');
            
            // Ajustar fines de semana
            if ($diaIndex === 6) { // Sábado -> Viernes
                $this->diaCalculado = 'Viernes';
            } elseif ($diaIndex === 0) { // Domingo -> Lunes
                $this->diaCalculado = 'Lunes';
            } else {
                $this->diaCalculado = $dias[$diaIndex];
            }
            
            Log::info("Día calculado: {$this->diaCalculado} para fecha: {$this->fechaSeleccionada}");
            
        } catch (\Exception $e) {
            $this->diaCalculado = 'Lunes';
            Log::warning('Error al calcular día de la semana: ' . $e->getMessage());
        }
    }
    
    /**
     * Cargar grupos donde enseña el maestro seleccionado
     */
    private function cargarGruposDelMaestro()
    {
        try {
            if (!$this->maestroSeleccionado) {
                return;
            }
            
            // Obtener grupos únicos donde el maestro imparte clases
            $gruposIds = Imparte::where('maestro_id', $this->maestroSeleccionado)
                ->distinct()
                ->pluck('grupo_id');
            
            $this->grupos = Grupo::whereIn('id', $gruposIds)
                ->orderBy('grado')
                ->orderBy('letra')
                ->get();
                
            Log::info("Grupos cargados para maestro {$this->maestroSeleccionado}: " . $this->grupos->count());
            
        } catch (\Exception $e) {
            Log::error('Error al cargar grupos del maestro: ' . $e->getMessage());
            Session::flash('error', 'Error al cargar los grupos del maestro.');
        }
    }
    
    /**
     * Cargar materias que enseña el maestro en el grupo seleccionado
     */
    private function cargarMateriasDelMaestroYGrupo()
    {
        try {
            if (!$this->maestroSeleccionado || !$this->grupoSeleccionado) {
                $this->materias = collect([]);
                return;
            }
            
            $materiasIds = Imparte::where('maestro_id', $this->maestroSeleccionado)
                ->where('grupo_id', $this->grupoSeleccionado)
                ->pluck('materia_id');
            
            $this->materias = Materia::whereIn('id', $materiasIds)
                ->orderBy('nombre')
                ->get();
                
            Log::info("Materias cargadas: " . $this->materias->count());
            
        } catch (\Exception $e) {
            Log::error('Error al cargar materias: ' . $e->getMessage());
            Session::flash('error', 'Error al cargar las materias.');
            $this->materias = collect([]);
        }
    }
    
    /**
     * Cargar horarios disponibles para la combinación seleccionada
     */
    private function cargarHorariosDisponibles()
    {
        try {
            $this->cargandoHorarios = true;
            
            if (!$this->maestroSeleccionado || !$this->grupoSeleccionado || !$this->materiaSeleccionada) {
                $this->horariosDisponibles = [];
                return;
            }
            
            // Obtener el registro imparte
            $imparte = Imparte::where('maestro_id', $this->maestroSeleccionado)
                ->where('grupo_id', $this->grupoSeleccionado)
                ->where('materia_id', $this->materiaSeleccionada)
                ->first();
            
            if (!$imparte) {
                Log::warning('No se encontró registro imparte para la combinación seleccionada');
                $this->horariosDisponibles = [];
                return;
            }
            
            // Obtener horarios programados para esta combinación en el día calculado
            $horariosProgramados = Horario::where('imparte_id', $imparte->id)
                ->where('dia_semana', $this->diaCalculado)
                ->get();
            
            Log::info("Horarios programados encontrados: " . $horariosProgramados->count());
            
            // Verificar cuáles ya tienen inasistencia registrada en la fecha seleccionada
            $this->horariosDisponibles = $horariosProgramados->map(function($horario) use ($imparte) {
                $yaRegistrada = Inasistencia::where('fecha', $this->fechaSeleccionada)
                    ->where('horario_id', $horario->id)
                    ->where('imparte_id', $imparte->id)
                    ->exists();
                
                return [
                    'id' => $horario->id,
                    'imparte_id' => $imparte->id,
                    'hora_numero' => $horario->hora_numero,
                    'dia_semana' => $horario->dia_semana,
                    'hora_texto' => $this->horasClase[$horario->hora_numero] ?? 'Hora desconocida',
                    'ya_registrada' => $yaRegistrada,
                    'disponible' => !$yaRegistrada
                ];
            })->toArray();
            
            Log::info("Horarios disponibles procesados: " . count($this->horariosDisponibles));
            
        } catch (\Exception $e) {
            Log::error('Error al cargar horarios disponibles: ' . $e->getMessage());
            Session::flash('error', 'Error al cargar los horarios disponibles.');
            $this->horariosDisponibles = [];
        } finally {
            $this->cargandoHorarios = false;
        }
    }
    
    /**
     * Agregar nueva inasistencia
     */
    public function agregar()
    {
        $this->mostrarFormulario = true;
        $this->seleccionado = -1;
        $this->inicializarComponente();
        
        // Limpiar selecciones
        $this->maestroSeleccionado = null;
        $this->grupoSeleccionado = null;
        $this->materiaSeleccionada = null;
        $this->horarioSeleccionado = null;
        $this->materias = [];
        $this->horariosDisponibles = [];
    }
    
    /**
     * Modificar inasistencia existente
     */
    public function modificar()
    {
        if ($this->seleccionado == -1 || !isset($this->inasistencias[$this->seleccionado])) {
            Session::flash('error', 'Debe seleccionar una inasistencia para modificar.');
            return;
        }
        
        $this->mostrarFormulario = true;
        $inasistenciaSeleccionada = $this->inasistencias[$this->seleccionado];
        
        // Cargar datos de la inasistencia
        $this->inasistencia = [
            'id' => $inasistenciaSeleccionada->id,
            'fecha' => $inasistenciaSeleccionada->fecha,
            'imparte_id' => $inasistenciaSeleccionada->imparte_id,
            'horario_id' => $inasistenciaSeleccionada->horario_id,
            'justificacion' => $inasistenciaSeleccionada->justificacion ?? ''
        ];
        
        $this->fechaSeleccionada = $inasistenciaSeleccionada->fecha;
        
        // Cargar datos relacionados
        $imparte = $inasistenciaSeleccionada->imparte;
        if ($imparte) {
            $this->maestroSeleccionado = $imparte->maestro_id;
            $this->grupoSeleccionado = $imparte->grupo_id;
            $this->materiaSeleccionada = $imparte->materia_id;
            $this->horarioSeleccionado = $inasistenciaSeleccionada->horario_id;
            
            // Cargar datos en cascada
            $this->cargarGruposDelMaestro();
            $this->cargarMateriasDelMaestroYGrupo();
            $this->calcularDiaSemana();
            $this->cargarHorariosDisponibles();
        }
    }
    
    /**
     * Guardar inasistencia
     */
    public function guardar()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            Session::flash('error', $e->validator->errors()->first());
            return;
        }
        
        try {
            // Obtener el registro imparte
            $imparte = Imparte::where('maestro_id', $this->maestroSeleccionado)
                ->where('grupo_id', $this->grupoSeleccionado)
                ->where('materia_id', $this->materiaSeleccionada)
                ->first();
            
            if (!$imparte) {
                Session::flash('error', 'No se encontró la asignación maestro-materia-grupo.');
                return;
            }
            
            $data = [
                'imparte_id' => $imparte->id,
                'horario_id' => $this->horarioSeleccionado,
                'fecha' => $this->fechaSeleccionada,
                'justificacion' => $this->inasistencia['justificacion'] ?? ''
            ];
            
            // Verificar duplicados
            $query = Inasistencia::where('fecha', $data['fecha'])
                ->where('horario_id', $data['horario_id'])
                ->where('imparte_id', $data['imparte_id']);
            
            // Si estamos editando, excluir el registro actual
            if (isset($this->inasistencia['id'])) {
                $query->where('id', '!=', $this->inasistencia['id']);
            }
            
            if ($query->exists()) {
                Session::flash('error', 'Ya existe una inasistencia registrada para este horario y fecha.');
                return;
            }
            
            // Crear o actualizar
            if (isset($this->inasistencia['id'])) {
                $inasistencia = Inasistencia::find($this->inasistencia['id']);
                if ($inasistencia) {
                    $inasistencia->update($data);
                    Session::flash('success', 'Inasistencia actualizada correctamente.');
                } else {
                    Session::flash('error', 'No se encontró la inasistencia a actualizar.');
                    return;
                }
            } else {
                Inasistencia::create($data);
                Session::flash('success', 'Inasistencia registrada correctamente.');
            }
            
            $this->cancelar();
            $this->recargarDatos();
            
        } catch (\Exception $e) {
            Log::error('Error al guardar inasistencia: ' . $e->getMessage());
            Session::flash('error', 'Error al guardar la inasistencia: ' . $e->getMessage());
        }
    }
    
    /**
     * Eliminar inasistencia
     */
    public function eliminar()
    {
        try {
            if ($this->seleccionado == -1) {
                Session::flash('error', 'Debe seleccionar una inasistencia para eliminar.');
                return;
            }
            
            $inasistenciaSeleccionada = $this->inasistencias[$this->seleccionado] ?? null;
            
            if (!$inasistenciaSeleccionada) {
                Session::flash('error', 'No se encontró la inasistencia seleccionada.');
                return;
            }
            
            $inasistencia = Inasistencia::find($inasistenciaSeleccionada->id);
            
            if ($inasistencia) {
                $inasistencia->delete();
                $this->seleccionado = -1;
                $this->recargarDatos();
                Session::flash('success', 'Inasistencia eliminada correctamente.');
            } else {
                Session::flash('error', 'No se encontró la inasistencia en la base de datos.');
            }
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar inasistencia: ' . $e->getMessage());
            Session::flash('error', 'Ocurrió un error al eliminar la inasistencia.');
        }
    }
    
    /**
     * Generar PDF de inasistencias
     */
    public function generarPDF()
{
    try {
        // Verificar que hay datos
        if (!$this->inasistencias || $this->inasistencias->isEmpty()) {
            Session::flash('error', 'No hay inasistencias registradas para generar el PDF.');
            return;
        }

        // Preparar datos con verificación adicional
        $inasistenciasParaPDF = $this->inasistencias->map(function($inasistencia) {
            return [
                'id' => $inasistencia->id,
                'fecha' => $inasistencia->fecha,
                'maestro' => $inasistencia->imparte->maestro->name ?? 'Sin maestro',
                'maestro_apellidos' => $inasistencia->imparte->maestro->apellidos ?? '',
                'materia' => $inasistencia->imparte->materia->nombre ?? 'Sin materia',
                'grupo' => ($inasistencia->imparte->grupo->grado ?? '') . 
                          ($inasistencia->imparte->grupo->letra ?? ''),
                'horario' => $this->obtenerTextoHorario($inasistencia->horario),
                'justificacion' => $inasistencia->justificacion ?? 'Sin justificación'
            ];
        });

        $data = [
            'inasistencias' => $inasistenciasParaPDF,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
            'titulo' => 'Reporte de Inasistencias'
        ];

        // Verificar que la vista existe
        if (!view()->exists('pdf.inasistencias')) {
            Session::flash('error', 'La plantilla PDF no existe. Debe crear la vista pdf.inasistencias');
            return;
        }

        // Generar PDF con configuración específica
        $pdf = Pdf::loadView('pdf.inasistencias', $data)
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont' => 'sans-serif',
                      'isHtml5ParserEnabled' => true,
                      'isPhpEnabled' => true
                  ]);

        $nombreArchivo = 'inasistencias_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $nombreArchivo, [
            'Content-Type' => 'application/pdf',
        ]);

    } catch (\Exception $e) {
        Log::error('Error detallado al generar PDF: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        Session::flash('error', 'Error al generar el PDF: ' . $e->getMessage());
        return;
    }
}
    
    /**
     * Cancelar operación
     */
    public function cancelar()
    {
        $this->mostrarFormulario = false;
        $this->seleccionado = -1;
        $this->inicializarComponente();
        
        // Limpiar todas las selecciones
        $this->maestroSeleccionado = null;
        $this->grupoSeleccionado = null;
        $this->materiaSeleccionada = null;
        $this->horarioSeleccionado = null;
        $this->materias = [];
        $this->horariosDisponibles = [];
    }
    
    /**
     * Seleccionar inasistencia de la tabla
     */
    public function seleccionarInasistencia($index)
    {
        $this->seleccionado = $index;
    }
    
    /**
     * Recargar datos del componente
     */
    private function recargarDatos()
    {
        try {
            $this->inasistencias = Inasistencia::with([
                'imparte.maestro', 
                'imparte.materia', 
                'imparte.grupo', 
                'horario'
            ])->orderBy('fecha', 'desc')
              ->orderBy('created_at', 'desc')
              ->get();
            
            $this->maestros = Maestro::orderBy('name')
                ->orderBy('apellidos')
                ->get();
            
            $this->grupos = Grupo::orderBy('grado')
                ->orderBy('letra')
                ->get();
            
        } catch (\Exception $e) {
            Log::error('Error al recargar datos: ' . $e->getMessage());
            Session::flash('error', 'Error al cargar los datos.');
            
            // Valores por defecto en caso de error
            $this->inasistencias = collect([]);
            $this->maestros = collect([]);
            $this->grupos = collect([]);
        }
    }
    
    /**
     * Obtener texto descriptivo del horario
     */
    public function obtenerTextoHorario($horario)
    {
        if (!$horario) return 'Sin horario';
        
        $horaTexto = $this->horasClase[$horario->hora_numero] ?? 'Hora desconocida';
        return "{$horario->dia_semana} - {$horaTexto}";
    }
    
    /**
     * Renderizar la vista
     */
    public function render()
    {
        return view('livewire.registrar-inasistencias');
    }
}