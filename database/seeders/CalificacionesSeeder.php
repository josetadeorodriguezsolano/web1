<?php

namespace Database\Seeders;

use App\Models\Calificacion;
use App\Models\Alumno;
use App\Models\Imparte;
use Illuminate\Database\Seeder;

class CalificacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener alumnos e impartes existentes
        $alumnos = Alumno::all();
        $impartes = Imparte::all();

        // Para cada combinación de alumno e imparte
        foreach ($alumnos->take(10) as $alumno) {
            foreach ($impartes->take(5) as $imparte) {
                // Crear calificación para diferentes unidades
                for ($unidad = 1; $unidad <= 3; $unidad++) {
                    Calificacion::create([
                        'alumno_id' => $alumno->id,
                        'materia_id' => $imparte->materia_id,
                        'unidad' => $unidad,
                        'calificacion' => rand(60, 100) / 10  // Calificación entre 6.0 y 10.0
                    ]);
                }
            }
        }
    }
}
