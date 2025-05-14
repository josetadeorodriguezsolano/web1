<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inscrito;
use App\Models\Grupo;

class Inscritos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $generacionSeleccionada = '';
    public $grupoSeleccionado = '';
    public $mostrarModalEliminar = false;
    public $idEliminar = null;

    // Resetear página si cambian filtros
    public function updatedGeneracionSeleccionada()
    {
        $this->grupoSeleccionado = '';
        $this->resetPage();
    }

    public function updatedGrupoSeleccionado()
    {
        $this->resetPage();
    }

    public function confirmarEliminacion($id)
    {
        $this->idEliminar = $id;
        $this->mostrarModalEliminar = true;
    }

    public function eliminarInscrito()
    {
        Inscrito::find($this->idEliminar)?->delete();
        $this->mostrarModalEliminar = false;
        session()->flash('mensaje', 'Alumno eliminado correctamente.');
    }

    
    public function render()
{
    $query = Inscrito::with(['alumno', 'grupo']);

    if ($this->grupoSeleccionado) {
        $query->where('grupo_id', $this->grupoSeleccionado);
    } elseif ($this->generacionSeleccionada) {
        $grupoIds = Grupo::where('generacion', $this->generacionSeleccionada)->pluck('id');
        $query->whereIn('grupo_id', $grupoIds);
    }

    return view('livewire.inscritos', [
        'generaciones' => Grupo::select('generacion')->distinct()->get(),
        'grupos' => $this->generacionSeleccionada
            ? Grupo::where('generacion', $this->generacionSeleccionada)->get()
            : Grupo::all(),
        'inscritos' => $query->paginate(10), 
    ]);
}

}
