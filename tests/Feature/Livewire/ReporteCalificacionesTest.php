<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ReporteCalificaciones;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Maestro;
use App\Models\Materia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class ReporteCalificacionesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $maestro;
    protected $alumno;
    protected $materia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->maestro = Maestro::factory()->create();
        $this->alumno = Alumno::factory()->create(['matricula' => 'TEST123', 'nombres' => 'Juan Carlos']);
        $this->materia = Materia::factory()->create(['nombre' => 'Matemáticas']);

        Auth::login($this->maestro);
    }

    /** @test */
    public function el_componente_se_renderiza_correctamente()
    {
        Livewire::test(ReporteCalificaciones::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.reporte-calificaciones');
    }

    /** @test */
    public function carga_materias_disponibles_al_montar()
    {
        Materia::factory()->count(3)->create();

        $component = Livewire::test(ReporteCalificaciones::class);

        $this->assertGreaterThan(0, count($component->get('materiasDisponibles')));
    }

    /** @test */
    public function establece_fechas_por_defecto_correctamente()
    {
        $component = Livewire::test(ReporteCalificaciones::class);

        $this->assertEquals(now()->format('Y-m-d'), $component->get('fechaFin'));
        $this->assertEquals(now()->subMonth()->format('Y-m-d'), $component->get('fechaInicio'));
    }

    /** @test */
    public function busca_alumnos_por_matricula_correctamente()
    {
        Alumno::factory()->create(['matricula' => 'TEST456']);
        Alumno::factory()->create(['matricula' => 'OTRO789']);

        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('matriculaBusqueda', 'TEST')
            ->call('buscarAlumnosPorMatricula');

        $sugerencias = $component->get('sugerenciasAlumnos');

        $this->assertGreaterThan(0, count($sugerencias));
        $this->assertTrue($component->get('mostrarSugerencias'));
    }

    /** @test */
    public function busca_alumnos_por_nombre_correctamente()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('matriculaBusqueda', 'Juan')
            ->call('buscarAlumnosPorMatricula');

        $sugerencias = $component->get('sugerenciasAlumnos');

        $this->assertGreaterThan(0, count($sugerencias));
    }

    /** @test */
    public function selecciona_alumno_correctamente()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->call('seleccionarAlumno', 'TEST123', 'Juan Carlos Pérez García');

        $this->assertEquals('TEST123 - Juan Carlos Pérez García', $component->get('matriculaBusqueda'));
        $this->assertFalse($component->get('mostrarSugerencias'));
    }

    /** @test */
    public function limpia_busqueda_correctamente()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('matriculaBusqueda', 'TEST123')
            ->set('mostrarSugerencias', true)
            ->call('limpiarBusqueda');

        $this->assertEquals('', $component->get('matriculaBusqueda'));
        $this->assertFalse($component->get('mostrarSugerencias'));
        $this->assertEquals([], $component->get('sugerenciasAlumnos'));
    }

    /** @test */
    public function aplica_filtros_correctamente()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('materiaFiltro', $this->materia->id)
            ->set('unidadFiltro', '1')
            ->call('aplicarFiltros');

        $component->assertHasNoErrors();
    }

    /** @test */
    public function limpia_filtros_correctamente()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('materiaFiltro', $this->materia->id)
            ->set('unidadFiltro', '2')
            ->set('matriculaBusqueda', 'TEST123')
            ->call('limpiarFiltros');

        $this->assertEquals('', $component->get('materiaFiltro'));
        $this->assertEquals('', $component->get('unidadFiltro'));
        $this->assertEquals('', $component->get('matriculaBusqueda'));
    }

    /** @test */
    public function carga_historial_de_calificaciones_sin_auditorias()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->call('cargarHistorialCalificaciones');

        $component->assertHasNoErrors();
    }

    /** @test */
    public function carga_historial_con_auditorias_existentes()
    {
        $calificacion = Calificacion::factory()->create([
            'alumno_id' => $this->alumno->id,
            'materia_id' => $this->materia->id,
            'unidad' => 1,
            'calificacion' => 8.5
        ]);

        $calificacion->update(['calificacion' => 9.0]);

        $component = Livewire::test(ReporteCalificaciones::class)
            ->call('cargarHistorialCalificaciones');

        $component->assertHasNoErrors();
    }

    /** @test */
    public function filtra_por_materia_correctamente()
    {
        Livewire::test(ReporteCalificaciones::class)
            ->set('materiaFiltro', $this->materia->id)
            ->call('cargarHistorialCalificaciones')
            ->assertHasNoErrors();
    }

    /** @test */
    public function filtra_por_unidad_correctamente()
    {
        Livewire::test(ReporteCalificaciones::class)
            ->set('unidadFiltro', '1')
            ->call('cargarHistorialCalificaciones')
            ->assertHasNoErrors();
    }

    /** @test */
    public function filtra_por_fechas_correctamente()
    {
        $fechaInicio = now()->subDays(7)->format('Y-m-d');
        $fechaFin = now()->format('Y-m-d');

        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('fechaInicio', $fechaInicio)
            ->set('fechaFin', $fechaFin)
            ->call('cargarHistorialCalificaciones');

        $component->assertHasNoErrors();
        $this->assertEquals($fechaInicio, $component->get('fechaInicio'));
        $this->assertEquals($fechaFin, $component->get('fechaFin'));
    }

    /** @test */
    public function actualiza_matricula_busqueda_con_live_wire()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('matriculaBusqueda', 'T');

        $this->assertEquals('T', $component->get('matriculaBusqueda'));
        $this->assertFalse($component->get('mostrarSugerencias'));
    }

    /** @test */
    public function muestra_sugerencias_con_mas_de_dos_caracteres()
    {
        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('matriculaBusqueda', 'TES');

        $sugerencias = $component->get('sugerenciasAlumnos');
        $this->assertIsArray($sugerencias);
    }

    /** @test */
    public function puede_manejar_busqueda_con_caracteres_especiales()
    {
        Alumno::factory()->create([
            'matricula' => 'SP123',
            'nombres' => 'José María',
            'apellidos' => 'Fernández-López'
        ]);

        $component = Livewire::test(ReporteCalificaciones::class)
            ->set('matriculaBusqueda', 'José')
            ->call('buscarAlumnosPorMatricula');

        $sugerencias = $component->get('sugerenciasAlumnos');
        $this->assertIsArray($sugerencias);
    }
    /** @test */
    public function valida_que_las_propiedades_son_del_tipo_correcto()
    {
        $component = Livewire::test(ReporteCalificaciones::class);

        $this->assertIsString($component->get('materiaFiltro'));
        $this->assertIsString($component->get('unidadFiltro'));
        $this->assertIsString($component->get('matriculaBusqueda'));
        $this->assertIsArray($component->get('sugerenciasAlumnos'));
        $this->assertIsBool($component->get('mostrarSugerencias'));
        $this->assertIsBool($component->get('cargando'));
    }

    /** @test */
    public function el_componente_implementa_paginacion()
    {
        $component = Livewire::test(ReporteCalificaciones::class);

        $this->assertTrue(method_exists($component->instance(), 'resetPage'));
        $this->assertTrue(method_exists($component->instance(), 'getPage'));
    }

    /** @test */
    public function render_devuelve_vista_correcta()
    {
        $component = Livewire::test(ReporteCalificaciones::class);

        $component->assertViewIs('livewire.reporte-calificaciones');
        $component->assertViewHas('historialesCalificaciones');
    }

    protected function tearDown(): void
    {
        Auth::logout();
        parent::tearDown();
    }
}
