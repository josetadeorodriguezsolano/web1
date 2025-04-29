<?php

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Falta;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlumnoTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic unit test example.
     */
    public function test_creacion_de_alumno()
    {
        $alumno = Alumno::factory()->create([
            'matricula' => 'A123456789',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'estatus' => 'vigente',
            'curp' => 'CURP123456HDFABC01',
            'contacto' => '5551234567',
            'tutor' => 'Carlos Pérez'
        ]);

        $this->assertDatabaseHas('alumnos', [
            'id' => $alumno->id,
            'curp' => 'CURP123456HDFABC01'
        ]);
    }

    public function test_se_puede_leer_un_alumno()
    {
        $alumno = Alumno::factory()->create(); // Usamos factory para crear rápido

        $encontrado = Alumno::find($alumno->id);

        $this->assertNotNull($encontrado);
        $this->assertEquals($alumno->matricula, $encontrado->matricula);
    }

    public function test_se_puede_actualizar_un_alumno()
    {
        $alumno = Alumno::factory()->create();
        $nuevoContacto = '6129876543'; // Otro número de ejemplo
        $nuevoTutor = 'María García';

        $alumno->update([
            'contacto' => $nuevoContacto,
            'tutor' => $nuevoTutor,
        ]);

        $alumno->refresh();

        $this->assertEquals($nuevoContacto, $alumno->contacto);
        $this->assertEquals($nuevoTutor, $alumno->tutor);
        $this->assertDatabaseHas('alumnos', [
            'id' => $alumno->id,
            'contacto' => $nuevoContacto,
            'tutor' => $nuevoTutor,
        ]);
    }

    public function test_curp_unico()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Alumno::factory()->create([
            'curp' => 'CURP123456HDFABC01'
        ]);

        // Intentar insertar otro con el mismo CURP
        Alumno::factory()->create([
            'curp' => 'CURP123456HDFABC01'
        ]);
    }

    public function test_estatus_por_defecto()
    {
        // Creamos un alumno sin pasarle el campo 'estatus'
        $alumno = Alumno::factory()->create();

        // Comprobamos que el estatus realmente quedó como 'vigente'
        $this->assertEquals('vigente', $alumno->estatus);
    }

    public function test_falto(): void
    {
        $alumno = Alumno::factory(1)->create()->first();
        $falta = Falta::create(['alumno_id'=>$alumno->id,
                        'fecha'=>date('Y-m-d')]);
        $this->assertTrue($alumno->falto(date('Y-m-d'))->id == $falta->id);
    }
}
