<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Iker',
            'email' => 'iker@example.com',
            'password' => Hash::make('secret123'), // o bcrypt('secret123')
        ]);

        // Puedes agregar más usuarios si quieres:
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpass'),
        ]);
    }
}
