<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Imparte;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Maestro;
use Illuminate\Support\Facades\DB;

class ImparteSeeder extends Seeder
{
    public function run(): void
    {
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Imparte::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $grupos = Grupo::all();
        $maestros = Maestro::all();

        foreach ($grupos as $grupo) {
            $materias = Materia::where('grado', $grupo->grado)->get();

            foreach ($materias as $materia) {
                Imparte::create([
                    'grupo_id' => $grupo->id,
                    'materia_id' => $materia->id,
                    'maestro_id' => $maestros->random()->id,
                ]);
            }
        }
    }
}
