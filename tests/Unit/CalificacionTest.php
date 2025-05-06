<?php

namespace Tests\Unit;

use App\Models\Calificacion;
use App\Models\Alumno;
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
        $materia = Materia::factory()->create();

        $calificacion = Calificacion::create([
            'alumno_id' => $alumno->id,
            'materia_id' => $materia->id,
            'unidad' => 'Unidad 1',
            'calificacion' => 9.5
        ]);

        $this->assertDatabaseHas('calificaciones', [
            'alumno_id' => $alumno->id,
            'materia_id' => $materia->id,
            'unidad' => 'Unidad 1',
            'calificacion' => 9.5
        ]);
    }

    /** @test */
    public function test_calificacion_fuera_de_rango()
    {
        $this->expectException(ValidationException::class);

        // Creación de calificación fuera de rango
        Calificacion::create([
            'alumno_id' => 1,
            'materia_id' => 1,
            'unidad' => 1,
            'calificacion' => 11.0, // Calificación fuera de rango
        ]);
    }

    /** @test */
    public function test_relacion_con_alumno_y_materia()
    {
        $alumno = Alumno::factory()->create();
        $materia = Materia::factory()->create();

        $calificacion = Calificacion::create([
            'alumno_id' => $alumno->id,
            'materia_id' => $materia->id,
            'unidad' => 'Unidad 2',
            'calificacion' => 8.5
        ]);

        $this->assertEquals($alumno->id, $calificacion->alumno->id);
        $this->assertEquals($materia->id, $calificacion->materia->id);
    }
}
