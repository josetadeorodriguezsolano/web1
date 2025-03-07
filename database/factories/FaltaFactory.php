<?php

namespace Database\Factories;

use App\Models\Falta;
use App\Models\Alumno;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaltaFactory extends Factory
{
    protected $model = Falta::class;

    public function definition(): array
    {
        return [
            'alumno_id' => Alumno::factory(),
            'fecha' => $this->faker->date(),
            'justificada' => $this->faker->boolean(30), // 30% de faltas justificadas
        ];
    }
}
