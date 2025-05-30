<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Maestro;
use PHPUnit\Framework\Attributes\Medium;

class AuditoriaInasistencias extends Component
{
    use WithPagination;

    public $matricula = '';
    public $materiaId = '';
    public $eventos = ['created', 'updated', 'deleted'];
    public $eventoSeleccionado = '';
    public $maestroId = '';

    public $mesSeleccionado = 'alltime';

    protected $queryString = [
        'matricula' => ['except' => ''],
        'materiaId' => ['except' => ''],
        'mesSeleccionado' => ['except' => 'alltime'],
        'page' => ['except' => 1]
    ];


    public function mount()
    {
    }

    public function render()
    {
        $audits = Audit::with('user')
            ->where('auditable_type', 'App\Models\Inasistencia')
            ->when($this->eventoSeleccionado, function ($query) {
                $query->where('event', $this->eventoSeleccionado);
            })

            ->when($this->matricula, function ($query) {
                $alumno = Alumno::where('matricula', $this->matricula)->first();
                if ($alumno) {
                    $query->where(function ($q) use ($alumno) {
                        $q->where('new_values->alumno_id', $alumno->id)
                            ->orWhere('old_values->alumno_id', $alumno->id);
                    });
                } else {
                    $query->whereRaw('0 = 1'); // No hay coincidencias
                }
            })
            ->when($this->materiaId, function ($query) {
                $query->where(function ($q) {
                    $q->where('new_values->materia_id', $this->materiaId)
                        ->orWhere('old_values->materia_id', $this->materiaId);
                });
            })
            ->when($this->mesSeleccionado !== 'alltime', function ($query) {
                $query->whereMonth('created_at', $this->mesSeleccionado);
            })
            ->when($this->maestroId, function ($query) {
                $query->where('user_id', $this->maestroId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.auditoria-inasistencias', [
            'audits' => $audits,
            'materias' => Materia::all(),
            'maestros' => Maestro::all(),
            'meses' => [
                'alltime' => 'Todos',
                '1' => 'Enero',
                '2' => 'Febrero',
                '3' => 'Marzo',
                '4' => 'Abril',
                '5' => 'Mayo',
                '6' => 'Junio',
                '7' => 'Julio',
                '8' => 'Agosto',
                '9' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre',
            ]
        ]);
    }



    public function aplicarFiltros()
    {
        $this->resetPage();
    }
}
