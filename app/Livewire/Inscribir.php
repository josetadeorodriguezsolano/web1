<?php

namespace App\Livewire;

use Livewire\Component;

class Inscribir extends Component
{

    public $alumno = [
    'matricula' => '',
    'nombres' => '',
    'apellidos' => '',
    'estatus' => 'vigente',  // Valor por defecto
    'curp' => '',
    'contacto' => '',
    'tutor' => ''
];
public $grupoSeleccionado = '';
public $grupos = [];
public $alumnosRecientes = [];

// Métodos necesarios:
// - registrarAlumno()
// - limpiarFormulario()
// - mount() para cargar los grupos disponibles

    public function render()
    {
        return view('livewire.inscribir');
    }
}
