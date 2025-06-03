<?php

namespace Database\Factories;

use App\Models\Horario;
use App\Models\Imparte;
use Illuminate\Database\Eloquent\Factories\Factory;

class HorarioFactory extends Factory
{
    protected $model = Horario::class;

    public function definition()
    {
        return [
            'hora_numero' => $this->faker->numberBetween(1, 7),
            'dia_semana' => $this->faker->randomElement([
                'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'
            ]),
            'imparte_id' => Imparte::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}