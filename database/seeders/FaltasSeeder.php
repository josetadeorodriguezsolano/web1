<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Falta;

class FaltasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Falta::factory()->count(50)->create(); // Genera 50 registros de prueba
    }
}
