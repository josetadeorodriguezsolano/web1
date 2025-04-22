<?php

namespace Database\Factories;

use App\Models\Imparte;
use App\Models\Materia;
use App\Models\Maestro;
use App\Models\Grupo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\imparte>
 */
class ImparteFactory extends Factory
{
    protected $model = Imparte::class;

    public function definition(): array
    {
        return [
            'materia_id' => Materia::factory(),
            'maestro_id' => Maestro::factory(),
            'grupo_id'   => Grupo::factory(),
        ];
    }
}
