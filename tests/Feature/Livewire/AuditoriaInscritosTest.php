<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Inscritos;
use App\Models\Grupo;
use App\Models\Inscrito;
use App\Models\Alumno;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuditoriaInscritosTest extends TestCase
{
    use RefreshDatabase;
    protected $user;


    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario para las pruebas
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /** @test */
    public function el_componente_se_renderiza_correctamente()
    {
        $this->actingAs($this->user);

        Livewire::test(Inscritos::class)
            ->assertStatus(200);
    }

    /** @test */
    public function puede_filtrar_por_generacion()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::factory()->create(['generacion' => 2025]);
        $alumno = Alumno::factory()->create();
        $inscrito = Inscrito::factory()->create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
        ]);

        Livewire::test(Inscritos::class)
            ->set('generacionSeleccionada', 2025)
            ->call('aplicarFiltros')
            ->assertSee($alumno->nombres);
    }

    /** @test */
    public function puede_filtrar_por_grupo()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::factory()->create(['generacion' => 2025]);
        $alumno = Alumno::factory()->create();
        $inscrito = Inscrito::factory()->create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
        ]);

        Livewire::test(Inscritos::class)
            ->set('grupoSeleccionado', $grupo->id)
            ->call('aplicarFiltros')
            ->assertSee($alumno->nombres);
    }
}
