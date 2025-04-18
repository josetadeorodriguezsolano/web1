<?php

namespace app\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Maestro;


class ReportesGrupo extends Component
{
    // Variables
    public $grupo_id = null;
    public $generacion = null;


    // Varuabkes hechas por el DAVIGOD para la vista
    public $buqueda = null;
    public $resultados = []; //De esta forma debe de quedar al momento en que funcione correctamente alumnosInscritos
    public $letra = null;
    public $grado = null;
// Agrega esta propiedad al inicio de tu componente
public $tabActivo = 'maestros';

// Agrega este método para materias con maestro
public function materiasConMaestro()
{
    return Materia::whereHas('grupos.imparte')
        ->when($this->grado, fn($q) => $q->where('grado', $this->grado))
        ->with(['grupos.imparte.maestro'])
        ->get();
}


     //Obtiene todos los grupos con número de alumnos
     //Lo puedes filtrar por generación

     public function controlEscolar()
     {
         logger("Filtros aplicados:", [
             'grado' => $this->grado,
             'letra' => $this->letra,
             'generacion' => $this->generacion
         ]);

         $this->resultados = Alumno::with(['inscritos.grupo'])
             ->whereHas('inscritos.grupo', function($q) {
                 $q->when($this->grado, fn($q) => $q->where('grado', $this->grado))
                   ->when($this->letra, fn($q) => $q->where('letra', $this->letra))
                   ->when($this->generacion, fn($q) => $q->where('generacion', $this->generacion));
             })
             ->orderBy('apellidos')
             ->orderBy('nombres')
             ->get();


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

    /*public function cambiarResutados()
    {

        //Cambiar el arreglo resultados de alumno
        $this->resultados  = [
            (object)[
                'id' => 1,
                'matricula' => 'TS93GKIAO',
                'nombres' => 'Nat',
                'apellidos' => 'McDermott',
                'estatus' => 'vigente',
                'curp' => 'DDBRN59A6FJYRGNSCM',
                'contacto' => '5138703540',
                'tutor' => 'Louie Lynch',
            ],
            (object)[
                'id' => 2,
                'matricula' => 'GR6J15V8HX',
                'nombres' => 'Melba',
                'apellidos' => 'Harber',
                'estatus' => 'vigente',
                'curp' => '6TGKXY3G6ARGGULO4V',
                'contacto' => '6576473872',
                'tutor' => 'Shea Crona',
            ],
            (object)[
                'id' => 3,
                'matricula' => 'ACG5VRSGE8',
                'nombres' => 'Lionel',
                'apellidos' => 'Okuneva',
                'estatus' => 'egresado',
                'curp' => 'OOUIR79D7QQXM5I2M',
                'contacto' => '2877592852',
                'tutor' => 'Susanna Daniel DDS',
            ],
        ];

        //Cambiar el arreglo resultados para los grupos
        $this->resultados = [
            (object) [
                "id" => 1,
                "grado" => 1,
                "letra" => "A",
                "generacion" => 2020
            ],
            (object) [
                "id" => 2,
                "grado" => 1,
                "letra" => "B",
                "generacion" => 2020
            ],
            (object) [
                "id" => 3,
                "grado" => 2,
                "letra" => "A",
                "generacion" => 2020
            ],
            (object) [
                "id" => 4,
                "grado" => 2,
                "letra" => "B",
                "generacion" => 2020
            ],
            (object) [
                "id" => 5,
                "grado" => 1,
                "letra" => "A",
                "generacion" => 2021
            ],
            (object) [
                "id" => 6,
                "grado" => 3,
                "letra" => "B",
                "generacion" => 2021
            ],
        ];
    }
        */
        public function getTotalAlumnosProperty()
        {
            return Alumno::count();
        }

        public function getTotalMaestrosProperty()
        {
            return Maestro::count();
        }

        public function getMateriasSinMaestroCountProperty()
        {
            return Materia::whereDoesntHave('maestros')->count();
        }

        public function getGruposSinMaestroCountProperty()
        {
            return Grupo::whereDoesntHave('imparte')->count();
        }

    // Obtiene alumnos inscritos en un grupo específico (nombres)
     // Requiere que $grupo_id no sea null

     public function alumnosInscritos()
     {
         return Alumno::whereHas('inscritos.grupo', function($q) {
                 $q->when($this->grado, fn($q) => $q->where('grado', $this->grado))
                   ->when($this->letra, fn($q) => $q->where('letra', $this->letra))
                   ->when($this->generacion, fn($q) => $q->where('generacion', $this->generacion));
             })
             ->orderBy('apellidos')
             ->orderBy('nombres')
             ->paginate(15);
     }


     //Obtiene las generaciones disponibles

    public function generacionesDisponibles()
    {
        return Grupo::select('generacion')
            ->distinct()
            ->orderBy('generacion', 'desc')
            ->get()
            ->pluck('generacion');
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
    public function filtrarGrupos($generacion, $filtroAdicional = null, $tipoFiltro = 'letra')
    {
        return Grupo::query()
            ->when($generacion, fn($q) => $q->where('generacion', $generacion))
            ->when($filtroAdicional, function($q) use ($tipoFiltro, $filtroAdicional) {
                match($tipoFiltro) {
                    'id'      => $q->where('id', $filtroAdicional),
                    'letra'   => $q->where('letra', $filtroAdicional),
                    'grado'   => $q->where('grado', $filtroAdicional),
                    default   => $q
                };
            })
            ->withCount('alumnos')
            ->orderBy('grado')
            ->orderBy('letra')
            ->get();
    }

    public function maestrosPorMateria()
    {
        return Materia::with(['grupos.imparte.maestro' => function($q) {
            $q->distinct()->select('maestros.id', 'name', 'apellidos');
        }])->findOrFail($this->buqueda);
    }

    //Método principal para usar en el blase

    public function render()
    {
        return view('livewire.reportes-grupo', [
            'totalAlumnos' => $this->totalAlumnos,
            'totalMaestros' => $this->totalMaestros,
            'materiasSinMaestroCount' => $this->materiasSinMaestroCount,
            'gruposSinMaestroCount' => $this->gruposSinMaestroCount,
            'grupos' => $this->gruposConAlumnos(),
            'resultados' => $this->resultados,
            'generaciones' => $this->generacionesDisponibles(),
            'letra' => $this->letra,
            'grado' => $this->grado

        ]);
    }

}
?>
