<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Imparte;
use App\Models\Grupo;
use App\Models\Maestro;
use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImparteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_imparte_puede_ser_creado_en_la_base_de_datos()
    {
        $imparte = Imparte::factory()->create();

        $this->assertDatabaseHas('imparte', [
            'id' => $imparte->id,
            'materia_id' => $imparte->materia_id,
            'maestro_id' => $imparte->maestro_id,
            'grupo_id' => $imparte->grupo_id
        ]);
    }

    /** @test */
    public function un_imparte_pertenece_a_un_grupo()
    {
        $grupo = Grupo::factory()->create();
        $imparte = Imparte::factory()->create(['grupo_id' => $grupo->id]);

        $this->assertEquals($grupo->id, $imparte->grupo->id);
    }

    /** @test */
    public function un_imparte_pertenece_a_un_maestro()
    {
        $maestro = Maestro::factory()->create();
        $imparte = Imparte::factory()->create(['maestro_id' => $maestro->id]);

        $this->assertEquals($maestro->id, $imparte->maestro->id);
    }

    /** @test */
    public function un_imparte_pertenece_a_una_materia()
    {
        $materia = Materia::factory()->create();
        $imparte = Imparte::factory()->create(['materia_id' => $materia->id]);

        $this->assertEquals($materia->id, $imparte->materia->id);
    }
}
