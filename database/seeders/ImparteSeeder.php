<?php

namespace Database\Seeders;

use App\Models\Grupo;
use App\Models\Imparte;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Maestro;
use App\Models\Materia;

class ImparteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maestros = Maestro::all();
        $grupos = Grupo::all();
        $materias = [];
        foreach ($grupos as $grupo) {
            $materias = Materia::porGrado($grupo->grado);
            foreach ($materias as $materia) {
                Imparte::create(['materia_id'=>$materia->id,
                                 'grupo_id'=>$grupo->id,
                                 'maestro_id'=> $maestros->random()->id]);
            }
        }
    }
}
