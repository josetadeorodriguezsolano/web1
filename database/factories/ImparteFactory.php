<?php

namespace Database\Factories;

use App\Models\maestro;
use App\Models\materia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\imparte>
 */
class ImparteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "materia_id" => materia::pluck('id')->random(),
            "maestro_id" => maestro::pluck('id')->random(),
            "grupo_id" => maestro::pluck('id')->random(),
        ];
    }
}
