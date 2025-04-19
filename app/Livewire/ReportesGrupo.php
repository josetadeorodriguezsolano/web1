<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Materia;

class ReportesGrupo extends Component
{
    // Variables
    public $grupo_id = null;
    public $generacion = null;
    public $alumnos =[];

    public $maestros = [];
    public $materia_id = null;
     //Obtiene todos los grupos con número de alumnos
     //Lo puedes filtrar por generación


    public function controlEscolar()
    {
        logger('Ejecutando controlEscolar');

        if($this->grupo_id!=0) //Este if es para busquedas de alumnos
        {
            //dd("Estoy entrando en el if de alumnos");
            $this->alumnos = $this->alumnosInscritos();
            //dd($this->alumnos);
        }
        else //Este else es para las operaciones de maestros buscados por materia
        {
            //dd("Estoy entrando en el else de maestros");
            /*
            $this->maestros = [
                (object)[
                    'id' => 1,
                    'name' => 'Matt Gottlieb',
                    'apellidos' => 'Lang',
                    'telefono' => '7167928925',
                    'curp' => 'VEZQZX7ZNXJT46HB60',
                    'direccion' => '63864 Blick Rapid, Olsonstad, WY 83059',
                    'email' => 'elena93@example.org',
                ]

           ];
           */
           $this->maestros = $this->maestrosPorMateria(1);
        }

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


    // Obtiene alumnos inscritos en un grupo específico (nombres)
     // Requiere que $grupo_id no sea null

     public function alumnosInscritos()
     {
         if(!$this->grupo_id) return collect();

         return Alumno::whereHas('inscritos', function($q) {
                 $q->where('grupo_id', $this->grupo_id);
             })
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
            'alumnos' => $this->alumnosInscritos(),
            'maestros' => $this->maestros,
            'generaciones' => $this->generacionesDisponibles(),
            'materia_id' => $this->materia_id,
            'grupo_id' => $this->grupo_id
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

public function maestrosPorMateria($materia_id)
    {
        return Materia::with(['grupos.imparte.maestro' => function($q) {
            $q->distinct()->select('maestros.id', 'name', 'apellidos');
        }])->findOrFail($materia_id);
    }
}
