<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Grupo;
use App\Models\AuditoriaInscrito;
use Illuminate\Support\Facades\DB;

class ReporteInscritos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Propiedades para filtros
    public $generacionSeleccionada = '';
    public $grupoSeleccionado = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $accionFiltro = '';

    // Propiedades de estado
    public $cargando = false;
    public $mensaje = '';
    public $tipoMensaje = '';

    // Datos para mostrar
    public $generaciones = [];
    public $grupos = [];
    public $historialInscripciones = [];

    public function mount()
    {
        $this->cargarGeneraciones();
        // Establecer fechas por defecto (último mes)
        $this->fechaFin = now()->format('Y-m-d');
        $this->fechaInicio = now()->subDays(30)->format('Y-m-d');
    }

    public function cargarGeneraciones()
    {
        try {
            $this->generaciones = Grupo::select('generacion')
                ->distinct()
                ->orderBy('generacion', 'desc')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar generaciones: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function updatedGeneracionSeleccionada()
    {
        $this->grupoSeleccionado = '';
        $this->cargarGrupos();
        $this->resetPage();
    }

    public function cargarGrupos()
    {
        try {
            if ($this->generacionSeleccionada) {
                $this->grupos = Grupo::where('generacion', $this->generacionSeleccionada)
                    ->orderBy('grado')
                    ->orderBy('letra')
                    ->get()
                    ->toArray();
            } else {
                $this->grupos = Grupo::orderBy('generacion', 'desc')
                    ->orderBy('grado')
                    ->orderBy('letra')
                    ->get()
                    ->toArray();
            }
        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar grupos: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function updatedGrupoSeleccionado()
    {
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
        $this->generacionSeleccionada = '';
        $this->grupoSeleccionado = '';
        $this->accionFiltro = '';
        $this->fechaInicio = now()->subDays(30)->format('Y-m-d');
        $this->fechaFin = now()->format('Y-m-d');
        $this->cargarGrupos();
        $this->resetPage();
    }

    public function cargarHistorialInscripciones()
    {
        try {
            // Consulta base para obtener auditorías de inscripciones
            $query = DB::table('auditoria_inscritos as ai')
                ->leftJoin('users as u', 'ai.user_id', '=', 'u.id')
                ->leftJoin('alumnos as a', 'ai.alumno_id', '=', 'a.id')
                ->leftJoin('grupos as g', 'ai.grupo_id', '=', 'g.id')
                ->select(
                    'ai.*',
                    'u.name as usuario_nombre',
                    'a.matricula as alumno_matricula',
                    'a.nombres as alumno_nombres',
                    'a.apellidos as alumno_apellidos',
                    'g.grado as grupo_grado',
                    'g.letra as grupo_letra',
                    'g.generacion as grupo_generacion'
                );

            // Aplicar filtros de fecha
            if ($this->fechaInicio) {
                $query->whereDate('ai.created_at', '>=', $this->fechaInicio);
            }
            if ($this->fechaFin) {
                $query->whereDate('ai.created_at', '<=', $this->fechaFin);
            }

            // Aplicar filtro de generación
            if ($this->generacionSeleccionada) {
                $query->where('g.generacion', $this->generacionSeleccionada);
            }

            // Aplicar filtro de grupo
            if ($this->grupoSeleccionado) {
                $query->where('ai.grupo_id', $this->grupoSeleccionado);
            }

            // Aplicar filtro de acción
            if ($this->accionFiltro) {
                $query->where('ai.accion', $this->accionFiltro);
            }

            return $query->orderBy('ai.created_at', 'desc')
                ->paginate(15);

        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar historial: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
            return collect();
        }
    }

    public function render()
    {
        $historialInscripciones = $this->cargarHistorialInscripciones();

        return view('livewire.reporte-inscritos', [
            'historialInscripciones' => $historialInscripciones
        ]);
    }
}