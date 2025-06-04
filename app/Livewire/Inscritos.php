<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inscrito;
use App\Models\Grupo;
use App\Rules\InscritosRules;
use Illuminate\Support\Facades\Validator;

class Inscritos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $generacionSeleccionada = '';
    public $grupoSeleccionado = '';

    // Cambio del modal de eliminación a modal de cambio de estatus
    public $mostrarModalCambiarEstatus = false;
    public $idCambiarEstatus = null;
    public $cargando = false;

    public $estatusSeleccionado = '';

    public function updatedEstatusSeleccionado()
    {
        $this->cargando = true;
        
        // Validar estatus seleccionado
        if (!empty($this->estatusSeleccionado)) {
            $validator = Validator::make(
                ['estatus' => $this->estatusSeleccionado],
                InscritosRules::getEstatusRules(),
                InscritosRules::getEstatusMessages()
            );

            if ($validator->fails()) {
                $this->estatusSeleccionado = '';
                session()->flash('mensaje', $validator->errors()->first('estatus'));
                session()->flash('tipo', 'error');
            }
        }
        
        $this->resetPage();
        $this->cargando = false;
    }

    // Resetear página si cambian filtros
    public function updatedGeneracionSeleccionada()
    {
        $this->cargando = true;
        
        // Validar generación seleccionada
        $validator = Validator::make(
            ['generacionSeleccionada' => $this->generacionSeleccionada],
            ['generacionSeleccionada' => InscritosRules::getRules()['generacionSeleccionada']],
            InscritosRules::getMessages()
        );

        if ($validator->fails()) {
            $this->generacionSeleccionada = '';
            session()->flash('mensaje', $validator->errors()->first('generacionSeleccionada'));
            session()->flash('tipo', 'error');
        }

        $this->grupoSeleccionado = '';
        $this->resetPage();
        $this->cargando = false;
    }

    public function updatedGrupoSeleccionado()
    {
        $this->cargando = true;
        
        // Validar grupo seleccionado
        $validator = Validator::make(
            ['grupoSeleccionado' => $this->grupoSeleccionado],
            ['grupoSeleccionado' => InscritosRules::getRules()['grupoSeleccionado']],
            InscritosRules::getMessages()
        );

        if ($validator->fails()) {
            $this->grupoSeleccionado = '';
            session()->flash('mensaje', $validator->errors()->first('grupoSeleccionado'));
            session()->flash('tipo', 'error');
        }

        $this->resetPage();
        $this->cargando = false;
    }

    // Cambiar de confirmarEliminacion a mostrarOpcionesEstatus
    public function mostrarOpcionesEstatus($id)
    {
        // Validar ID antes de mostrar opciones
        $validator = Validator::make(
            ['idCambiarEstatus' => $id],
            ['idCambiarEstatus' => ['required', 'exists:inscritos,id']], // Validación más específica
            ['idCambiarEstatus.required' => 'Se requiere un ID para cambiar estatus.',
             'idCambiarEstatus.exists' => 'El inscrito seleccionado no existe.']
        );

        if ($validator->fails()) {
            session()->flash('mensaje', $validator->errors()->first('idCambiarEstatus'));
            session()->flash('tipo', 'error');
            return;
        }

        $this->idCambiarEstatus = $id;
        $this->mostrarModalCambiarEstatus = true;
    }

    // Nuevo método para dar de baja
    public function darDeBaja()
    {
        $this->cambiarEstatusInscrito('baja', 'Alumno marcado como inactivo correctamente.');
    }

    // Nuevo método para marcar como egresado
    public function marcarComoEgresado()
    {
        $this->cambiarEstatusInscrito('egresado', 'Alumno marcado como egresado correctamente.');
    }

    // Método privado para cambiar estatus (reutilizable)
    private function cambiarEstatusInscrito($nuevoEstatus, $mensajeExito)
    {
        // Validar que el ID exista antes de intentar cambiar estatus
        $validator = Validator::make(
            ['idCambiarEstatus' => $this->idCambiarEstatus],
            ['idCambiarEstatus' => ['required', 'exists:inscritos,id']],
            ['idCambiarEstatus.required' => 'Se requiere un ID para cambiar estatus.',
             'idCambiarEstatus.exists' => 'El inscrito seleccionado no existe.']
        );

        if ($validator->fails()) {
            $this->mostrarModalCambiarEstatus = false;
            session()->flash('mensaje', $validator->errors()->first('idCambiarEstatus'));
            session()->flash('tipo', 'error');
            return;
        }

        try {
            $inscrito = Inscrito::find($this->idCambiarEstatus);

            if ($inscrito) {
                // Validar el nuevo estatus
                $validator = Validator::make(
                    ['estatus' => $nuevoEstatus],
                    InscritosRules::getEstatusRules(),
                    InscritosRules::getEstatusMessages()
                );

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first('estatus'));
                }
                
                $inscrito->update(['estatus' => $nuevoEstatus]);
                $this->mostrarModalCambiarEstatus = false;
                
                session()->flash('mensaje', $mensajeExito);
                session()->flash('tipo', 'success');
            }
        } catch (\Exception $e) {
            session()->flash('mensaje', 'Error al cambiar el estatus del alumno: ' . $e->getMessage());
            session()->flash('tipo', 'error');
        }
    }

    public function verAlumno($alumnoId)
    {
        // Verificar que el alumno existe antes de redirigir
        $inscrito = Inscrito::whereHas('alumno', function($query) use ($alumnoId) {
            $query->where('id', $alumnoId);
        })->first();

        if (!$inscrito) {
            session()->flash('mensaje', 'El alumno seleccionado no existe o ha sido eliminado.');
            session()->flash('tipo', 'error');
            return;
        }
        
        // Redirigir a la vista del alumno
        return redirect()->route('alumnos.show', $alumnoId);
    }

    public function aplicarFiltros()
    {
        $this->cargando = true;
        
        // Validar filtros
        $validator = Validator::make(
            [
                'generacionSeleccionada' => $this->generacionSeleccionada,
                'grupoSeleccionado' => $this->grupoSeleccionado
            ],
            [
                'generacionSeleccionada' => InscritosRules::getRules()['generacionSeleccionada'],
                'grupoSeleccionado' => InscritosRules::getRules()['grupoSeleccionado']
            ],
            InscritosRules::getMessages()
        );

        if ($validator->fails()) {
            $this->cargando = false;
            session()->flash('mensaje', 'Error en los filtros seleccionados. Por favor, verifica e intenta nuevamente.');
            session()->flash('tipo', 'error');
            return;
        }
        
        $this->resetPage();
        $this->cargando = false;
    }

    public function render()
    {
        // Modificamos la consulta para mostrar todos los alumnos (sin filtro predeterminado de estatus)
        $query = Inscrito::with(['alumno', 'grupo']);
        
        // Aplicamos los filtros de generación y grupo si existen
        if ($this->grupoSeleccionado) {
            $query->where('grupo_id', $this->grupoSeleccionado);
        } elseif ($this->generacionSeleccionada) {
            $grupoIds = Grupo::where('generacion', $this->generacionSeleccionada)->pluck('id');
            $query->whereIn('grupo_id', $grupoIds);
        }
        
        // Aplicamos el filtro de estatus si existe
        if ($this->estatusSeleccionado) {
            $query->where('estatus', $this->estatusSeleccionado);
        }
        
        // Ordenamos primero por estatus con el orden especificado: vigente, egresado, baja
        // Y luego por ID en orden ascendente
        $query->orderByRaw("FIELD(estatus, 'vigente', 'egresado', 'baja')")
              ->orderBy('id', 'asc');

        return view('livewire.inscritos', [
            'generaciones' => Grupo::select('generacion')->distinct()->get(),
            'grupos' => $this->generacionSeleccionada
                ? Grupo::where('generacion', $this->generacionSeleccionada)->get()
                : Grupo::all(),
            'inscritos' => $query->paginate(10),
        ]);
    }
}