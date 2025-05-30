<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MaestrosSeeder::class,
            MateriasSeeder_2::class,
            GruposSeeder::class,
            HorasSeeder::class,
            ImparteSeeder::class,
            FaltasSeeder::class,
            HorariosSeeder::class,
            UserSeeder::class,
        ]);
    }
}
