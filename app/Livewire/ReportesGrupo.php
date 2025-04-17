<?php

namespace app\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Materia;


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

    public function mount()
{
    // Datos simulados

    $this->resultados = [
        (object)[
            'id' => 1,
            'name' => 'Matt Gottlieb',
            'apellidos' => 'Lang',
            'telefono' => '7167928925',
            'curp' => 'VEZQZX7ZNXJT46HB60',
            'direccion' => '63864 Blick Rapid, Olsonstad, WY 83059',
            'email_verified_at' => '2025-04-15 21:08:30',
        ],
        (object)[
            'id' => 2,
            'name' => 'Marley Hagenes',
            'apellidos' => 'Medhurst',
            'telefono' => '7271620148',
            'curp' => 'RAGXKN7Z28ATGCXWD2',
            'direccion' => '765 Mante Valley Suite 020, Klockburgh, FL 31652-3454',
            'email_verified_at' => '2025-04-15 21:08:31',
        ],
        (object)[
            'id' => 3,
            'name' => 'Dr. Stephania Keeling I',
            'apellidos' => 'Hoeger',
            'telefono' => '5273039822',
            'curp' => 'K6957GQTCSDYJ6R4QN',
            'direccion' => '81548 Zboncak Mills, New Gideon, LA 83826-3008',
            'email_verified_at' => '2025-04-15 21:08:31',
        ],
        (object)[
            'id' => 4,
            'name' => 'David Collins I',
            'apellidos' => 'Rosenbaum',
            'telefono' => '4110748944',
            'curp' => 'I8NMAK1P8RL7MET7LX',
            'direccion' => '56787 Schamberger Turnpike, New Staceytown, DC 59827',
            'email_verified_at' => '2025-04-15 21:08:31',
        ],
        (object)[
            'id' => 5,
            'name' => 'Prof. Shyann Douglas III',
            'apellidos' => 'Schroeder',
            'telefono' => '0701663821',
            'curp' => 'WM03HM79HBO6JKYVF',
            'direccion' => '3914 Janice Inlet Suite 109, Port Chester, WY 83800-2520',
            'email_verified_at' => '2025-04-15 21:08:31',
        ],
        (object)[
            'id' => 6,
            'name' => 'Laron Marks',
            'apellidos' => 'Casper',
            'telefono' => '4692690404',
            'curp' => 'U04NYPPU670PMT948',
            'direccion' => '426 Schumm Land, East Caden, DC 43775-0797',
            'email_verified_at' => '2025-04-15 21:08:31',
        ],
        (object)[
            'id' => 7,
            'name' => 'Ms. Felicity Carroll',
            'apellidos' => 'Nader',
            'telefono' => '6793984746',
            'curp' => 'B0ZTMRNY83X0E8Q0QR',
            'direccion' => '68405 Beth Estate Apt. 613, West Ruth, NV 07869',
            'email_verified_at' => '2025-04-15 21:08:31',
        ],
        (object)[
            'id' => 8,
            'name' => 'Prof. Dortha Stokes IV',
            'apellidos' => 'King',
            'telefono' => '45046640256',
            'curp' => 'UI0SRXGULPVXJD9DIX',
            'direccion' => '4052 Rempel Crossing, Edenfort, TX 80736',
            'email_verified_at' => '2025-04-15 21:08:32',
        ],
    ];

}

     //Obtiene todos los grupos con número de alumnos
     //Lo puedes filtrar por generación


     public function controlEscolar()
     {
        logger("La letra seleccionada es: $this->letra");
        logger("El grado seleccionado es: $this->grado");
        logger("La generacion seleccionada es: $this->generacion");

        //$this->resultados = $this-> gruposConAlumnos();

        $this->grupo_id = 1;
        $this->resultados = $this->alumnosInscritos();
        //logger("El grupo_id es: $this->grupo_id");
        //$this->resultados = $this->filtrarGrupos('2024', '1', 'grado');
        //$this->buqueda = 1;
        //$this->resultados = $this->maestrosPorMateria();
        //logger("El contenido de resultados es: $this->resultados");
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

    public function cambiarResutados()
    {
        /*
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
        */
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
    // Obtiene alumnos inscritos en un grupo específico (nombres)
     // Requiere que $grupo_id no sea null

    public function alumnosInscritos()
    {
        if(!$this->grupo_id) return collect();

        return Alumno::whereHas('inscritos', fn($q) => $q->where('grupo_id', $this->grupo_id))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get();
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


     //Método principal para usar en el blase

    public function render()
    {
        return view('livewire.reportes-grupo', [
            'grupos' => $this->gruposConAlumnos(),
            'resultados' => $this->resultados,
            'generaciones' => $this->generacionesDisponibles(),
            'letra' => $this->letra,
            'grado' => $this->grado
        ]);
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


}
?>
