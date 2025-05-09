<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Inscrito;
use App\Models\Imparte;
use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class GrupoTest extends TestCase
{
    use RefreshDatabase;

    public function test_se_puede_seleccionar_un_grupo_y_listar_alumnos(): void
    {
    $grupo = Grupo::factory()->create([
        'grado' => 3,
        'letra' => 'A',
        'generacion' => '2022-2025',
    ]);

    $alumnos = Alumno::factory(2)->create(); // Creamos 2 alumnos

    foreach ($alumnos as $alumno) {
        Inscrito::create([
            'grupo_id' => $grupo->id,
            'alumno_id' => $alumno->id
        ]);
    }

    $alumnosObtenidos = $grupo->alumnos;

    $this->assertCount(2, $alumnosObtenidos);
    $this->assertEquals(
        $alumnos->pluck('id')->sort()->values()->toArray(),
        $alumnosObtenidos->pluck('id')->sort()->values()->toArray()
    );
    }


    /** @test */
    public function un_grupo_puede_ser_creado_en_la_base_de_datos()
    {
        $grupo = Grupo::factory()->create();

        $this->assertDatabaseHas('grupos', [
            'id' => $grupo->id,
            'grado' => $grupo->grado,
            'letra' => $grupo->letra,
            'generacion' => $grupo->generacion
        ]);
    }

    /** @test */
    public function un_grupo_puede_tener_muchos_inscritos()
    {
        // Crear un grupo
        $grupo = Grupo::factory()->create();

        // Crear un alumno
        $alumno = Alumno::factory()->create();

        // Crear un inscrito manualmente insertando en la BD
        $inscrito = new Inscrito();
        $inscrito->alumno_id = $alumno->id;
        $inscrito->grupo_id = $grupo->id;
        $inscrito->estatus = 'vigente';
        $inscrito->save();

        // Refrescar el grupo para cargar las relaciones
        $grupo->refresh();

        // Verificar que el grupo tiene el inscrito
        $this->assertEquals(1, $grupo->inscritos->count());
        $this->assertEquals($inscrito->id, $grupo->inscritos->first()->id);
    }

    /** @test */
    public function un_grupo_puede_tener_muchos_alumnos_a_traves_de_inscritos()
    {
        // Crear un grupo
        $grupo = Grupo::factory()->create();

        // Crear un alumno
        $alumno = Alumno::factory()->create();

        // Crear un inscrito manualmente
        $inscrito = new Inscrito();
        $inscrito->alumno_id = $alumno->id;
        $inscrito->grupo_id = $grupo->id;
        $inscrito->estatus = 'vigente';
        $inscrito->save();

        // Refrescar el grupo para cargar las relaciones
        $grupo->refresh();

        // Verificar que el grupo tiene acceso al alumno a través de la relación
        $this->assertEquals(1, $grupo->alumnos->count());
        $this->assertEquals($alumno->id, $grupo->alumnos->first()->id);
    }

    /** @test */
    public function un_grupo_puede_tener_muchas_materias_a_traves_de_imparte()
    {
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        Imparte::factory()->create([
            'grupo_id' => $grupo->id,
            'materia_id' => $materia->id
        ]);

        $this->assertTrue($grupo->materias->contains($materia));
    }
}
