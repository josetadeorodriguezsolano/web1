<?php

namespace Database\Factories;

use App\Models\Grupo;
use App\Models\Alumno;
use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    protected $model = Grupo::class;

    public function definition(): array
    {
        return [
            'grado' => $this->faker->randomElement(['1','2','3']),
            'letra' => $this->faker->randomElement(['A','B','C','D','E','F']),
            'generacion' => $this->faker->randomElement(range(2021, date('Y') - 1)),
        ];
    }
}
