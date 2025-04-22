<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Maestro;
use App\Models\Imparte;
use App\Models\Inscrito;

class ReportesGrupo extends Component
{
    // Variables de filtrado
    public $grupo_id = null;
    public $generacion = null;
    public $grado = null;
    public $letra = null;
    public $materia_id = null;
    public $maestro_id = null;

    // Variables de resultados
    public $resultados = [];

    // Variables de estadísticas
    public $totalAlumnos = 0;
    public $totalMaestros = 0;
    public $materiasSinMaestro = 0;
    public $gruposSinMaestro = 0;
    public $searchMaestro = '';

    public function mount()
    {
        $this->calcularEstadisticas();
        $this->cargarDatosIniciales();
    }

    public function calcularEstadisticas()
    {
        $this->totalAlumnos = Alumno::count();
        $this->totalMaestros = Maestro::count();

        // Materias sin maestro asignado
        $this->materiasSinMaestro = Materia::whereDoesntHave('imparte', function($q) {
            $q->whereNotNull('maestro_id');
        })->count();

        // Grupos sin maestro asignado
        $this->gruposSinMaestro = Grupo::whereDoesntHave('imparte', function($q) {
            $q->whereNotNull('maestro_id');
        })->count();
    }

    public function cargarDatosIniciales()
    {
        $this->resultados = $this->alumnosInscritos()->take(5);
    }

    public function controlEscolar()
    {
        $this->validate([
            'grado' => 'nullable|integer|between:1,3',
            'letra' => 'nullable|string|max:1',
            'generacion' => 'nullable|integer|min:2000|max:' . (date('Y') + 1)
        ]);

        $this->resultados = $this->filtrarResultados();
        $this->calcularEstadisticas();
    }
  /**
     * Método solicitado para DAVIGOD
 * Filtra grupos por generación y un parámetro adicional (ID, letra o grado).
 *
 * USO NORMAL
 * 1. Filtrar solo por generación:
 *    $this->filtrarGrupos('2024');
 *
 * FRILTRAR POR GENERACIÓN + LETRA
 *    $this->filtrarGrupos('2024', 'A', 'letra');
 *
 * FRILTRAR POR GENERACIÓN + GRADO
 *    $this->filtrarGrupos('2024', '1', 'grado');
 *
 * FILTRAR POR GENERACIÓN + ID
 *    $this->filtrarGrupos('2024', 5, 'id');
 *
 */
    public function filtrarResultados()
    {
        return Alumno::when($this->grado || $this->letra || $this->generacion, function($query) {
                $query->whereHas('inscritos.grupo', function($q) {
                    if ($this->grado) {
                        $q->where('grado', $this->grado);
                    }
                    if ($this->letra) {
                        $q->where('letra', $this->letra);
                    }
                    if ($this->generacion) {
                        $q->where('generacion', $this->generacion);
                    }
                });
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
    }

    public function alumnosInscritos()
    {
        if (!$this->grupo_id) {
            return collect();
        }

        return Grupo::find($this->grupo_id)
            ->alumnos()
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
    }

    public function generacionesDisponibles()
    {
        return Grupo::select('generacion')
            ->distinct()
            ->orderBy('generacion', 'desc')
            ->get()
            ->pluck('generacion');
    }

    public function gruposConAlumnos()
    {
        return Grupo::query()
            ->when($this->generacion, fn($q) => $q->where('generacion', $this->generacion))
            ->withCount('alumnos')
            ->orderBy('grado')
            ->orderBy('letra')
            ->get();
    }

    public function actualizar()
    {
        $this->reset(['grado', 'letra', 'generacion', 'materia_id', 'maestro_id']);
        $this->cargarDatosIniciales();
        $this->calcularEstadisticas();
    }
    public function obtenerMaterias()
    {
        return Materia::select('id', 'nombre')
                     ->orderBy('nombre')
                     ->get()
                     ->toArray();
    }
    public function obtenerMaestrosBasico()
{
    return Maestro::select('id', 'name', 'apellidos')
                 ->orderBy('apellidos')
                 ->orderBy('name')
                 ->get()
                 ->map(function ($maestro) {
                     return [
                         'id' => $maestro->id,
                         'nombre_completo' => $maestro->apellidos . ' ' . $maestro->name
                     ];
                 })
                 ->toArray();
}
public function obtenerMaestrosCompleto()
{
    return Maestro::select('id', 'name', 'apellidos', 'telefono', 'curp', 'direccion', 'email')
                 ->orderBy('apellidos')
                 ->orderBy('name')
                 ->get()
                 ->map(function ($maestro) {
                     return [
                         'id' => $maestro->id,
                         'nombre_completo' => $maestro->apellidos . ' ' . $maestro->name,
                         'telefono' => $maestro->telefono,
                         'curp' => $maestro->curp,
                         'direccion' => $maestro->direccion,
                         'email' => $maestro->email,
                         'tiene_materias' => $maestro->materias()->exists()
                     ];
                 })
                 ->toArray();
}
public function obtenerMaestrosSinMaterias($search = '')
{
    return Maestro::whereDoesntHave('materias')
                 ->when($search, function($query) use ($search) {
                     $query->where(function($q) use ($search) {
                         $q->where('name', 'like', '%'.$search.'%')
                           ->orWhere('apellidos', 'like', '%'.$search.'%')
                           ->orWhere('curp', 'like', '%'.$search.'%');
                     });
                 })
                 ->select('id', 'name', 'apellidos', 'telefono', 'curp')
                 ->orderBy('apellidos')
                 ->orderBy('name')
                 ->get()
                 ->map(function ($maestro) {
                     return [
                         'id' => $maestro->id,
                         'nombre_completo' => $maestro->apellidos . ' ' . $maestro->name,
                         'telefono' => $maestro->telefono,
                         'curp' => $maestro->curp
                     ];
                 })
                 ->toArray();
}
    public function render()
    {
        return view('livewire.reportes-grupo', [
            'grupos' => $this->gruposConAlumnos(),
            'generaciones' => $this->generacionesDisponibles(),
            'materias' => $this->obtenerMaterias(),

            'maestros_basico' => $this->obtenerMaestrosBasico(),
            'maestros_completo' => $this->obtenerMaestrosCompleto(),
            'maestros_sin_materias' => $this->obtenerMaestrosSinMaterias($this->searchMaestro ?? ''),
            'resultados' => $this->resultados,
            'totalAlumnos' => $this->totalAlumnos,
            'totalMaestros' => $this->totalMaestros,
            'materiasSinMaestro' => $this->materiasSinMaestro,
            'gruposSinMaestro' => $this->gruposSinMaestro
        ]);
    }
}
