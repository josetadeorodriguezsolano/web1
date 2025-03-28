<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
USE App\Models\alumno;
use Illuminate\Support\Testing\Fakes\Fake;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AlumnoFactory extends Factory
{
    protected $model = alumno::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'matricula' => $this->faker->regexify('[A-Z0-9]{10}'),
            'nombres'   => $this->faker->firstName,
            'apellidos' => $this->faker->lastName,
            'estatus'   => $this->faker->randomElement(['vigente', 'egresado', 'baja']),
            'curp'      => strtoupper($this->faker->unique()->regexify('[A-Z0-9]{18}')),
            'contacto'  => $this->faker->numerify('##########'),
            'tutor'     => $this->faker->name,
        ];
    }
}
