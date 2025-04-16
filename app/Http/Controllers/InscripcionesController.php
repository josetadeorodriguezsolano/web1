<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InscripcionesController extends Controller
{
      public function index()
    {
        // Simulamos datos por ahora
        $datos = [
            'ciclo_actual' => '2025-1',
            'estado_inscripcion' => 'Abierto',
            'total_materias' => 120,
            'total_inscritos' => 4320,
            'materias_llenas' => 15,
        ];

        return view('admin.index', compact('datos'));
    }
}
