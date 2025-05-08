<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Materia;
use App\Models\Imparte;
use App\Models\Calificacion;
use App\Models\Alumno;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MateriaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function una_materia_puede_ser_creada_en_la_base_de_datos()
    {
        $materia = Materia::factory()->create();

        $this->assertDatabaseHas('materias', [
            'id' => $materia->id,
            'nombre' => $materia->nombre,
            'clave' => $materia->clave,
            'creditos' => $materia->creditos,
            'grado' => $materia->grado
        ]);
    }

    /** @test */
    public function una_materia_puede_ser_filtrada_por_grado()
    {
        $gradoBuscado = '2';
        $materiaCorrecta = Materia::factory()->create(['grado' => $gradoBuscado]);
        $materiaIncorrecta = Materia::factory()->create(['grado' => '3']);

        $materiasFiltradas = Materia::porGrado($gradoBuscado)->get();

        $this->assertTrue($materiasFiltradas->contains($materiaCorrecta));
        $this->assertFalse($materiasFiltradas->contains($materiaIncorrecta));
    }

    /** @test */
    public function una_materia_puede_tener_muchas_imparticiones()
    {
        $materia = Materia::factory()->create();
        $imparte = Imparte::factory()->create(['materia_id' => $materia->id]);

        $materia->refresh();
        $this->assertEquals(1, $materia->impartes->count());
        $this->assertEquals($imparte->id, $materia->impartes->first()->id);
    }

    /** @test */
    public function una_materia_puede_tener_muchas_calificaciones()
    {
        $materia = Materia::factory()->create();
        $alumno = Alumno::factory()->create();

        // Crear calificación manualmente
        $calificacion = new Calificacion();
        $calificacion->alumno_id = $alumno->id;
        $calificacion->materia_id = $materia->id;
        $calificacion->unidad = 1;
        $calificacion->calificacion = 8.5;
        $calificacion->save();

        $materia->refresh();
        $this->assertEquals(1, $materia->calificaciones->count());
        $this->assertEquals($calificacion->id, $materia->calificaciones->first()->id);
    }
}
