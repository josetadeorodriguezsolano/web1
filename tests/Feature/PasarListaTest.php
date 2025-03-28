<?php

namespace Tests\Feature;

use App\Models\Grupo;
use App\Models\Imparte;
use App\Models\Maestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PasarListaTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_pase_de_lista(): void
    {
        $grupo = Grupo::where('generacion',2024)->first();
        $imparte = Imparte::where('grupo_id',$grupo->id)->first();
        $maestro = $imparte->maestro;
        $response = $this->actingAs($maestro)->get('/pase_de_lista');
        $response->assertStatus(200);

        $alumno = $grupo->alumnos->first();
        $response = $this->actingAs($maestro)
            ->get('pase_de_lista/inasistencia/vino/'.$alumno->id);
        $response->assertStatus(200);
        $response->assertContent("true");

        $response = $this->actingAs($maestro)
            ->get('pase_de_lista/inasistencia/falto/'.$alumno->id);
        $response->assertStatus(200);
        $response->assertContent("false");
    }
}
