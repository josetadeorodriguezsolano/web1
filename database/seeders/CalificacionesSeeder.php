<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Calificacion;
use App\Models\Imparte;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Materia;

class CalificacionesSeeder extends Seeder
{
    public function run(): void
    {
        $impartes = Imparte::with(['grupo', 'materia', 'maestro'])->get();

        foreach ($impartes as $imparte) {
            $alumnos = $imparte->grupo->alumnos;

            // Para cada alumno, generar calificaciones para las unidades 1-4
            foreach ($alumnos as $alumno) {
                for ($unidad = 1; $unidad <= 4; $unidad++) {
                    if (rand(1, 100) > 20) {
                        // Generar calificación aleatoria entre 5.0 y 10.0
                        // Con más probabilidad entre 6.0 y 9.0
                        $base = rand(5, 10);
                        $decimal = $base < 10 ? rand(0, 9) / 10 : 0;
                        $calificacion = $base + $decimal;

                        Calificacion::create([
                            'alumno_id' => $alumno->id,
                            'materia_id' => $imparte->materia_id,
                            'grupo_id' => $imparte->grupo_id,
                            'imparte_id' => $imparte->id,
                            'unidad' => $unidad,
                            'calificacion' => $calificacion
                        ]);
                    }
                }
            }
        }

        $this->command->info('Se han creado ' . Calificacion::count() . ' calificaciones de prueba.');
    }
}
