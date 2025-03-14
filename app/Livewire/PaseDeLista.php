<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Grupo;

class PaseDeLista extends Component
{
    public function render()
    {
        $maestro = Auth::user();
        $gruposImpartidos = $maestro->gruposImpartidos(2024);
        $grupo = Grupo::find(1);
        return view('livewire.pase-de-lista',
            ['gruposImpartidos'=> $gruposImpartidos,
            'grupo'=> $grupo]);
    }
}
