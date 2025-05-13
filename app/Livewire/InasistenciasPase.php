<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\Inasistencia;
use Illuminate\Support\Carbon;

class InasistenciasPase extends Component
{
    public $grupos;
    public $materias;
    public $grupoSeleccionado = '';
    public $materiaSeleccionada = '';
    public $fechaSeleccionada = '';
    public $alumnos = [];
    public $diasDelMes = [];
    public $inasistenciasPorDia = [];
    public $refrescar = 0;
    public $diasNoEscolares = [];
    public $generaciones = [];
    public $generacionSeleccionada = '';
    public $gruposFiltrados = [];




    public function mount()
    {
        $this->generaciones = Grupo::select('generacion')->distinct()->pluck('generacion')->toArray();
        $this->materias = Materia::all()->pluck('nombre', 'id')->toArray();
    }

    public function updatedGeneracionSeleccionada()
    {
        $this->gruposFiltrados = Grupo::where('generacion', $this->generacionSeleccionada)->get();
        $this->grupoSeleccionado = ''; // Limpiar grupo cuando cambia la generación
    }

    public function updatedGrupoSeleccionado()
    {
        $this->inasistenciasPorDia = [];
        $this->actualizarTabla();
    }

    public function updatedMateriaSeleccionada()
    {
        $this->inasistenciasPorDia = [];
        $this->actualizarTabla();
    }

    public function updatedFechaSeleccionada()
    {
        $this->inasistenciasPorDia = [];
        $this->actualizarTabla();
    }

    public function actualizarTabla()
{
    if (
        filled($this->grupoSeleccionado) &&
        filled($this->materiaSeleccionada) &&
        filled($this->fechaSeleccionada)
    ) {
        $this->generarTablaMensual();
    } else {
        // Limpia la tabla si falta algún filtro
        $this->alumnos = collect();
        $this->diasDelMes = [];
        $this->inasistencias = [];
    }
}


    public function generarTablaMensual()
    {
        if (empty($this->fechaSeleccionada)) return;

        try {
            $fecha = Carbon::createFromFormat('Y-m', $this->fechaSeleccionada)->startOfMonth();
        } catch (\Exception $e) {
            \Log::error('Fecha inválida: ' . $this->fechaSeleccionada);
            return;
        }

        // Calcular días hábiles del mes (lunes a viernes)
        $diasEnMes = $fecha->daysInMonth;
        $this->diasDelMes = [];
        $this->diasNoEscolares = [];

        for ($dia = 1; $dia <= $diasEnMes; $dia++) {
            $carbonDia = Carbon::createFromFormat('Y-m', $this->fechaSeleccionada)->day($dia);

            if ($carbonDia->isWeekend()) {
                $this->diasNoEscolares[$dia] = true; // Si quieres dejarla para lógica futura
                continue; // ❌ No agregues sábados ni domingos a la tabla
            }

            $this->diasDelMes[] = [
                'numero' => $dia,
                'nombre' => $carbonDia->isoFormat('dd') // Ej: Lu, Ma, Mi, Ju, Vi
            ];
        }

        // Cargar alumnos del grupo seleccionado
        $grupo = Grupo::find($this->grupoSeleccionado);
        $this->alumnos = $grupo ? $grupo->alumnos : collect();

        // Cargar inasistencias del mes actual para esa materia
        $inicio = $fecha->copy()->startOfMonth()->toDateString();
        $fin = $fecha->copy()->endOfMonth()->toDateString();

        $inasistencias = Inasistencia::where('materia_id', $this->materiaSeleccionada)
            ->whereBetween('fecha', [$inicio, $fin])
            ->get();

        // Organizar inasistencias por alumno y día
        foreach ($inasistencias as $inasistencia) {
            $dia = Carbon::parse($inasistencia->fecha)->day;
            $this->inasistenciasPorDia[$inasistencia->alumno_id][$dia] = true;
        }
    }



    public function toggleInasistencia($alumnoId, $dia)
    {
        if (!$this->fechaSeleccionada || !$this->materiaSeleccionada) return;

        try {
            $fecha = Carbon::createFromFormat('Y-m', $this->fechaSeleccionada)->day($dia)->toDateString();
        } catch (\Exception $e) {
            \Log::error('Fecha inválida en toggleInasistencia(): ' . $this->fechaSeleccionada);
            return;
        }

        $existe = Inasistencia::where('alumno_id', $alumnoId)
            ->where('materia_id', $this->materiaSeleccionada)
            ->where('fecha', $fecha)
            ->first();

        if ($existe) {
            $existe->delete();
        } else {
            Inasistencia::create([
                'alumno_id' => $alumnoId,
                'materia_id' => $this->materiaSeleccionada,
                'fecha' => $fecha,
            ]);
        }

        $this->inasistenciasPorDia = [];
        $this->generarTablaMensual(); // Volver a leer de la base de datos 
        $this->refrescar++; // Forzar render       
    }


    public function render()
    {
        return view('livewire.inasistencias-pase');
    }
}
