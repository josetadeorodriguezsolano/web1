<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\AuditoriaCalificacion;
use Illuminate\Support\Facades\DB;

class ReporteCalificaciones extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propiedades para la búsqueda
    public $matriculaBusqueda = '';
    public $alumnoSeleccionado = null;
    public $sugerenciasAlumnos = [];
    public $mostrarSugerencias = false;

    // Propiedades para filtros adicionales
    public $fechaInicio = '';
    public $fechaFin = '';
    public $materiaFiltro = '';
    public $unidadFiltro = '';

    // Propiedades de estado
    public $cargando = false;
    public $mensaje = '';
    public $tipoMensaje = '';

    // Datos para mostrar
    public $historialesCalificaciones = [];
    public $materiasDisponibles = [];

    public function mount()
    {
        $this->cargarMateriasDisponibles();
        // Establecer fechas por defecto (último mes)
        $this->fechaFin = now()->format('Y-m-d');
        $this->fechaInicio = now()->subDays(30)->format('Y-m-d');
    }

    public function cargarMateriasDisponibles()
    {
        try {
            // Obtener materias que tengan calificaciones registradas
            $this->materiasDisponibles = DB::table('materias')
                ->join('calificaciones', 'materias.id', '=', 'calificaciones.materia_id')
                ->select('materias.id', 'materias.nombre')
                ->distinct()
                ->orderBy('materias.nombre')
                ->get()
                ->toArray();
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
            $this->alumnoSeleccionado = null;
        }
    }

    public function buscarAlumnosPorMatricula()
    {
        try {
            $this->sugerenciasAlumnos = Alumno::where('matricula', 'LIKE', '%' . $this->matriculaBusqueda . '%')
                ->orWhere('nombres', 'LIKE', '%' . $this->matriculaBusqueda . '%')
                ->orWhere('apellidos', 'LIKE', '%' . $this->matriculaBusqueda . '%')
                ->select('id', 'matricula', 'nombres', 'apellidos')
                ->limit(5)
                ->get()
                ->toArray();

            $this->mostrarSugerencias = count($this->sugerenciasAlumnos) > 0;
        } catch (\Exception $e) {
            $this->mensaje = 'Error en la búsqueda: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function seleccionarAlumno($alumnoId)
    {
        try {
            $alumno = Alumno::find($alumnoId);
            if ($alumno) {
                $this->alumnoSeleccionado = $alumno;
                $this->matriculaBusqueda = $alumno->matricula;
                $this->mostrarSugerencias = false;
                $this->cargarHistorialCalificaciones();
            }
        } catch (\Exception $e) {
            $this->mensaje = 'Error al seleccionar alumno: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function limpiarBusqueda()
    {
        $this->matriculaBusqueda = '';
        $this->alumnoSeleccionado = null;
        $this->sugerenciasAlumnos = [];
        $this->mostrarSugerencias = false;
        $this->historialesCalificaciones = [];
        $this->resetPage();
    }

    public function aplicarFiltros()
    {
        if ($this->alumnoSeleccionado) {
            $this->cargarHistorialCalificaciones();
        }
        $this->resetPage();
    }

    public function cargarHistorialCalificaciones()
    {
        if (!$this->alumnoSeleccionado) {
            return;
        }

        $this->cargando = true;

        try {
            // Consulta base para obtener auditorías de calificaciones
            $query = DB::table('auditoria_calificaciones as ac')
                ->leftJoin('users as u', 'ac.user_id', '=', 'u.id')
                ->leftJoin('materias as m', 'ac.materia_id', '=', 'm.id')
                ->leftJoin('calificaciones as c', 'ac.calificacion_id', '=', 'c.id')
                ->where('ac.alumno_id', $this->alumnoSeleccionado->id)
                ->select(
                    'ac.*',
                    'u.name as usuario_nombre',
                    'm.nombre as materia_nombre',
                    'c.unidad'
                );

            // Aplicar filtros de fecha
            if ($this->fechaInicio) {
                $query->whereDate('ac.created_at', '>=', $this->fechaInicio);
            }
            if ($this->fechaFin) {
                $query->whereDate('ac.created_at', '<=', $this->fechaFin);
            }

            // Aplicar filtro de materia
            if ($this->materiaFiltro) {
                $query->where('ac.materia_id', $this->materiaFiltro);
            }

            // Aplicar filtro de unidad
            if ($this->unidadFiltro) {
                $query->where('c.unidad', $this->unidadFiltro);
            }

            $this->historialesCalificaciones = $query
                ->orderBy('ac.created_at', 'desc')
                ->paginate(15)
                ->toArray();

        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar historial: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }

        $this->cargando = false;
    }

    public function render()
    {
        return view('livewire.reporte-calificaciones');
    }
}