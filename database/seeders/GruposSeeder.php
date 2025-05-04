<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Database\Seeder;
use App\Models\Inscrito;
use Illuminate\Support\Facades\DB;

class GruposSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Grupo::query()->delete();
Alumno::query()->delete();
Inscrito::query()->delete();

        $añoInicio = 2020;
        $añoFinal = date('Y')-1;
        $letras = ['A','B','C','D','E','F'];
        $grados = [1,2,3];
        for ($generacion = $añoInicio; $generacion<=$añoFinal; $generacion++)
        {
            foreach ($grados as $grado)
            {
                foreach ($letras as $letra)
                {
                    $grupo = Grupo::create(['grado' => $grado,
                                   'letra' => $letra,
                                   'generacion' => $generacion]);
                    $alumnos = [];
                    if ($grado == 1)
                    {
                        $alumnos = Alumno::factory(30)->create();
                    }
                    else
                    {
                        $grupoAnterior = Grupo::where([['grado', $grado-1],
                                                    ['letra',$letra],
                                                    ['generacion',$generacion-1]])->first();
                        if ($grupoAnterior == null)
                            $alumnos = Alumno::factory(30)->create();
                        else
                            $alumnos = $grupoAnterior->alumnos;
                    }
                    foreach ($alumnos as $alumno)
                    {
                        Inscrito::create(['alumno_id'=>$alumno->id,
                                        'grupo_id'=>$grupo->id]);
                    }
                }
            }
        }
    }
}
