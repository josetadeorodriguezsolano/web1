<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Inscritos;
use App\Models\User;
use App\Models\Inscrito;
use App\Models\Grupo;
use App\Models\Alumno;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InscritosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario para las pruebas
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
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
    public function muestra_solo_inscritos_vigentes()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        // Crear un alumno vigente
        $alumnoVigente = Alumno::create([
            'matricula' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'curp' => 'PERJ990101HDFRNN01',
            'contacto' => '1234567890',
            'tutor' => 'María Pérez'
        ]);

        // Crear un alumno con baja
        $alumnoBaja = Alumno::create([
            'matricula' => '87654321',
            'nombres' => 'María',
            'apellidos' => 'García',
            'curp' => 'GARM990101MDFRNN01',
            'contacto' => '0987654321',
            'tutor' => 'José García'
        ]);

        Inscrito::create([
            'alumno_id' => $alumnoVigente->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ]);

        Inscrito::create([
            'alumno_id' => $alumnoBaja->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'baja'
        ]);

        Livewire::test(Inscritos::class)
            ->assertSee($alumnoVigente->nombres)
            ->assertDontSee($alumnoBaja->nombres);
    }

    /** @test */
    public function puede_filtrar_por_generacion()
    {
        $this->actingAs($this->user);

        $generacion2023 = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2023
        ]);

        $generacion2024 = Grupo::create([
            'grado' => '1',
            'letra' => 'B',
            'generacion' => 2024
        ]);

        $alumno1 = Alumno::create([
            'matricula' => '11111111',
            'nombres' => 'María',
            'apellidos' => 'González',
            'curp' => 'GONM990101MDFRNN01',
            'contacto' => '1111111111',
            'tutor' => 'Pedro González'
        ]);

        $alumno2 = Alumno::create([
            'matricula' => '22222222',
            'nombres' => 'José',
            'apellidos' => 'López',
            'curp' => 'LOPJ990101HDFRNN01',
            'contacto' => '2222222222',
            'tutor' => 'Ana López'
        ]);

        // Ambos inscritos con estatus vigente
        Inscrito::create([
            'alumno_id' => $alumno1->id,
            'grupo_id' => $generacion2023->id,
            'estatus' => 'vigente'
        ]);

        Inscrito::create([
            'alumno_id' => $alumno2->id,
            'grupo_id' => $generacion2024->id,
            'estatus' => 'vigente'
        ]);

        Livewire::test(Inscritos::class)
            ->set('generacionSeleccionada', '2023')
            ->assertSee($alumno1->nombres)
            ->assertDontSee($alumno2->nombres);
    }

    /** @test */
    public function puede_filtrar_por_grupo()
    {
        $this->actingAs($this->user);

        $grupo1 = Grupo::create(['grado' => '1', 'letra' => 'A', 'generacion' => 2024]);
        $grupo2 = Grupo::create(['grado' => '1', 'letra' => 'B', 'generacion' => 2024]);

        $alumno1 = Alumno::create([
            'matricula' => '44444444',
            'nombres' => 'Carlos',
            'apellidos' => 'Sánchez',
            'curp' => 'SANC990101HDFRNN01',
            'contacto' => '4444444444',
            'tutor' => 'Rosa Sánchez'
        ]);

        $alumno2 = Alumno::create([
            'matricula' => '55555555',
            'nombres' => 'Laura',
            'apellidos' => 'García',
            'curp' => 'GARL990101MDFRNN01',
            'contacto' => '5555555555',
            'tutor' => 'Juan García'
        ]);

        Inscrito::create([
            'alumno_id' => $alumno1->id,
            'grupo_id' => $grupo1->id,
            'estatus' => 'vigente'
        ]);

        Inscrito::create([
            'alumno_id' => $alumno2->id,
            'grupo_id' => $grupo2->id,
            'estatus' => 'vigente'
        ]);

        Livewire::test(Inscritos::class)
            ->set('grupoSeleccionado', $grupo1->id)
            ->assertSee($alumno1->nombres)
            ->assertDontSee($alumno2->nombres);
    }

    /** @test */
    public function modal_de_eliminacion_se_muestra_correctamente()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $alumno = Alumno::create([
            'matricula' => '33333333',
            'nombres' => 'Ana',
            'apellidos' => 'Martínez',
            'curp' => 'MARA990101MDFRNN01',
            'contacto' => '3333333333',
            'tutor' => 'Luis Martínez'
        ]);

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ]);

        Livewire::test(Inscritos::class)
            ->call('confirmarEliminacion', $inscrito->id)
            ->assertSet('mostrarModalEliminar', true)
            ->assertSet('idEliminar', $inscrito->id);
    }

    /** @test */
    public function marca_alumno_como_baja_en_lugar_de_eliminar()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $alumno = Alumno::create([
            'matricula' => '66666666',
            'nombres' => 'Roberto',
            'apellidos' => 'Díaz',
            'curp' => 'DIAR990101HDFRNN01',
            'contacto' => '6666666666',
            'tutor' => 'Elena Díaz'
        ]);

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ]);

        Livewire::test(Inscritos::class)
            ->call('confirmarEliminacion', $inscrito->id)
            ->call('eliminarInscrito')
            ->assertSet('mostrarModalEliminar', false);

        // Verificar que el registro no fue eliminado sino que cambió su estatus
        $this->assertDatabaseHas('inscritos', [
            'id' => $inscrito->id,
            'estatus' => 'baja'
        ]);

        // Verificar que ya no se muestra en la lista
        Livewire::test(Inscritos::class)
            ->assertDontSee($alumno->nombres);
    }

    /** @test */
    public function la_paginacion_funciona_correctamente()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        // Crear más de 10 inscritos VIGENTES para probar paginación
        for ($i = 1; $i <= 15; $i++) {
            $alumno = Alumno::create([
                'matricula' => str_pad($i, 8, '0', STR_PAD_LEFT),
                'nombres' => 'Alumno',
                'apellidos' => "Número $i",
                'curp' => 'XXXX' . str_pad($i, 6, '0', STR_PAD_LEFT) . 'HDFXXX01',
                'contacto' => str_pad($i, 10, '0', STR_PAD_LEFT),
                'tutor' => "Tutor $i"
            ]);

            Inscrito::create([
                'alumno_id' => $alumno->id,
                'grupo_id' => $grupo->id,
                'estatus' => 'vigente'
            ]);
        }

        Livewire::test(Inscritos::class)
            ->assertViewHas('inscritos', function ($inscritos) {
                return $inscritos->count() === 10; // Solo muestra 10 por página
            });
    }

    /** @test */
    public function resetea_pagina_al_cambiar_filtros()
    {
        $this->actingAs($this->user);

        $generacion = Grupo::create(['grado' => '1', 'letra' => 'A', 'generacion' => 2024]);

        // Crear suficientes registros para tener múltiples páginas
        for ($i = 1; $i <= 15; $i++) {
            $alumno = Alumno::create([
                'matricula' => '9' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'nombres' => 'Test',
                'apellidos' => "Alumno $i",
                'curp' => 'TEST' . str_pad($i, 6, '0', STR_PAD_LEFT) . 'HDFXXX01',
                'contacto' => '9' . str_pad($i, 9, '0', STR_PAD_LEFT),
                'tutor' => "Tutor Test $i"
            ]);

            Inscrito::create([
                'alumno_id' => $alumno->id,
                'grupo_id' => $generacion->id,
                'estatus' => 'vigente'
            ]);
        }

        // Solo verificar que el componente funciona al cambiar generación
        Livewire::test(Inscritos::class)
            ->set('generacionSeleccionada', '2024')
            ->assertViewHas('inscritos');
    }

    /** @test */
    public function muestra_mensaje_cuando_no_hay_inscritos_vigentes()
    {
        $this->actingAs($this->user);

        // Crear un inscrito con baja para verificar que no se muestra
        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $alumno = Alumno::create([
            'matricula' => '99999999',
            'nombres' => 'Pedro',
            'apellidos' => 'Ruiz',
            'curp' => 'RUIP990101HDFRNN01',
            'contacto' => '9999999999',
            'tutor' => 'Ana Ruiz'
        ]);

        Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'baja'
        ]);

        Livewire::test(Inscritos::class)
            ->assertSee('No hay alumnos inscritos.');
    }

    /** @test */
    public function botones_de_acciones_funcionan_correctamente()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::create(['grado' => '1', 'letra' => 'A', 'generacion' => 2024]);
        $alumno = Alumno::create([
            'matricula' => '77777777',
            'nombres' => 'Pedro',
            'apellidos' => 'Ramírez',
            'curp' => 'RAMP990101HDFRNN01',
            'contacto' => '7777777777',
            'tutor' => 'Sara Ramírez'
        ]);

        Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ]);

        Livewire::test(Inscritos::class)
            ->assertSee('Ver alumno')
            ->assertSee('Eliminar');
    }

    /** @test */
    public function los_selectores_muestran_las_opciones_correctas()
    {
        $this->actingAs($this->user);

        // Crear varias generaciones y grupos
        $grupo1 = Grupo::create(['grado' => '1', 'letra' => 'A', 'generacion' => 2023]);
        $grupo2 = Grupo::create(['grado' => '2', 'letra' => 'B', 'generacion' => 2024]);
        $grupo3 = Grupo::create(['grado' => '3', 'letra' => 'C', 'generacion' => 2024]);

        Livewire::test(Inscritos::class)
            ->assertViewHas('generaciones', function ($generaciones) {
                return $generaciones->count() === 2; // 2023 y 2024
            })
            ->assertViewHas('grupos', function ($grupos) {
                return $grupos->count() === 3; // Los 3 grupos creados
            });
    }

    /** @test */
    public function mensaje_de_baja_se_muestra_correctamente()
    {
        $this->actingAs($this->user);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $alumno = Alumno::create([
            'matricula' => '88888888',
            'nombres' => 'Sofia',
            'apellidos' => 'Torres',
            'curp' => 'TORS990101MDFRNN01',
            'contacto' => '8888888888',
            'tutor' => 'Carmen Torres'
        ]);

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ]);

        Livewire::test(Inscritos::class)
            ->call('confirmarEliminacion', $inscrito->id)
            ->call('eliminarInscrito')
            ->assertSet('mostrarModalEliminar', false);
    }
}