<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriaSeeder_2 extends Seeder {
    public function run(): void {
        $materias = [
            ['nombre' => 'Matemáticas 1', 'clave' => 'MAT101', 'creditos' => 5],
            ['nombre' => 'Física 1', 'clave' => 'FIS102', 'creditos' => 4],
            ['nombre' => 'Química 1', 'clave' => 'QUI103', 'creditos' => 4],
            ['nombre' => 'Historia 1', 'clave' => 'HIS104', 'creditos' => 3],
            ['nombre' => 'Geografía 1', 'clave' => 'GEO105', 'creditos' => 3],
            ['nombre' => 'Biología 1', 'clave' => 'BIO106', 'creditos' => 4],
            ['nombre' => 'Educación Física 1', 'clave' => 'EDF107', 'creditos' => 2],
            ['nombre' => 'Inglés 1', 'clave' => 'ING108', 'creditos' => 4],
            ['nombre' => 'Español 1', 'clave' => 'ESP109', 'creditos' => 4],
            ['nombre' => 'Artes 1', 'clave' => 'ART110', 'creditos' => 2],

            
        ];

        foreach ($materias as $materia) {
            Materia::create($materia);
        }
    }
}
