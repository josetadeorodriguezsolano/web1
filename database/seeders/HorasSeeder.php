<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HorasSeeder extends Seeder
{
    public function run()
    {
        DB::table('horas')->insert([
            ['numero' => 1, 'inicio' => '07:00:00'],
            ['numero' => 2, 'inicio' => '07:50:00'],
            ['numero' => 3, 'inicio' => '08:40:00'],
            ['numero' => 4, 'inicio' => '09:30:00'],
            ['numero' => 5, 'inicio' => '10:20:00'],
            ['numero' => 6, 'inicio' => '11:10:00'],
        ]);
    }
}