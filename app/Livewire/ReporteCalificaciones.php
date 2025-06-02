<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Materia;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Models\Audit;

class ReporteCalificaciones extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propiedades para filtros
    public $materiaFiltro = '';
    public $unidadFiltro = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $matriculaBusqueda = '';
    
    // Propiedades para autocompletado
    public $sugerenciasAlumnos = [];
    public $mostrarSugerencias = false;

    // Propiedades de estado
    public $cargando = false;
    public $mensaje = '';
    public $tipoMensaje = '';

    // Datos para mostrar
    public $materiasDisponibles = [];

    public function mount()
    {
        $this->cargarMateriasDisponibles();
        // Establecer fechas por defecto (último mes)
        $this->fechaFin = now()->format('Y-m-d');
        $this->fechaInicio = now()->subMonth()->format('Y-m-d');
    }

    public function cargarMateriasDisponibles()
    {
        try {
            $this->materiasDisponibles = Materia::orderBy('nombre')->get()->toArray();
        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar materias: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function updatedMatriculaBusqueda()
    {
        if (strlen($this->matriculaBusqueda) >= 2) {
            $this->buscarAlumnosPorMatricula();
        } else {
            $this->sugerenciasAlumnos = [];
            $this->mostrarSugerencias = false;
        }
        $this->resetPage();
    }

    public function buscarAlumnosPorMatricula()
    {
        try {
            $busqueda = trim($this->matriculaBusqueda);
            
            $this->sugerenciasAlumnos = Alumno::where(function($query) use ($busqueda) {
                $query->where('matricula', 'LIKE', '%' . $busqueda . '%')
                      ->orWhere('nombres', 'LIKE', '%' . $busqueda . '%')
                      ->orWhere('apellidos', 'LIKE', '%' . $busqueda . '%')
                      ->orWhereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ['%' . $busqueda . '%']);
            })
            ->select('id', 'matricula', 'nombres', 'apellidos')
            ->limit(8)
            ->get()
            ->toArray();

            $this->mostrarSugerencias = count($this->sugerenciasAlumnos) > 0;
            
            // Debug para ver qué encuentra
            \Log::info('Búsqueda alumnos:', [
                'busqueda' => $busqueda,
                'encontrados' => count($this->sugerenciasAlumnos),
                'resultados' => $this->sugerenciasAlumnos
            ]);
            
        } catch (\Exception $e) {
            $this->mensaje = 'Error en la búsqueda: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function seleccionarAlumno($matricula, $nombre)
    {
        $this->matriculaBusqueda = $matricula . ' - ' . $nombre;
        $this->mostrarSugerencias = false;
        $this->resetPage();
    }

    public function limpiarBusqueda()
    {
        $this->matriculaBusqueda = '';
        $this->sugerenciasAlumnos = [];
        $this->mostrarSugerencias = false;
        $this->resetPage();
    }

    public function aplicarFiltros()
    {
        $this->cargando = true;
        $this->resetPage();
        $this->cargando = false;
    }

    public function limpiarFiltros()
    {
        $this->materiaFiltro = '';
        $this->unidadFiltro = '';
        $this->matriculaBusqueda = '';
        $this->sugerenciasAlumnos = [];
        $this->mostrarSugerencias = false;
        $this->fechaFin = now()->format('Y-m-d');
        $this->fechaInicio = now()->subMonth()->format('Y-m-d');
        $this->resetPage();
    }

    public function cargarHistorialCalificaciones()
    {
        try {
            // Consulta base de auditorías
            $query = Audit::where('auditable_type', 'App\\Models\\Calificacion');

            // Debug: ver cuántas auditorías hay
            $totalAuditorias = $query->count();
            \Log::info("Total auditorías de calificaciones: " . $totalAuditorias);

            // Aplicar filtros de fecha
            if ($this->fechaInicio) {
                $query->whereDate('created_at', '>=', $this->fechaInicio);
            }
            if ($this->fechaFin) {
                $query->whereDate('created_at', '<=', $this->fechaFin);
            }

            $auditorias = $query->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            \Log::info("Auditorías después de filtros de fecha: " . $auditorias->total());

            // Enriquecer cada auditoría con datos relacionados
            $auditorias->getCollection()->transform(function ($auditoria) {
                // Buscar la calificación para obtener datos relacionados
                $calificacion = Calificacion::find($auditoria->auditable_id);
                
                if ($calificacion) {
                    // Datos del alumno
                    if ($calificacion->alumno) {
                        $auditoria->alumno_info = [
                            'matricula' => $calificacion->alumno->matricula,
                            'nombres' => $calificacion->alumno->nombres,
                            'apellidos' => $calificacion->alumno->apellidos,
                        ];
                    }
                    
                    // Datos de la materia
                    if ($calificacion->materia) {
                        $auditoria->materia_info = [
                            'id' => $calificacion->materia->id,
                            'nombre' => $calificacion->materia->nombre,
                        ];
                    }

                    // Datos de la unidad
                    $auditoria->unidad_info = $calificacion->unidad;
                } else {
                    // Si la calificación fue eliminada, buscar en los valores de auditoría
                    $alumnoId = $auditoria->old_values['alumno_id'] ?? $auditoria->new_values['alumno_id'] ?? null;
                    $materiaId = $auditoria->old_values['materia_id'] ?? $auditoria->new_values['materia_id'] ?? null;
                    
                    if ($alumnoId) {
                        $alumno = Alumno::find($alumnoId);
                        if ($alumno) {
                            $auditoria->alumno_info = [
                                'matricula' => $alumno->matricula,
                                'nombres' => $alumno->nombres,
                                'apellidos' => $alumno->apellidos,
                            ];
                        }
                    }
                    
                    if ($materiaId) {
                        $materia = Materia::find($materiaId);
                        if ($materia) {
                            $auditoria->materia_info = [
                                'id' => $materia->id,
                                'nombre' => $materia->nombre,
                            ];
                        }
                    }

                    $auditoria->unidad_info = $auditoria->old_values['unidad'] ?? $auditoria->new_values['unidad'] ?? null;
                }
                
                // Debug: ver datos enriquecidos
                if ($this->matriculaBusqueda && strtolower($this->matriculaBusqueda) === 'tceg3612') {
                    \Log::info('Debug auditoría enriquecida:', [
                        'auditoria_id' => $auditoria->id,
                        'auditable_id' => $auditoria->auditable_id,
                        'alumno_info' => $auditoria->alumno_info ?? 'NO ENCONTRADO',
                        'materia_info' => $auditoria->materia_info ?? 'NO ENCONTRADO'
                    ]);
                }
                
                return $auditoria;
            });

            // Aplicar filtros post-procesamiento
            if ($this->materiaFiltro || $this->unidadFiltro || $this->matriculaBusqueda) {
                $filteredCollection = $auditorias->getCollection()->filter(function ($auditoria) {
                    $cumpleFiltros = true;
                    
                    // Filtro por materia
                    if ($this->materiaFiltro && isset($auditoria->materia_info)) {
                        $cumpleFiltros = $cumpleFiltros && ($auditoria->materia_info['id'] == $this->materiaFiltro);
                    }
                    
                    // Filtro por unidad
                    if ($this->unidadFiltro && isset($auditoria->unidad_info)) {
                        $cumpleFiltros = $cumpleFiltros && ($auditoria->unidad_info == $this->unidadFiltro);
                    }

                    // Filtro por matrícula/nombre - MEJORADO
                    if ($this->matriculaBusqueda && isset($auditoria->alumno_info)) {
                        $busqueda = trim(strtolower($this->matriculaBusqueda));
                        
                        // Extraer solo la matrícula si el formato es "MATRICULA - NOMBRE"
                        if (strpos($busqueda, ' - ') !== false) {
                            $busqueda = trim(explode(' - ', $busqueda)[0]);
                        }
                        
                        $matricula = strtolower($auditoria->alumno_info['matricula'] ?? '');
                        $nombres = strtolower($auditoria->alumno_info['nombres'] ?? '');
                        $apellidos = strtolower($auditoria->alumno_info['apellidos'] ?? '');
                        $nombreCompleto = $nombres . ' ' . $apellidos;
                        
                        // Buscar en matrícula exacta o parcial
                        $coincideMatricula = strpos($matricula, $busqueda) !== false;
                        
                        // Buscar en nombres (parcial)
                        $coincideNombres = strpos($nombres, $busqueda) !== false || 
                                          strpos($apellidos, $busqueda) !== false ||
                                          strpos($nombreCompleto, $busqueda) !== false;
                        
                        $cumpleFiltros = $cumpleFiltros && ($coincideMatricula || $coincideNombres);
                        
                        // Debug para ver qué está pasando
                        if ($busqueda === 'tceg3612') {
                            \Log::info('Debug búsqueda:', [
                                'busqueda' => $busqueda,
                                'matricula_bd' => $matricula,
                                'coincide' => $coincideMatricula,
                                'nombres' => $nombreCompleto,
                                'cumple_filtros' => $cumpleFiltros
                            ]);
                        }
                    }
                    
                    return $cumpleFiltros;
                });
                
                $auditorias->setCollection($filteredCollection);
            }

            return $auditorias;

        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar historial: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }
    }

    public function render()
    {
        $historialesCalificaciones = $this->cargarHistorialCalificaciones();

        return view('livewire.reporte-calificaciones', [
            'historialesCalificaciones' => $historialesCalificaciones
        ]);
    }
}