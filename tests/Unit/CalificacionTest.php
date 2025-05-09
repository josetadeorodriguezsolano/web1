<?php

namespace Tests\Unit;


use App\Models\Calificacion;
use App\Models\Alumno;
use App\Models\Imparte;
use App\Models\Materia;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;


class CalificacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creacion_calificacion_con_unidad()
    {
        $alumno = Alumno::factory()->create();
        $imparte = Imparte::factory()->create();

        $calificacion = Calificacion::create([
            'alumno_id' => $alumno->id,
            'imparte_id' => $imparte->id,
            'unidad' => 'Unidad 1',
            'calificacion' => 9.5
        ]);

        $this->assertDatabaseHas('calificaciones', [
            'alumno_id' => $alumno->id,
            'imparte_id' => $imparte->id,
            'unidad' => 'Unidad 1',
            'calificacion' => 9.5
        ]);
    }

    //Guardar, Editar, Listar
    public function test_guardar_calificacion(): void
    {
    $alumno = Alumno::factory()->create();
    $materia = Materia::factory()->create();

    $calificacion = Calificacion::create([
        'alumno_id' => $alumno->id,
        'materia_id' => $materia->id,
        'unidad' => 1,
        'calificacion' => 9.5
    ]);

    $this->assertDatabaseHas('calificaciones', [
        'alumno_id' => $alumno->id,
        'materia_id' => $materia->id,
        'unidad' => 1,
        'calificacion' => 9.5
    ]);
    }

    public function test_editar_calificacion(): void
    {
    $alumno = Alumno::factory()->create();
    $materia = Materia::factory()->create();

    $calificacion = Calificacion::create([
        'alumno_id' => $alumno->id,
        'materia_id' => $materia->id,
        'unidad' => 1,
        'calificacion' => 7.0
    ]);

    $calificacion->update(['calificacion' => 8.8]);

    $this->assertDatabaseHas('calificaciones', [
        'id' => $calificacion->id,
        'calificacion' => 8.8
    ]);
    }

    public function test_listar_calificaciones_de_alumno(): void
    {
    $alumno = Alumno::factory()->create();
    $materia1 = Materia::factory()->create();
    $materia2 = Materia::factory()->create();

    Calificacion::create([
        'alumno_id' => $alumno->id,
        'materia_id' => $materia1->id,
        'unidad' => 1,
        'calificacion' => 9.0
    ]);

    Calificacion::create([
        'alumno_id' => $alumno->id,
        'materia_id' => $materia2->id,
        'unidad' => 2,
        'calificacion' => 8.0
    ]);

    $calificaciones = $alumno->calificaciones;

    $this->assertCount(2, $calificaciones);
    $this->assertEquals(9.0, $calificaciones[0]->calificacion);
    $this->assertEquals(8.0, $calificaciones[1]->calificacion);
    }



      /** @test */
    public function test_relacion_con_alumno_y_imparte()
    {
        $alumno = Alumno::factory()->create();
        $imparte = Imparte::factory()->create();

        $calificacion = Calificacion::create([
            'alumno_id' => $alumno->id,
            'imparte_id' => $imparte->id,
            'unidad' => 'Unidad 2',
            'calificacion' => 8.5
        ]);

        $this->assertEquals($alumno->id, $calificacion->alumno->id);
        $this->assertEquals($imparte->id, $calificacion->imparte->id);
    }

}
