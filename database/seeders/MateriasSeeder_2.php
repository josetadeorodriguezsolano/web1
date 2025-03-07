<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriaSeeder extends Seeder {
    public function run(): void {
        $materias = [
            ['nombre' => 'Matemáticas', 'clave' => 'MAT101', 'creditos' => 5],
            ['nombre' => 'Física', 'clave' => 'FIS102', 'creditos' => 4],
            ['nombre' => 'Química', 'clave' => 'QUI103', 'creditos' => 4],
            ['nombre' => 'Historia', 'clave' => 'HIS104', 'creditos' => 3],
            ['nombre' => 'Geografía', 'clave' => 'GEO105', 'creditos' => 3],
            ['nombre' => 'Biología', 'clave' => 'BIO106', 'creditos' => 4],
            ['nombre' => 'Educación Física', 'clave' => 'EDF107', 'creditos' => 2],
            ['nombre' => 'Inglés', 'clave' => 'ING108', 'creditos' => 4],
            ['nombre' => 'Español', 'clave' => 'ESP109', 'creditos' => 4],
            ['nombre' => 'Artes', 'clave' => 'ART110', 'creditos' => 2],
        ];

        foreach ($materias as $materia) {
            Materia::create($materia);
        }
    }
}