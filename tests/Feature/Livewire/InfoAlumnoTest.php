<?php

namespace Tests\Feature\Livewire;

use App\Livewire\InfoAlumno;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Grupo;
use App\Models\Inscrito;
use App\Models\Materia;
use App\Models\Imparte;
use App\Models\Maestro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InfoAlumnoTest extends TestCase
{
    use RefreshDatabase;

    protected $alumno;
    protected $grupo;
    protected $materia;
    protected $inscripcion;
    protected $maestro;

    public function setUp(): void
    {
        parent::setUp();
        $this->crearDatosDePrueba();
    }

    private function crearDatosDePrueba()
    {
        // Crear grupo
        $this->grupo = Grupo::factory()->create([
            'grado' => '2',
            'letra' => 'A',
            'generacion' => 2023
        ]);

        // Crear alumno con datos válidos
        $this->alumno = Alumno::factory()->create([
            'nombres' => 'Juan Carlos',
            'apellidos' => 'García Pérez',
            'matricula' => 'A12345678',
            'curp' => 'GAPE050823HDFRCR01',
            'contacto' => '6121234567',
            'tutor' => 'María López Gómez',
            'estatus' => 'vigente'
        ]);

        // Crear inscripción
        $this->inscripcion = Inscrito::create([
            'alumno_id' => $this->alumno->id,
            'grupo_id' => $this->grupo->id,
            'estatus' => 'vigente'
        ]);

        // Crear maestro
        $this->maestro = Maestro::factory()->create();

        // Crear materia
        $this->materia = Materia::factory()->create([
            'nombre' => 'Matemáticas',
            'clave' => 'MAT101',
            'creditos' => 5,
            'grado' => '2'
        ]);

        // Crear relación Imparte
        Imparte::create([
            'materia_id' => $this->materia->id,
            'maestro_id' => $this->maestro->id,
            'grupo_id' => $this->grupo->id,
        ]);

        // Crear calificaciones
        Calificacion::create([
            'alumno_id' => $this->alumno->id,
            'materia_id' => $this->materia->id,
            'unidad' => 1,
            'calificacion' => 8.5
        ]);

        Calificacion::create([
            'alumno_id' => $this->alumno->id,
            'materia_id' => $this->materia->id,
            'unidad' => 2,
            'calificacion' => 9.0
        ]);
    }

    /** @test */
    public function test_componente_puede_renderizarse()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->assertStatus(200);
    }

    /** @test */
    public function test_carga_correctamente_datos_del_alumno()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->assertSet('alumno.nombres', 'Juan Carlos')
            ->assertSet('alumno.apellidos', 'García Pérez')
            ->assertSet('alumno.matricula', 'A12345678')
            ->assertSet('alumno.curp', 'GAPE050823HDFRCR01')
            ->assertSet('alumno.contacto', '6121234567')
            ->assertSet('alumno.tutor', 'María López Gómez')
            ->assertSet('alumno.estatus', 'vigente');
    }

    /** @test */
    public function test_carga_grupo_del_alumno()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->assertSet('grupo.id', $this->grupo->id)
            ->assertSet('grupo.grado', '2')
            ->assertSet('grupo.letra', 'A')
            ->assertSet('grupo.generacion', 2023);
    }

    /** @test */
    public function test_inicializa_datos_del_formulario()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->assertSet('alumnoData.nombres', 'Juan Carlos')
            ->assertSet('alumnoData.apellidos', 'García Pérez')
            ->assertSet('alumnoData.matricula', 'A12345678')
            ->assertSet('alumnoData.curp', 'GAPE050823HDFRCR01')
            ->assertSet('alumnoData.contacto', '6121234567')
            ->assertSet('alumnoData.tutor', 'María López Gómez')
            ->assertSet('alumnoData.estatus', 'vigente');
    }

    /** @test */
    public function test_activa_modo_edicion()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->assertSet('editando', false)
            ->call('activarEdicion')
            ->assertSet('editando', true);
    }

    /** @test */
    public function test_cancela_modo_edicion()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.nombres', 'Nombre Modificado')
            ->call('cancelarEdicion')
            ->assertSet('editando', false)
            ->assertSet('alumnoData.nombres', 'Juan Carlos');
    }

    /** @test */
    public function test_modal_de_eliminacion()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->assertSet('mostrarModalEliminar', false)
            ->call('confirmarEliminacion')
            ->assertSet('mostrarModalEliminar', true)
            ->call('cancelarEliminacion')
            ->assertSet('mostrarModalEliminar', false);
    }

    // ========== PRUEBAS DE VALIDACIÓN ==========

    /** @test */
    public function test_valida_matricula_requerida()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.matricula', '')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.matricula' => 'required']);
    }

    /** @test */
    public function test_valida_matricula_minima()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.matricula', 'A123')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.matricula' => 'min']);
    }

    /** @test */
    public function test_valida_matricula_maxima()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.matricula', 'A12345678901')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.matricula' => 'max']);
    }

    /** @test */
    public function test_valida_matricula_formato()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.matricula', 'a12345@#$')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.matricula' => 'regex']);
    }

    /** @test */
    public function test_valida_nombres_requeridos()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.nombres', '')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.nombres' => 'required']);
    }

    /** @test */
    public function test_valida_nombres_formato()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.nombres', 'Juan123@#$')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.nombres' => 'regex']);
    }

    /** @test */
    public function test_valida_apellidos_requeridos()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.apellidos', '')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.apellidos' => 'required']);
    }

    /** @test */
    public function test_valida_apellidos_formato()
    {
        // Necesitamos un valor que realmente falle la validación ANTES del formateo
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.apellidos', 'García@#$%') // Caracteres especiales que no se limpian
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.apellidos' => 'regex']);
    }

    /** @test */
    public function test_valida_curp_requerido()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.curp', '')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.curp' => 'required']);
    }

    /** @test */
    public function test_valida_curp_longitud()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.curp', 'GAPE050823')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.curp' => 'size']);
    }

    /** @test */
    public function test_valida_curp_formato()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.curp', 'gape@#$823hdfrcr01') // Caracteres especiales
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.curp' => 'regex']);
    }

    /** @test */
    public function test_valida_contacto_requerido()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.contacto', '')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.contacto' => 'required']);
    }

    /** @test */
    public function test_valida_contacto_longitud()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.contacto', '612123')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.contacto' => 'min']);
    }

    /** @test */
    public function test_valida_contacto_formato()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.contacto', '612') // Muy corto, solo 3 dígitos
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.contacto' => 'min']);
    }

    /** @test */
    public function test_valida_tutor_requerido()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.tutor', '')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.tutor' => 'required']);
    }

    /** @test */
    public function test_valida_tutor_formato()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.tutor', 'María@#$López') // Caracteres especiales
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.tutor' => 'regex']);
    }

    /** @test */
    public function test_valida_estatus_requerido()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.estatus', '')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.estatus' => 'required']);
    }

    /** @test */
    public function test_valida_estatus_valor_valido()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.estatus', 'invalido')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.estatus' => 'in']);
    }

    // ========== PRUEBAS DE UNICIDAD ==========

    /** @test */
    public function test_no_permite_matricula_duplicada()
    {
        Alumno::factory()->create(['matricula' => 'B87654321']);

        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.matricula', 'B87654321')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.matricula' => 'unique']);
    }

    /** @test */
    public function test_permite_conservar_misma_matricula()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.matricula', 'A12345678')
            ->call('guardarCambios')
            ->assertHasNoErrors(['alumnoData.matricula']);
    }

    /** @test */
    public function test_no_permite_curp_duplicado()
    {
        Alumno::factory()->create(['curp' => 'MALP050823HDFRCR01']);

        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.curp', 'MALP050823HDFRCR01')
            ->call('guardarCambios')
            ->assertHasErrors(['alumnoData.curp' => 'unique']);
    }

    /** @test */
    public function test_permite_conservar_mismo_curp()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.curp', 'GAPE050823HDFRCR01')
            ->call('guardarCambios')
            ->assertHasNoErrors(['alumnoData.curp']);
    }

    // ========== TESTS DE FORMATEO CORREGIDOS ==========

    /** @test */
    public function test_formatea_matricula_a_mayusculas()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion');

        // Simular la actualización del campo
        $component->set('alumnoData.matricula', 'a98765432');

        // Verificar que se formateó
        $component->assertSet('alumnoData.matricula', 'A98765432');
    }

    /** @test */
    public function test_formatea_curp_a_mayusculas()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion');

        $component->set('alumnoData.curp', 'malp050823hdfrcr01');
        $component->assertSet('alumnoData.curp', 'MALP050823HDFRCR01');
    }

    /** @test */
    public function test_elimina_numeros_de_nombres()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion');

        $component->set('alumnoData.nombres', 'Pedro123Antonio');
        $component->assertSet('alumnoData.nombres', 'PedroAntonio');
    }

    /** @test */
    public function test_elimina_numeros_de_apellidos()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion');

        $component->set('alumnoData.apellidos', 'García456Pérez');
        $component->assertSet('alumnoData.apellidos', 'GarcíaPérez');
    }

    /** @test */
    public function test_elimina_numeros_de_tutor()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion');

        $component->set('alumnoData.tutor', 'María789López');
        $component->assertSet('alumnoData.tutor', 'MaríaLópez');
    }

    /** @test */
    public function test_elimina_letras_de_contacto()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion');

        $component->set('alumnoData.contacto', '612abc1234');
        $component->assertSet('alumnoData.contacto', '6121234');
    }

    /** @test */
    public function test_limita_contacto_a_10_digitos()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion');

        $component->set('alumnoData.contacto', '612123456789012');
        $component->assertSet('alumnoData.contacto', '6121234567');
    }

    // ========== PRUEBAS DE GUARDADO ==========

    /** @test */
    public function test_guarda_cambios_correctamente()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.nombres', 'Pedro Antonio')
            ->set('alumnoData.apellidos', 'Martínez López')
            ->set('alumnoData.matricula', 'A98765432')
            ->set('alumnoData.curp', 'MALP050823HDFRCR01')
            ->set('alumnoData.contacto', '6129876543')
            ->set('alumnoData.tutor', 'Carmen Martínez')
            ->set('alumnoData.estatus', 'vigente')
            ->call('guardarCambios')
            ->assertSet('editando', false)
            ->assertSet('mensaje', 'Alumno actualizado correctamente.')
            ->assertSet('tipoMensaje', 'success');

        $this->assertDatabaseHas('alumnos', [
            'id' => $this->alumno->id,
            'nombres' => 'Pedro Antonio',
            'apellidos' => 'Martínez López',
            'matricula' => 'A98765432',
            'curp' => 'MALP050823HDFRCR01',
            'contacto' => '6129876543',
            'tutor' => 'Carmen Martínez'
        ]);
    }

    /** @test */
    public function test_formatea_datos_al_guardar()
    {
        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('activarEdicion')
            ->set('alumnoData.nombres', '  pedro antonio  ')
            ->set('alumnoData.apellidos', '  MARTÍNEZ LÓPEZ  ')
            ->set('alumnoData.matricula', $this->alumno->matricula) // Mantener la misma matrícula
            ->set('alumnoData.curp', $this->alumno->curp) // Mantener el mismo CURP
            ->set('alumnoData.contacto', '  6129876543  ')
            ->set('alumnoData.tutor', '  CARMEN MARTÍNEZ  ')
            ->set('alumnoData.estatus', 'vigente')
            ->call('guardarCambios');

        // Verificar que guardó correctamente
        $component->assertSet('mensaje', 'Alumno actualizado correctamente.');
        $component->assertSet('tipoMensaje', 'success');
        $component->assertSet('editando', false);

        // Verificar formateo correcto en la BD
        $this->assertDatabaseHas('alumnos', [
            'id' => $this->alumno->id,
            'nombres' => 'Pedro Antonio',
            'apellidos' => 'Martínez López',
            'contacto' => '6129876543',
            'tutor' => 'Carmen Martínez'
        ]);
    }

    /** @test */
    public function test_da_de_baja_alumno()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->call('confirmarEliminacion')
            ->call('eliminarAlumno')
            ->assertSet('mostrarModalEliminar', false)
            ->assertSet('mensaje', 'Alumno dado de baja correctamente.')
            ->assertSet('tipoMensaje', 'success');

        $this->assertDatabaseHas('alumnos', [
            'id' => $this->alumno->id,
            'estatus' => 'baja'
        ]);

        $this->assertDatabaseHas('inscritos', [
            'alumno_id' => $this->alumno->id,
            'estatus' => 'baja'
        ]);
    }

    /** @test */
    public function test_carga_calificaciones_del_alumno()
    {
        Livewire::test(InfoAlumno::class, ['alumnoId' => $this->alumno->id])
            ->assertCount('calificaciones', 1)
            ->assertSet('calificaciones.0.nombre', 'Matemáticas')
            ->assertSet('calificaciones.0.unidades.1', 8.5)
            ->assertSet('calificaciones.0.unidades.2', 9.0)
            ->assertSet('calificaciones.0.promedio', 8.8);
    }

    /** @test */
    public function test_maneja_alumno_sin_inscripcion()
    {
        $alumnoSinInscripcion = Alumno::factory()->create([
            'nombres' => 'Sin Inscripción',
            'matricula' => 'SININS123'
        ]);

        $component = Livewire::test(InfoAlumno::class, ['alumnoId' => $alumnoSinInscripcion->id])
            ->assertStatus(200)
            ->assertSet('alumno.nombres', 'Sin Inscripción');

        // Verificar que grupo es null
        $this->assertNull($component->get('grupo'));

        // Verificar que no hay calificaciones
        $component->assertCount('calificaciones', 0);
    }
}
