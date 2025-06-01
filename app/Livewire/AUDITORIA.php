<?php

namespace App\Livewire;

use App\Models\Maestro;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AUDITORIA extends Component
{
    public $auditorias = [];
    public $tipoConsulta = ''; // 'horario' o 'inasistencia'

    public function updatedTipoConsulta()
    {
        $this->cargarAuditorias();
    }
public function cargarAuditorias()
{
    if ($this->tipoConsulta === 'horario') {
        $modelo = 'App\\Models\\Horario';
    } elseif ($this->tipoConsulta === 'inasistencia') {
        $modelo = 'App\\Models\\Inasistencia';
    } else {
        $this->auditorias = [];
        return;
    }

    $this->auditorias = DB::table('audits')
        ->join('maestros', 'maestros.id', '=', 'audits.user_id')
        ->select(
            'audits.*',
            'maestros.name as nombre_maestro',
            'maestros.apellidos as apellidos_maestro'
        )
        ->where('audits.auditable_type', $modelo)
        ->where('audits.user_type', 'App\\Models\\Maestro')
        ->orderBy('audits.created_at', 'desc')
        ->get()
        ->map(function ($item) {
            $item->old_values = json_decode($item->old_values, true) ?? [];
            $item->new_values = json_decode($item->new_values, true) ?? [];
            return $item;
        });
}

    public function render()
    {
        return view('livewire.AUDITORIA');
    }
}
