<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\Inasistencia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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



    public function mount()
    {
        $this->grupos = Grupo::all();
        $this->materias = Materia::all()->pluck('nombre', 'id')->toArray();
    }

    public function updatedGrupoSeleccionado()
    {
        $this->actualizarTabla();
    }

    public function updatedMateriaSeleccionada()
    {
        $this->actualizarTabla();
    }

    public function updatedFechaSeleccionada()
    {
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
        }
    }




    public function generarTablaMensual()
    {
        if (empty($this->fechaSeleccionada)) return;

        try {
            $fecha = Carbon::createFromFormat('Y-m', $this->fechaSeleccionada)->startOfMonth();
        } catch (\Exception $e) {

            return;
        }

        // Calcular días del mes
        $diasEnMes = $fecha->daysInMonth;
        $this->diasDelMes = range(1, $diasEnMes);

        // Cargar alumnos del grupo seleccionado
        $grupo = Grupo::find($this->grupoSeleccionado);
        $this->alumnos = $grupo ? $grupo->alumnos : collect();

        // Reset arrays
        $this->inasistenciasPorDia = [];
        $this->diasNoEscolares = [];

        // Detectar sábados y domingos
        foreach ($this->diasDelMes as $dia) {
            $carbonDia = Carbon::createFromFormat('Y-m', $this->fechaSeleccionada)->day($dia);
            if ($carbonDia->isWeekend()) {
                $this->diasNoEscolares[$dia] = true;
            }
        }

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
