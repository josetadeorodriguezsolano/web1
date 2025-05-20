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

    public $mostrarModalEliminar = false;
    public $idEliminar = null;
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

    public function confirmarEliminacion($id)
    {
        // Validar ID antes de confirmar eliminación
        $validator = Validator::make(
            ['idEliminar' => $id],
            ['idEliminar' => InscritosRules::getRules()['idEliminar']],
            InscritosRules::getMessages()
        );

        if ($validator->fails()) {
            $this->dispatch('mostrar-alerta', [
                'tipo' => 'error',
                'mensaje' => $validator->errors()->first('idEliminar')
            ]);
            return;
        }

        $this->idEliminar = $id;
        $this->mostrarModalEliminar = true;
    }

    public function verAlumno($alumnoId)
    {
        // Verificar que el alumno existe antes de redirigir
        $inscrito = Inscrito::whereHas('alumno', function($query) use ($alumnoId) {
            $query->where('id', $alumnoId);
        })->first();

        if (!$inscrito) {
            $this->dispatch('mostrar-alerta', [
                'tipo' => 'error',
                'mensaje' => 'El alumno seleccionado no existe o ha sido eliminado.'
            ]);
            return;
        }
        
        // Redirigir a la vista del alumno
        return redirect()->route('alumnos.show', $alumnoId);
    }

    public function eliminarInscrito()
    {
        // Validar que el ID exista antes de intentar eliminar
        $validator = Validator::make(
            ['idEliminar' => $this->idEliminar],
            ['idEliminar' => InscritosRules::getRules()['idEliminar']],
            InscritosRules::getMessages()
        );

        if ($validator->fails()) {
            $this->mostrarModalEliminar = false;
            $this->dispatch('mostrar-alerta', [
                'tipo' => 'error',
                'mensaje' => $validator->errors()->first('idEliminar')
            ]);
            return;
        }

        try {
            // En lugar de eliminar el registro, actualizamos el estatus a 'baja'
            $inscrito = Inscrito::find($this->idEliminar);

            if ($inscrito) {
                // Validar el estatus
                $validator = Validator::make(
                    ['estatus' => 'baja'],
                    InscritosRules::getEstatusRules(),
                    InscritosRules::getEstatusMessages()
                );

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first('estatus'));
                }
                
                $inscrito->update(['estatus' => 'baja']);
                $this->mostrarModalEliminar = false;
                
                $this->dispatch('mostrar-alerta', [
                    'tipo' => 'success',
                    'mensaje' => 'Alumno marcado como baja correctamente.'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('mostrar-alerta', [
                'tipo' => 'error',
                'mensaje' => 'Error al dar de baja al alumno: ' . $e->getMessage()
            ]);
        }
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
            $this->dispatch('mostrar-alerta', [
                'tipo' => 'error',
                'mensaje' => 'Error en los filtros seleccionados. Por favor, verifica e intenta nuevamente.'
            ]);
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
        $query->where('inscritos.estatus', $this->estatusSeleccionado);
    }
    
    // Ordenamos por estatus con el orden especificado: vigente, egresado, baja
    $query->orderByRaw("FIELD(inscritos.estatus, 'vigente', 'egresado', 'baja')")
          ->join('alumnos', 'inscritos.alumno_id', '=', 'alumnos.id')
          ->orderBy('alumnos.apellidos')
          ->orderBy('alumnos.nombres')
          ->select('inscritos.*'); // Seleccionamos explícitamente las columnas de inscritos

    
    $query->orderByRaw("FIELD(inscritos.estatus, 'vigente', 'egresado', 'baja')")
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