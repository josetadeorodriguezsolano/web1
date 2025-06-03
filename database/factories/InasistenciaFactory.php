<?php

namespace Database\Factories;

use App\Models\Inasistencia;
use App\Models\Imparte;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Factories\Factory;

class InasistenciaFactory extends Factory
{
    protected $model = Inasistencia::class;

    public function definition(): array
    {
        return [
            'fecha' => $this->faker->date(),
            'justificacion' => $this->faker->sentence(),
            'imparte_id' => Imparte::factory(),
            'horario_id' => function (array $attributes) {
                // Crear un horario relacionado con el imparte
                return Horario::factory()->create([
                    'imparte_id' => $attributes['imparte_id']
                ])->id;
            },
        ];
    }
}