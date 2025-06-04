<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Grupo;
use App\Models\Inscrito;
use App\Rules\AuditoriaInscripcionesRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OwenIt\Auditing\Models\Audit;

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

    public function mount()
    {
        $this->cargarGeneraciones();
        // Establecer fechas por defecto (último mes)S
        $this->fechaFin = now()->format('Y-m-d');
        $this->fechaInicio = now()->subMonth()->format('Y-m-d');
    }

    public function cargarGeneraciones()
    {
        try {
            $this->generaciones = Grupo::select('generacion')
                ->distinct()
                ->orderBy('generacion', 'desc')
                ->pluck('generacion')
                ->toArray();
        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar generaciones: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function updatedGeneracionSeleccionada()
    {
        // Validar generación seleccionada
        $validator = Validator::make(
            ['generacionSeleccionada' => $this->generacionSeleccionada],
            ['generacionSeleccionada' => AuditoriaInscripcionesRules::generationRules()],
            AuditoriaInscripcionesRules::generationMessages()
        );

        if ($validator->fails()) {
            $this->generacionSeleccionada = '';
            $this->mensaje = $validator->errors()->first('generacionSeleccionada');
            $this->tipoMensaje = 'error';
            return;
        }

        $this->grupoSeleccionado = '';
        $this->cargarGrupos();
        $this->resetPage();
    }

    public function cargarGrupos()
    {
        try {
            $query = Grupo::query();

            if ($this->generacionSeleccionada) {
                $query->where('generacion', $this->generacionSeleccionada);
            }

            $this->grupos = $query->orderBy('generacion', 'desc')
                ->orderBy('grado')
                ->orderBy('letra')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar grupos: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function updatedGrupoSeleccionado()
    {
        // Validar grupo seleccionado
        $validator = Validator::make(
            ['grupoSeleccionado' => $this->grupoSeleccionado],
            ['grupoSeleccionado' => AuditoriaInscripcionesRules::groupRules()],
            AuditoriaInscripcionesRules::groupMessages()
        );

        if ($validator->fails()) {
            $this->grupoSeleccionado = '';
            $this->mensaje = $validator->errors()->first('grupoSeleccionado');
            $this->tipoMensaje = 'error';
            return;
        }

        // Validar que el grupo pertenezca a la generación seleccionada
        if ($this->grupoSeleccionado && $this->generacionSeleccionada) {
            if (!AuditoriaInscripcionesRules::validateGroupBelongsToGeneration(
                $this->grupoSeleccionado,
                $this->generacionSeleccionada
            )) {
                $this->grupoSeleccionado = '';
                $this->mensaje = 'El grupo seleccionado no pertenece a la generación especificada.';
                $this->tipoMensaje = 'error';
                return;
            }
        }

        $this->resetPage();
    }

    public function aplicarFiltros()
    {
        $this->cargando = true;

        // Validar filtros antes de aplicar
        $validator = Validator::make(
            [
                'generacionSeleccionada' => $this->generacionSeleccionada,
                'grupoSeleccionado' => $this->grupoSeleccionado
            ],
            AuditoriaInscripcionesRules::getFilterRules(),
            AuditoriaInscripcionesRules::getFilterMessages()
        );

        if ($validator->fails()) {
            $this->cargando = false;
            $this->mensaje = 'Error en los filtros: ' . $validator->errors()->first();
            $this->tipoMensaje = 'error';
            return;
        }

        // Validar relación grupo-generación
        if ($this->grupoSeleccionado && $this->generacionSeleccionada) {
            if (!AuditoriaInscripcionesRules::validateGroupBelongsToGeneration(
                $this->grupoSeleccionado,
                $this->generacionSeleccionada
            )) {
                $this->cargando = false;
                $this->mensaje = 'El grupo seleccionado no pertenece a la generación especificada.';
                $this->tipoMensaje = 'error';
                return;
            }
        }

        $this->resetPage();
        $this->cargando = false;
    }

    public function limpiarFiltros()
    {
        $this->generacionSeleccionada = '';
        $this->grupoSeleccionado = '';
        $this->accionFiltro = '';
        $this->fechaFin = now()->format('Y-m-d');
        $this->fechaInicio = now()->subMonth()->format('Y-m-d');
        $this->cargarGrupos();
        $this->resetPage();
    }

    public function cargarHistorialInscripciones()
    {
        try {
            // Consulta base de auditoríasS
            $query = Audit::where('auditable_type', 'App\\Models\\Inscrito');

            // Aplicar filtros de fecha
            if ($this->fechaInicio) {
                $query->whereDate('created_at', '>=', $this->fechaInicio);
            }
            if ($this->fechaFin) {
                $query->whereDate('created_at', '<=', $this->fechaFin);
            }

            // Aplicar filtro de acción (evento)
            if ($this->accionFiltro) {
                $query->where('event', $this->accionFiltro);
            }

            $auditorias = $query->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $auditorias->getCollection()->transform(function ($auditoria) {
                // Buscar el inscrito para obtener datos relacionados
                $inscrito = Inscrito::find($auditoria->auditable_id);

                if ($inscrito) {
                    // Datos del alumno
                    if ($inscrito->alumno) {
                        $auditoria->alumno_info = [
                            'matricula' => $inscrito->alumno->matricula,
                            'nombres' => $inscrito->alumno->nombres,
                            'apellidos' => $inscrito->alumno->apellidos,
                        ];
                    }

                    // Datos del grupo
                    if ($inscrito->grupo) {
                        $auditoria->grupo_info = [
                            'grado' => $inscrito->grupo->grado,
                            'letra' => $inscrito->grupo->letra,
                            'generacion' => $inscrito->grupo->generacion,
                        ];
                    }
                }

                return $auditoria;
            });

            // Aplicar filtros post-procesamiento si es necesario
            if ($this->generacionSeleccionada || $this->grupoSeleccionado) {
                $filteredCollection = $auditorias->getCollection()->filter(function ($auditoria) {
                    $cumpleFiltros = true;

                    if ($this->generacionSeleccionada && isset($auditoria->grupo_info)) {
                        $cumpleFiltros = $cumpleFiltros && ($auditoria->grupo_info['generacion'] == $this->generacionSeleccionada);
                    }

                    if ($this->grupoSeleccionado) {
                        $inscrito = Inscrito::find($auditoria->auditable_id);
                        $cumpleFiltros = $cumpleFiltros && ($inscrito && $inscrito->grupo_id == $this->grupoSeleccionado);
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
        $historialInscripciones = $this->cargarHistorialInscripciones();

        return view('livewire.reporte-inscritos', [
            'historialInscripciones' => $historialInscripciones
        ]);
    }
}
