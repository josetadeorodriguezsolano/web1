<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;
use App\Models\Materia;

class GruposSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $añoInicio = 2020;
        $añoFinal = date('Y')-1;
        $letras = ['A','B','C','D','E','F'];
        $grados = ['1','2','3'];
        for ($generacion = $añoInicio; $generacion<=$añoFinal; $generacion++){
            foreach ($grados as $grado){
                foreach ($letras as $letra) {
                    Grupo::create(['grado' => $grado,
                                   'letra' => $letra,
                                   'generacion' => $generacion]);
                }
            }
        }
    }
}
