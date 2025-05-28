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
        // Verificación para evitar errores si faltan datos
        if (Grupo::count() === 0 || Materia::count() === 0 || Maestro::count() === 0) {
            $this->command->warn('Seeder omitido: asegúrate de tener grupos, materias y maestros en la base de datos.');
            return;
        }

        // Limpiar tabla
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Imparte::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $grupos = Grupo::all();
        $maestros = Maestro::all()->keyBy('id');

        foreach ($grupos as $grupo) {
            // Obtener materias según el grado del grupo
            $materias = Materia::where('grado', $grupo->grado)->get();

            foreach ($materias as $materia) {
                Imparte::create([
                    'grupo_id' => $grupo->id,
                    'materia_id' => $materia->id,
                    'maestro_id' => $maestros->random()->id,
                ]);
            }
        }

        $this->command->info('ImparteSeeder completado exitosamente.');
    }
}
