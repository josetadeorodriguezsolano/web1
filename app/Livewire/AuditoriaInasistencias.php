<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use OwenIt\Auditing\Models\Audit;
use App\Models\Alumno;
use App\Models\Materia;

class AuditoriaInasistencias extends Component
{
    use WithPagination;

    public $matricula = '';
    public $materiaId = '';
    public $eventos = ['created', 'updated', 'deleted'];


    protected $queryString = [
        'matricula' => ['except' => ''],
        'materiaId' => ['except' => ''],
        'page' => ['except' => 1]
    ];

    public function mount()
    {
    }

    public function render()
    {
        $audits = Audit::with(['user', 'auditable'])
            ->where('auditable_type', 'App\Models\Inasistencia')
            ->when($this->matricula, function ($query) {
                $alumno = Alumno::where('matricula', $this->matricula)->first();
                if ($alumno) {
                    $query->whereHas('auditable', function ($q) use ($alumno) {
                        $q->where('alumno_id', $alumno->id);
                    });
                } else {
                    $query->whereRaw('0 = 1');
                }
            })
            
            ->when($this->materiaId, function ($query) {
                $query->whereHas('auditable', function ($q) {
                    $q->where('materia_id', $this->materiaId);
                });
            })
            ->whereIn('event', $this->eventos)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.auditoria-inasistencias', [
            'audits' => $audits,
            'materias' => Materia::all()
        ]);
    }

    public function aplicarFiltros()
    {
        $this->resetPage();
    }
}