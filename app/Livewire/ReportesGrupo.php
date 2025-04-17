<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Alumno;

class ReportesGrupo extends Component
{
    // Variables
    public $grupo_id = null;
    public $generacion = null;
    public $alumnos =[];


     //Obtiene todos los grupos con número de alumnos
     //Lo puedes filtrar por generación


    public function controlEscolar()
    {
        logger('Ejecutando controlEscolar');
        $this->alumnos = $this->alumnosInscritos();
        dd($this->alumnos);
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
            'generaciones' => $this->generacionesDisponibles()
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
}
