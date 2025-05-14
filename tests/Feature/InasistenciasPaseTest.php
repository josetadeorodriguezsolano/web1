<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Alumno;
use App\Models\Inasistencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class InasistenciasPaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_componente_se_monta_correctamente()
    {
        Livewire::test('inasistencias-pase')
            ->assertStatus(200);
    }

    public function test_se_generan_dias_del_mes()
    {
        // Prepara datos necesarios
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();

        Livewire::test('inasistencias-pase')
            ->set('grupoSeleccionado', $grupo->id)
            ->set('materiaSeleccionada', $materia->id)
            ->set('fechaSeleccionada', '2024-05') // mes cualquiera
            ->call('actualizarTabla')
            ->assertNotEmpty('diasDelMes');
    }


}
