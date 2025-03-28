<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PaseDeLista;
use App\Models\Falta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use App\Models\Grupo;
use App\Models\Imparte;
use App\Models\Maestro;

class PaseDeListaTest extends TestCase
{
    /** @test */
    public function renders_successfully()
    {
        $grupo = Grupo::where('generacion',2024)->first();
        $imparte = Imparte::where('grupo_id',$grupo->id)->first();
        $maestro = $imparte->maestro;
        Livewire::actingAs($maestro)->test(PaseDeLista::class)
            ->assertStatus(200);
        $maestro = Maestro::find($maestro->id);
        /*dd($maestro->imparte->map(function($imparte){
            return $imparte->grupo;
        }));*/
        $gruposImpartidos = $maestro->gruposImpartidos(2024);
        $grupo_id = $gruposImpartidos->firstWhere("grupo_id","!=",$grupo->id)->grupo_id;
        $alumno = Grupo::find($grupo_id)->alumnos->first();
        Falta::eliminar($alumno->id);
        //Falta::insertar($alumno->id);
        Livewire::actingAs($maestro)
            ->test(PaseDeLista::class)
            ->set('selectGrupo',$grupo_id)
            ->call('faltas',0)
            ->assertStatus(200)
            ->assertSee($alumno->name);
        $this->assertTrue(Falta::where([['alumno_id',$alumno->id],
            ['fecha',date('Y-m-d')]])
            ->first()!=null);

        $maestro = Maestro::find($maestro->id);
        Livewire::actingAs($maestro)
            ->test(PaseDeLista::class)
            ->set('selectGrupo',$grupo_id)
            ->call('faltas',0)
            ->assertStatus(200)
            ->assertSee($alumno->name);
        $this->assertTrue(Falta::where([['alumno_id',$alumno->id],
            ['fecha',date('Y-m-d')]])
            ->first()==null);

    }
}
