<?php

namespace Tests\Feature;

use App\Models\Inscrito;
use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscritoTest extends TestCase
{
    use RefreshDatabase;

    public function test_se_puede_crear_un_inscrito()
    {
        $alumno = Alumno::factory()->create();
        $grupo = Grupo::factory()->create();

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente',
        ]);

        $this->assertDatabaseHas('inscritos', [
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente',
        ]);
    }

    public function test_se_puede_leer_un_registro_inscrito()
    {
        $inscrito = Inscrito::factory()->create(); // Usamos factory para crear rápido

        $encontrado = Inscrito::find($inscrito->id);

        $this->assertNotNull($encontrado);
        $this->assertEquals($inscrito->alumno_id, $encontrado->alumno_id);
        $this->assertEquals($inscrito->grupo_id, $encontrado->grupo_id);
    }

    public function test_se_puede_actualizar_un_registro_inscrito()
    {
        $inscrito = Inscrito::factory()->create();
        $nuevoEstatus = 'baja';

        $inscrito->update([
            'estatus' => $nuevoEstatus,
        ]);

        $inscrito->refresh();

        $this->assertEquals($nuevoEstatus, $inscrito->estatus);
        $this->assertDatabaseHas('inscritos', [
            'id' => $inscrito->id,
            'estatus' => $nuevoEstatus,
        ]);
    }

    public function test_se_puede_eliminar_un_registro_inscrito()
    {
        $inscrito = Inscrito::factory()->create();

        $inscrito->delete();

        $this->assertDatabaseMissing('inscritos', ['id' => $inscrito->id]);
    }

    public function test_tiene_relacion_valida_con_alumno()
    {
        $alumno = Alumno::factory()->create();
        $grupo = Grupo::factory()->create();

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente',
        ]);

        $this->assertInstanceOf(Alumno::class, $inscrito->alumno);
        $this->assertEquals($alumno->id, $inscrito->alumno->id);
    }

    public function test_tiene_relacion_valida_con_grupo()
    {
        $alumno = Alumno::factory()->create();
        $grupo = Grupo::factory()->create();

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente',
        ]);

        $this->assertInstanceOf(Grupo::class, $inscrito->grupo);
        $this->assertEquals($grupo->id, $inscrito->grupo->id);
    }

    public function test_puede_tener_grado()
    {
        $alumno = Alumno::factory()->create();
        $grupo = Grupo::factory()->create(['grado' => 3]);

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente',
        ]);

        $this->assertEquals(3, $inscrito->grado);
    }

    public function test_puede_tener_generacion()
    {
        $alumno = Alumno::factory()->create();
        $grupo = Grupo::factory()->create(['generacion' => 2022]);

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente',
        ]);

        $this->assertEquals(2022, $inscrito->generacion);
    }

    public function test_puede_tener_salon()
    {
        $alumno = Alumno::factory()->create();
        $grupo = Grupo::factory()->create(['letra' => 'B']);

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente',
        ]);

        $this->assertEquals('B', $inscrito->salon);
    }
}
