<?php

namespace Database\Factories;

use App\Models\Calificacion;
use App\Models\Alumno;
use App\Models\Imparte;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalificacionFactory extends Factory
{
    protected $model = Calificacion::class;

    public function definition(): array
    {
        return [
            'alumno_id'     => Alumno::factory(),
            'imparte_id'    => Imparte::factory(),
            'unidad' => $this->faker->word, // Unidad aleatoria
            'calificacion' => $this->faker->randomFloat(1, 1.0, 10.0),
        ];
    }
}
