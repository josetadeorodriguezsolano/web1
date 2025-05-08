<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Maestro;
use App\Models\Imparte;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaestroTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_maestro_puede_ser_creado_en_la_base_de_datos()
    {
        $maestro = Maestro::factory()->create();

        $this->assertDatabaseHas('maestros', [
            'id' => $maestro->id,
            'name' => $maestro->name,
            'email' => $maestro->email
        ]);
    }

    /** @test */
    public function un_maestro_puede_tener_muchas_materias_impartidas()
    {
        $maestro = Maestro::factory()->create();
        $imparte = Imparte::factory()->create(['maestro_id' => $maestro->id]);

        $this->assertTrue($maestro->imparte->contains($imparte));
        $this->assertEquals(1, $maestro->imparte->count());
    }

    /** @test */
    public function un_maestro_puede_filtrar_grupos_por_generacion()
    {
        $maestro = Maestro::factory()->create();
        $generacion = date('Y') - 1; // Generación del año pasado

        // Crear un imparte para esta generación
        $imparteCorrecta = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'grupo_id' => \App\Models\Grupo::factory()->create(['generacion' => $generacion])->id
        ]);

        // Crear un imparte para otra generación
        $imparteIncorrecta = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'grupo_id' => \App\Models\Grupo::factory()->create(['generacion' => $generacion - 1])->id
        ]);

        $gruposImpartidos = $maestro->gruposImpartidos($generacion);

        $this->assertTrue($gruposImpartidos->contains($imparteCorrecta));
        $this->assertFalse($gruposImpartidos->contains($imparteIncorrecta));
    }
}
