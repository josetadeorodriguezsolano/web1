<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriasSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $materias = [
            ['nombre' => 'Matemáticas', 'clave' => 'MAT101', 'creditos' => 5,'grado' => '1'],
            ['nombre' => 'Física', 'clave' => 'FIS102', 'creditos' => 4,'grado' => '1'],
            ['nombre' => 'Química', 'clave' => 'QUI103', 'creditos' => 4,'grado' => '1'],
            ['nombre' => 'Historia', 'clave' => 'HIS104', 'creditos' => 3,'grado' => '1'],
            ['nombre' => 'Geografía', 'clave' => 'GEO105', 'creditos' => 3,'grado' => '1'],
            ['nombre' => 'Biología', 'clave' => 'BIO106', 'creditos' => 4,'grado' => '1'],
            ['nombre' => 'Educación Física', 'clave' => 'EDF107', 'creditos' => 2,'grado' => '1'],
            ['nombre' => 'Inglés', 'clave' => 'ING108', 'creditos' => 4,'grado' => '1'],
            ['nombre' => 'Español', 'clave' => 'ESP109', 'creditos' => 4,'grado' => '1'],
            ['nombre' => 'Artes', 'clave' => 'ART110', 'creditos' => 2,'grado' => '1'],

            ['nombre' => 'Matemáticas 2', 'clave' => 'MAT201', 'creditos' => 5,'grado' => '2'],
            ['nombre' => 'Física 2', 'clave' => 'FIS202', 'creditos' => 4,'grado' => '2'],
            ['nombre' => 'Química 2', 'clave' => 'QUI203', 'creditos' => 4,'grado' => '2'],
            ['nombre' => 'Historia 2', 'clave' => 'HIS204', 'creditos' => 3,'grado' => '2'],
            ['nombre' => 'Geografía 2', 'clave' => 'GEO205', 'creditos' => 3,'grado' => '2'],
            ['nombre' => 'Biología 2', 'clave' => 'BIO206', 'creditos' => 4,'grado' => '2'],
            ['nombre' => 'Educación Física 2', 'clave' => 'EDF207', 'creditos' => 2,'grado' => '2'],
            ['nombre' => 'Inglés 2', 'clave' => 'ING208', 'creditos' => 4,'grado' => '2'],
            ['nombre' => 'Español 2', 'clave' => 'ESP209', 'creditos' => 4,'grado' => '2'],
            ['nombre' => 'Artes 2', 'clave' => 'ART210', 'creditos' => 2,'grado' => '2'],

            ['nombre' => 'Matemáticas 3', 'clave' => 'MAT301', 'creditos' => 5,'grado' => '3'],
            ['nombre' => 'Física 3', 'clave' => 'FIS302', 'creditos' => 4,'grado' => '3'],
            ['nombre' => 'Química 3', 'clave' => 'QUI303', 'creditos' => 4,'grado' => '3'],
            ['nombre' => 'Historia 3', 'clave' => 'HIS304', 'creditos' => 3,'grado' => '3'],
            ['nombre' => 'Geografía 3', 'clave' => 'GEO305', 'creditos' => 3,'grado' => '3'],
            ['nombre' => 'Biología 3', 'clave' => 'BIO306', 'creditos' => 4,'grado' => '3'],
            ['nombre' => 'Educación Física 3', 'clave' => 'EDF307', 'creditos' => 2,'grado' => '3'],
            ['nombre' => 'Inglés 3', 'clave' => 'ING308', 'creditos' => 4,'grado' => '3'],
            ['nombre' => 'Español 3', 'clave' => 'ESP309', 'creditos' => 4,'grado' => '3'],
            ['nombre' => 'Artes 3', 'clave' => 'ART310', 'creditos' => 2,'grado' => '3'],
        ];

        foreach ($materias as $materia) {
            Materia::create($materia);
        }
    }
}
