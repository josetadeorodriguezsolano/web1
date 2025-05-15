<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Imparte;
use Illuminate\Database\Seeder;
use App\Models\Maestro;
use App\Models\Materia;

class ImparteSeeder extends Seeder
{
    public function run(): void
    {
        $maestros = Maestro::all();
        $grupos = Grupo::all();

        foreach ($grupos as $grupo) {
            // se asignara solo la materia del mismo grado que el grupo
            $materias = Materia::where('grado', $grupo->grado)->get();

            foreach ($materias as $materia) {
                Imparte::create([
                    'materia_id' => $materia->id,
                    'grupo_id' => $grupo->id,
                    //es para que ponga maestros que no tengan materias
                    'maestro_id' => rand(0, 1) ? $maestros->random()->id : null
                ]);
            }
        }
    }
}