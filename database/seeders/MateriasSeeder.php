<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriasSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $grados = ['1','2','3'];
        foreach ($grados as $grado){
            Materia::factory(6)->create(['grado' => $grado]);
        }
    }
}
