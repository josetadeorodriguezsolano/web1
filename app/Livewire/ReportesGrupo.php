<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Maestro;
use App\Models\Imparte;
use App\Models\Inscrito;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
=======
>>>>>>> anntho

class ReportesGrupo extends Component
{
    // Variables de filtrado
    public $grupo_id = null;
    public $generacion = null;
    public $grado = null;
    public $letra = null;
    public $materia_id = null;
    public $maestro_id = null;
<<<<<<< HEAD
   
=======

>>>>>>> anntho
    // Variables de resultados
    public $resultados = [];

    // Variables de estadísticas
    public $totalAlumnos = 0;
    public $totalMaestros = 0;
    public $materiasSinMaestro = 0;
    public $gruposSinMaestro = 0;
    public $searchMaestro = '';
<<<<<<< HEAD
    public $modo = true; //True reporte de alumnos, false reporte de maestros
    public $maestros = [];

    //vista tab
    public $vista = 'maestros';  // Tab por defecto
    public $filtro = '';
    //
    public $maestros_con_materias = [];
    public $materias_sin_maestro = [];
=======
>>>>>>> anntho

    public function mount()
    {
        $this->calcularEstadisticas();
        $this->cargarDatosIniciales();
    }

<<<<<<< HEAD
    public function cambiarVista($tab)
    {
        $this->vista = $tab; 
        $this->resultados = []; 
        $this->buscarReporte(); 
    }
    public function cambioModo()
    {
        $this->resultados = []; 
        $this->modo = !$this->modo; 
        $this->buscarReporte(); 
    }
    public function buscarReporte()
    {
        $this->resultados = []; 

        if ($this->modo) {
            $this->controlEscolar();
        } else {
            // Reportes de maestros
            switch ($this->vista) {
                case 'maestros':
                    $this->controlEscolarMaestros();
                    break;
                case 'maestro_por_materias':
                    $this->reporteMaestroPorMateria();
                    break;
                case 'materias_sin_maestro':
                    $this->reporteMateriasSinMaestro();
                    break;
                case 'grupos_sin_maestro':
                    $this->reporteGruposSinMaestro();
                    break;
                default:
                    $this->controlEscolarMaestros(); 
                    break;
            }
        }
    }
    //MIO
    public function reporteMaestroPorMateria()
    {
        $query = \App\Models\Imparte::with(['maestro', 'materia', 'grupo']);

        if ($this->materia_id) {
            $query->where('materia_id', $this->materia_id);
        }

        if ($this->maestro_id) {
            $query->where('maestro_id', $this->maestro_id);
        }

        $asignaciones = $query->orderBy('maestro_id')->get();

        $maestrosAgrupados = [];

        foreach ($asignaciones as $asignacion) {
            $maestro_id = $asignacion->maestro->id;
            $maestro_nombre = $asignacion->maestro->nombre_completo ?? "{$asignacion->maestro->name} {$asignacion->maestro->apellidos}";
            $conflicto = false;

            if (!isset($maestrosAgrupados[$maestro_id])) {
                $maestrosAgrupados[$maestro_id] = [
                    'maestro' => $maestro_nombre,
                    'materias' => [],
                ];
            }

            // Verificar cruces con otras materias del mismo maestro
            foreach ($maestrosAgrupados[$maestro_id]['materias'] as &$materiaExistente) {
                if (
                    $asignacion->dia === $materiaExistente['dia'] &&
                    (
                        ($asignacion->hora_inicio >= $materiaExistente['hora_inicio'] && $asignacion->hora_inicio < $materiaExistente['hora_fin']) ||
                        ($asignacion->hora_fin > $materiaExistente['hora_inicio'] && $asignacion->hora_fin <= $materiaExistente['hora_fin']) ||
                        ($asignacion->hora_inicio <= $materiaExistente['hora_inicio'] && $asignacion->hora_fin >= $materiaExistente['hora_fin'])
                    )
                ) {
                    $materiaExistente['conflicto'] = true;
                    $conflicto = true;
                }
            }

            $maestrosAgrupados[$maestro_id]['materias'][] = [
                'materia' => $asignacion->materia->nombre ?? 'Desconocida',
                'grupo' => "{$asignacion->grupo->grado}{$asignacion->grupo->letra}",
                'dia' => $asignacion->dia,
                'hora_inicio' => $asignacion->hora_inicio->format('H:i'),
                'hora_fin' => $asignacion->hora_fin->format('H:i'),
                'conflicto' => $conflicto,
            ];
        }

        $this->resultados = collect($maestrosAgrupados);

        // Lista plana para mostrar fácilmente en tabla
        $this->maestros_con_materias = [];
        foreach ($maestrosAgrupados as $maestro) {
            foreach ($maestro['materias'] as $materia) {
                $this->maestros_con_materias[] = [
                    'maestro' => $maestro['maestro'],
                    'materia' => $materia['materia'],
                    'grupo' => $materia['grupo'],
                    'dia' => $materia['dia'],
                    'hora_inicio' => $materia['hora_inicio'],
                    'hora_fin' => $materia['hora_fin'],
                    'cruce' => $materia['conflicto'],
                ];
            }
        }

        $this->modo = false;
    }
    public function reporteMateriasSinMaestro()
    {
        $materiasSinMaestro = \App\Models\Materia::leftJoin('imparte', 'materias.id', '=', 'imparte.materia_id')
            ->whereNull('imparte.maestro_id') // no hay maestro asignado
            ->select('materias.id', 'materias.nombre')
            ->get(); // Obtiene como una colección de objetos Eloquent

        $this->resultados = $materiasSinMaestro;
        $this->modo = false;
    }

