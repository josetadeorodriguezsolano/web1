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

    public function verAlumno($alumnoId)
    {
        // Redirigir a la vista del alumno
        // Ajusta la ruta según tu configuración
        return redirect()->route('alumnos.show', $alumnoId);
    }

   public function eliminarInscrito()
{
    // En lugar de eliminar el registro, actualizamos el estatus a 'baja'
    $inscrito = Inscrito::find($this->idEliminar);

    if ($inscrito) {
        $inscrito->update(['estatus' => 'baja']);
        $this->mostrarModalEliminar = false;
        session()->flash('mensaje', 'Alumno marcado como baja correctamente.');
    }
}

    public function render()
{
    // Modificamos la consulta para solo mostrar alumnos con estatus 'vigente'
    $query = Inscrito::with(['alumno', 'grupo'])
            ->where('estatus', 'vigente');

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
