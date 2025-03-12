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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Grupo::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $añoInicio = 2020;
        $añoFinal = date('Y')-1;
        $letras = ['A','B','C','D','E','F'];
        $grados = ['1','2','3'];
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
                    $grupoAnterior = Grupo::where([['grado','1'],
                                                    ['generacion',$generacion-1],
                                                    ['letra',$letra]])->first();
                    if ($grupoAnterior)
                    {
                        echo "si ".$grupo->id;
                        $inscritos = $grupoAnterior->inscritos;
                        foreach ($inscritos as $inscrito)
                        {
                            Inscrito::create(['alumno_id'=>$inscrito->alumno_id,
                                            'grupo_id'=>$grupo->id]);
                        }
                    }
                    else
                    {
                        echo "no ".$grupo->id;
                        $alumnos = Alumno::factory(30)->create();
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
}