    public function reporteGruposSinMaestro()
    {
    
        $gruposSinMaestro = \App\Models\Grupo::leftJoin('imparte', 'grupos.id', '=', 'imparte.grupo_id')
            ->whereNull('imparte.maestro_id') 
            ->leftJoin('materias', 'imparte.materia_id', '=', 'materias.id')
            ->select(
                'grupos.id', 
                DB::raw("CONCAT(grupos.grado, grupos.letra, '-', grupos.generacion) as grupo_nombre"), 
                'materias.nombre as materia_nombre' 
            )
            ->get();

        $this->resultados = $gruposSinMaestro;
        $this->modo = false;
    }








    public function actualizarMateriaId(){
        //dd("Se actualizó a: ", $value);
        
        $this->materia_id = $this->materia_id !== '' ? (int) $this->materia_id : null;
        //dd("Materia se actualizo a: ", $this->materia_id);
    }


=======
>>>>>   anntho
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

<<<<<<< HEAD

=======
>>>>>>> anntho
    public function cargarDatosIniciales()
    {
        $this->resultados = $this->alumnosInscritos()->take(5);
    }

<<<<<<< HEAD
    /*Tanto este metodo como el contolEscolarMaestros deberian ser el mismo
        pero por algun motivo el if en control escolar del modo no funciona
        como deberia, asi se creo un metodo particualar para cada caso, un if en
        la visa que cambia el metodo que debe de tomar el boton, hay que pensar
        en como optimizar este problema en futuras iteraciones
    */
    public function controlEscolar()
    {




=======
    public function controlEscolar()
    {
>>>>>>> anntho
        $this->validate([
            'grado' => 'nullable|integer|between:1,3',
            'letra' => 'nullable|string|max:1',
            'generacion' => 'nullable|integer|min:2000|max:' . (date('Y') + 1)
        ]);

<<<<<<< HEAD
        if($this->modo===true)
        {

            //dd("Estoy en el if");
            $this->resultados = $this->filtrarResultados();
            $this->calcularEstadisticas();
        }
        if($this->modo===false)
        {

            dd("Estoy en el else");
            //$this->resultados = $this->obtenerMaestrosPorMateria();
            //dd($this->resultados);
            //$this->resultados = $this->obtenerMaestrosPorMateria($this->materia_id);
            //$this->calcularEstadisticas();
            //dd($this->resultados);
            /*
            $this->resultados = $this->obtenerMaestrosPorMateria($this->materia_id);
            dd($this->resultados);
            //dd("estoy en el else y mi valor de materia es: ");
            //dd("estoy en el else y mi valor de materia es: ", $this->materia_id);
            $this->resultados = $this->obtenerMaestrosPorMateria($this->materia_id);
            dd($this->resultados);
            //$this->calcularEstadisticas();
             dd([
            'materia_id' => $this->materia_id,
            'tipo' => gettype($this->materia_id)
        ]);
            */
        }
    }

    public function controlEscolarMaestros()
    {
        //dd("Estoy en el else");
        $this->resultados = $this->obtenerMaestrosPorMateria();
        //dd($this->resultados);
    }


=======
        $this->resultados = $this->filtrarResultados();
        $this->calcularEstadisticas();
    }
>>>>>>> anntho
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
<<<<<<< HEAD

public function obtenerMaestrosPorMateria()
{
    //$materia_id = $materia_id !== '' ? (int) $materia_id : null;

    $materia_id = $this->materia_id;
    return Maestro::whereHas('materias', function ($query) use ($materia_id) {
        if ($materia_id) {
            $query->where('materias.id', $materia_id);
        }
    })->get();
}

=======
>>>>>>> anntho
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
<<<<<<< HEAD

=======
>>>>>>> anntho
            'resultados' => $this->resultados,
            'totalAlumnos' => $this->totalAlumnos,
            'totalMaestros' => $this->totalMaestros,
            'materiasSinMaestro' => $this->materiasSinMaestro,
<<<<<<< HEAD
            'gruposSinMaestro' => $this->gruposSinMaestro,
            'materia_id' => $this->materia_id
        ]);
    }
   


=======
            'gruposSinMaestro' => $this->gruposSinMaestro
        ]);
    }
>>>>>>> anntho
}
