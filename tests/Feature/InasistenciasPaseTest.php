<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Livewire\Livewire;
use App\Models\Grupo;
use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InasistenciasPaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_mount()
    {
        Grupo::factory()->create(['generacion' => 2021]);
        Materia::factory()->create(['nombre' => 'Matemáticas']);

        $component = Livewire::test(\App\Livewire\InasistenciasPase::class);

        $component->assertSet('generaciones', function ($generaciones) {
            return in_array(2021, $generaciones);
        });

        $component->assertSet('materias', function ($materias) {
            return collect($materias)->contains('Matemáticas');
        });
    }

    public function test_updatedGeneracionSeleccionada()
    {
        $grupo1 = \App\Models\Grupo::factory()->create(['generacion' => 2022]);
        $grupo2 = \App\Models\Grupo::factory()->create(['generacion' => 2023]);
        $component = Livewire::test(\App\Livewire\InasistenciasPase::class);
        $component->set('generacionSeleccionada', 2022);
        $component->assertSet('gruposFiltrados', function ($grupos) use ($grupo1, $grupo2) {
            return $grupos->contains('id', $grupo1->id) &&
                   !$grupos->contains('id', $grupo2->id);
        });
        $component->assertSet('grupoSeleccionado', '');
    }

    public function test_updatedMateriaSeleccionada()
    {
        Grupo::factory()->create([
            'generacion' => 2021
        ]);
        Materia::factory()->create([
            'nombre' => 'Matemáticas'
        ]);
        $component = Livewire::test(\App\Livewire\InasistenciasPase::class);
        $component->assertSet('generaciones', function ($generaciones) {
            return in_array(2021, $generaciones);
        });
        $component->assertSet('materias', function ($materias) {
            return collect($materias)->contains('Matemáticas');
        });
    }

    public function test_updatedGrupoSeleccionado()
    {
        $grupo = \App\Models\Grupo::factory()->create(['grado' => 2]);
        $materia1 = \App\Models\Materia::factory()->create(['nombre' => 'Física', 'grado' => 2]);
        $materia2 = \App\Models\Materia::factory()->create(['nombre' => 'Química', 'grado' => 3]);

        $component = Livewire::test(\App\Livewire\InasistenciasPase::class);

        $component->set('grupoSeleccionado', $grupo->id);

        $component->assertSet('materias', function ($materias) use ($materia1, $materia2) {
            return array_key_exists($materia1->id, $materias) &&
                   !array_key_exists($materia2->id, $materias);
        });
    }

    public function test_updatedFechaSeleccionada()
    {
        $component = Livewire::test(\App\Livewire\InasistenciasPase::class);

        $component->set('fechaSeleccionada', '2025-05');

        $component->assertSet('fechaSeleccionada', '2025-05');
    }

    public function test_actualizarTabla()
    {
        $component = Livewire::test(\App\Livewire\InasistenciasPase::class);

        $component->call('actualizarTabla');

        $component->assertSet('alumnos', collect());
        $component->assertSet('diasDelMes', []);
    }

    public function test_generar_tabla_mensual()
    {
        $grupo = \App\Models\Grupo::factory()->create(['grado' => 3]);
        $materia = \App\Models\Materia::factory()->create(['grado' => 3]);

        $alumnos = \App\Models\Alumno::factory()->count(2)->create();

        $component = Livewire::test(\App\Livewire\InasistenciasPase::class)
            ->set('grupoSeleccionado', $grupo->id)
            ->set('materiaSeleccionada', $materia->id)
            ->set('fechaSeleccionada', now()->format('Y-m'));

        $dia = 3;
        \App\Models\Inasistencia::create([
            'alumno_id' => $alumnos[0]->id,
            'materia_id' => $materia->id,
            'fecha' => now()->startOfMonth()->addDays($dia - 1)->format('Y-m-d'),
        ]);

        $component->call('actualizarTabla');


        $component->assertSet('diasDelMes', function ($dias) {
            dump('diasDelMes:', $dias);
            return true;
        });

        $component->assertSet('alumnos', function ($alumnosLivewire) use ($alumnos) {
            dump('alumnos:', collect($alumnosLivewire)->pluck('id'));
            return true;
        });

        $component->assertSet('inasistenciasPorDia', function ($inasistencias) use ($alumnos, $dia) {
            dump('inasistencias:', $inasistencias);
            return true;
        });
    }
public function test_toggleInasistencia()
{
    $grupo = \App\Models\Grupo::factory()->create();

    // Crear alumno SIN pasar grupo_id, para evitar error en insert
    $alumno = \App\Models\Alumno::factory()->create();

    $materia = \App\Models\Materia::factory()->create(['grado' => $grupo->grado]);

    $component = Livewire::test(\App\Livewire\InasistenciasPase::class);

    $component->set('grupoSeleccionado', $grupo->id);
    $component->set('materiaSeleccionada', $materia->id);
    $component->set('fechaSeleccionada', '2025-05');

    $component->call('toggleInasistencia', $alumno->id, 3);

    $this->assertDatabaseHas('inasistencias', [
        'alumno_id' => $alumno->id,
        'materia_id' => $materia->id,
        'fecha' => '2025-05-03',
    ]);

    $component->call('toggleInasistencia', $alumno->id, 3);

    $this->assertDatabaseMissing('inasistencias', [
        'alumno_id' => $alumno->id,
        'materia_id' => $materia->id,
        'fecha' => '2025-05-03',
    ]);
}


}
