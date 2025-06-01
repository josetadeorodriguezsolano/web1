<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Horario;
use App\Models\Imparte;
use App\Models\Hora;
use Illuminate\Support\Facades\DB;

class HorariosSeeder extends Seeder
{
    public function run()
    {
        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes'];
        $horas = [1, 2, 3, 4, 5, 6];

        $impartes = Imparte::all();

        foreach ($impartes as $imparte) {
            $cantidadHorarios = rand(2, 5);
            $combinaciones = [];

            for ($i = 0; $i < $cantidadHorarios; $i++) {
                do {
                    $dia = $dias[array_rand($dias)];
                    $hora = $horas[array_rand($horas)];
                    $clave = $dia . '_' . $hora;
                } while (in_array($clave, $combinaciones));

                $combinaciones[] = $clave;

                $horaId = Hora::where('numero', $hora)->first()->id;

                Horario::create([
                    'imparte_id' => $imparte->id,
                    'dia' => $dia,
                    'hora_id' => $horaId, 
                ]);
            }
        }
    }
}
