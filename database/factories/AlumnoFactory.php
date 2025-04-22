<?php

namespace Database\Factories;

use Faker\Generator as FakerGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Alumno;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AlumnoFactory extends Factory
{
    protected $model = Alumno::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'matricula' => strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4)) . $this->faker->randomNumber(4, true),
            'nombres' => 'Juan Gustavo',
            'apellidos' => 'Corrales Cerveza',
           // 'estatus'   => $this->faker->randomElement(['vigente', 'egresado', 'baja']),
            'curp'      => strtoupper(
                substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3) .
                $this->faker->numerify('######') .
                'H' .
                substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6) .
                $this->faker->numerify('##')
            ),
            'contacto'  => $this->faker->numerify('##########'),
            'tutor'     => 'Berenice Talamantes',
        ];
    }
    public function withFaker(): FakerGenerator
    {
        return \Faker\Factory::create('es_MX'); // o 'es_ES', o 'en_US'
    }

}
