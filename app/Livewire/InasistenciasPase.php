<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\Inasistencia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;

class InasistenciasPase extends Component
{
    #[Locked]
    public $grupos;

    #[Locked]
    public $materias;

    public $grupoSeleccionado = '';
    public $materiaSeleccionada = '';
    public $fechaSeleccionada = '';

    #[Locked]
    public $alumnos = [];

    #[Locked]
    public $diasDelMes = [];

    #[Locked]
    public $inasistenciasPorDia = [];

    #[Locked]
    public $refrescar = 0;

    #[Locked]
    public $diasNoEscolares = [];

    #[Locked]
    public $generaciones = [];

    public $generacionSeleccionada = '';

    #[Locked]
    public $gruposFiltrados = [];

    public function mount()
    {
        $this->generaciones = Grupo::select('generacion')->distinct()->pluck('generacion')->toArray();
        $this->materias = Materia::all()->pluck('nombre', 'id')->toArray();


    }

    protected function rules()
    {
         return [
        'grupoSeleccionado' => ['nullable', 'integer', function ($attribute, $value, $fail) {

            $gruposArray = is_array($this->gruposFiltrados) ? $this->gruposFiltrados : $this->gruposFiltrados->toArray();

            if (!in_array($value, array_column($gruposArray, 'id'))) {
                $fail('Grupo seleccionado inválido.');
            }
        }],

            'materiaSeleccionada' => ['nullable', 'integer', function ($attribute, $value, $fail) {
                if (!array_key_exists($value, $this->materias)) {
                    $fail('Materia seleccionada inválida.');
                }
            }],
            'generacionSeleccionada' => ['nullable', function ($attribute, $value, $fail) {
                if (!in_array($value, $this->generaciones)) {
                    $fail('Generación seleccionada inválida.');
                }
            }],
        ];
    }

  public function updatedGeneracionSeleccionada()
{
    $this->validateOnly('generacionSeleccionada');

    $this->gruposFiltrados = Grupo::where('generacion', $this->generacionSeleccionada)->get();
    $this->grupoSeleccionado = '';
}

    public function updatedGrupoSeleccionado()
    {
        $this->validateOnly('grupoSeleccionado');
        $this->inasistenciasPorDia = [];

        $grupo = Grupo::find($this->grupoSeleccionado);

        if ($grupo) {
            $this->materias = Materia::where('grado', $grupo->grado)->pluck('nombre', 'id')->toArray();
        } else {
            $this->materias = [];
        }

        $this->materiaSeleccionada = '';
        $this->actualizarTabla();
    }

    public function updatedMateriaSeleccionada()
    {
        $this->validateOnly('materiaSeleccionada');
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
        $this->validate();

        if (
            filled($this->grupoSeleccionado) &&
            filled($this->materiaSeleccionada) &&
            filled($this->fechaSeleccionada)
        ) {
            $this->generarTablaMensual();
        } else {
            $this->alumnos = collect();
            $this->diasDelMes = [];
        }
    }


    private function generarTablaMensual()
    {
        if (empty($this->fechaSeleccionada)) return;

        try {
            $fecha = Carbon::createFromFormat('Y-m', $this->fechaSeleccionada)->startOfMonth();
        } catch (\Exception $e) {
            Log::error('Fecha inválida: ' . $this->fechaSeleccionada);
            return;
        }

        $diasEnMes = $fecha->daysInMonth;
        $this->diasDelMes = [];
        $this->diasNoEscolares = [];

        for ($dia = 1; $dia <= $diasEnMes; $dia++) {
            $carbonDia = Carbon::createFromFormat('Y-m', $this->fechaSeleccionada)->day($dia);

            if ($carbonDia->isWeekend()) {
                $this->diasNoEscolares[$dia] = true;
                continue;
            }

            $this->diasDelMes[] = [
                'numero' => $dia,
                'nombre' => $carbonDia->isoFormat('dd')
            ];
        }

        $grupo = Grupo::find($this->grupoSeleccionado);
        $this->alumnos = $grupo ? $grupo->alumnos : collect();

        $inicio = $fecha->copy()->startOfMonth()->toDateString();
        $fin = $fecha->copy()->endOfMonth()->toDateString();

        $inasistencias = Inasistencia::where('materia_id', $this->materiaSeleccionada)
            ->whereBetween('fecha', [$inicio, $fin])
            ->get();

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
            Log::error('Fecha inválida en toggleInasistencia(): ' . $this->fechaSeleccionada);
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
        $this->generarTablaMensual();
        $this->refrescar++;
    }

    public function render()
    {
        return view('livewire.inasistencias-pase');
    }
}
