<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MateriaFactory extends Factory {
    public function definition(): array {
        return [
            'nombre' => $this->faker->word(),
            'clave' => strtoupper($this->faker->unique()->lexify('MAT???')),
            'creditos' => $this->faker->numberBetween(2, 6),
            'grado' => $this->faker->randomElement(['1','2','3']),
        ];
    }
}
