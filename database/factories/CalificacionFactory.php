<?php

namespace Database\Factories;

use App\Models\Calificacion;
use App\Models\Alumno;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalificacionFactory extends Factory
{
    protected $model = Calificacion::class;

    public function definition(): array
    {
        return [
            'alumno_id' => Alumno::factory(),
            'materia_id' => Materia::factory(),
            'unidad' => $this->faker->numberBetween(1, 5),  // O usa una cadena si prefieres
            'calificacion' => $this->faker->randomFloat(1, 1.0, 10.0),
        ];
    }
}
