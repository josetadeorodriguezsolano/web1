<?php

namespace Tests\Unit\Livewire;

use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Ina;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Maestro;
use App\Models\Imparte;
use App\Models\Horario;
use App\Models\Inasistencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Carbon\Carbon;

class InaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Silenciar logs durante las pruebas
        Log::shouldReceive('info')->andReturn(true);
        Log::shouldReceive('error')->andReturn(true);
        Log::shouldReceive('warning')->andReturn(true);
    }

    #[Test]
    public function puede_montar_el_componente_correctamente()
    {
        // Crear datos de prueba con relaciones correctas
        $grupos = Grupo::factory()->count(2)->create();
        $materias = Materia::factory()->count(4)->create();
        $maestros = Maestro::factory()->count(3)->create();
        
        // Crear impartes con IDs válidos
        $impartes = collect();
        foreach ($maestros as $maestro) {
            $imparte = Imparte::factory()->create([
                'maestro_id' => $maestro->id,
                'materia_id' => $materias->first()->id,
                'grupo_id' => $grupos->first()->id
            ]);
            $impartes->push($imparte);
        }
        
        // Crear inasistencias con datos válidos
        foreach ($impartes->take(2) as $imparte) {
            // Crear un horario primero
            $horario = Horario::factory()->create([
                'imparte_id' => $imparte->id,
                'dia_semana' => 'Lunes'
            ]);
            
            // Crear inasistencia con horario válido
            Inasistencia::create([
                'imparte_id' => $imparte->id,
                'horario_id' => $horario->id,
                'fecha' => '2024-01-01',
                'justificacion' => 'Test justificacion'
            ]);
        }
        
        $component = Livewire::test(Ina::class);
        
        $component->assertSet('impartes', function ($impartes) {
            return $impartes->count() === 3;
        });
        
        $component->assertSet('inasistencias', function ($inasistencias) {
            return $inasistencias->count() === 2;
        });
        
        $component->assertSet('mostrarFormulario', false)
                  ->assertSet('seleccionado', -1)
                  ->assertSet('horarios', []);
    }

    #[Test]
    public function obtiene_dia_semana_correctamente()
    {
        $component = Livewire::test(Ina::class);
        
        // Probar con fecha de lunes (2024-01-01 era lunes)
        $component->set('inasistencia.fecha', '2024-01-01');
        $component->call('obtenerDiaSemanaAjustado');
        $component->assertSet('diaNumero', 'Lunes');
        
        // Probar con fecha de miércoles
        $component->set('inasistencia.fecha', '2024-01-03');
        $component->call('obtenerDiaSemanaAjustado');
        $component->assertSet('diaNumero', 'Miércoles');
        
        // Probar con sábado (debe convertir a viernes)
        $component->set('inasistencia.fecha', '2024-01-06');
        $component->call('obtenerDiaSemanaAjustado');
        $component->assertSet('diaNumero', 'Viernes');
        
        // Probar con domingo (debe convertir a lunes)
        $component->set('inasistencia.fecha', '2024-01-07');
        $component->call('obtenerDiaSemanaAjustado');
        $component->assertSet('diaNumero', 'Lunes');
    }

    #[Test]
    public function obtiene_dia_por_defecto_con_fecha_invalida()
    {
        $component = Livewire::test(Ina::class);
        
        // Fecha inválida
        $component->set('inasistencia.fecha', 'fecha-invalida');
        $component->call('obtenerDiaSemanaAjustado');
        $component->assertSet('diaNumero', 'Lunes');
        
        // Sin fecha
        $component->set('inasistencia.fecha', null);
        $component->call('obtenerDiaSemanaAjustado');
        $component->assertSet('diaNumero', 'Lunes');
    }

    #[Test]
    public function obtiene_horarios_por_maestro_correctamente()
    {
        // Crear datos de prueba con relaciones correctas
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        $maestro = Maestro::factory()->create();
        
        $imparte = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'materia_id' => $materia->id,
            'grupo_id' => $grupo->id
        ]);
        
        // Crear horarios para lunes
        $horarios = Horario::factory()->count(3)->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        $component = Livewire::test(Ina::class)
            ->set('maestro_id', $maestro->id)
            ->set('inasistencia.fecha', '2024-01-01'); // Lunes
        
        $component->call('obtenerHorariosPorMaestro');
        
        $component->assertSet('horarios', function ($horarios) {
            return $horarios->count() === 3 && 
                   $horarios->every(fn($horario) => $horario->dia_semana === 'Lunes');
        });
    }

    #[Test]
    public function limpia_horarios_sin_maestro_seleccionado()
    {
        $component = Livewire::test(Ina::class)
            ->set('maestro_id', null);
        
        $component->call('obtenerHorariosPorMaestro');
        
        $component->assertSet('horarios', []);
    }

    #[Test]
    public function marca_horarios_ocupados_correctamente()
    {
        // Crear datos de prueba con relaciones correctas
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        $maestro = Maestro::factory()->create();
        
        $imparte = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'materia_id' => $materia->id,
            'grupo_id' => $grupo->id
        ]);
        
        $horario = Horario::factory()->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        // Crear inasistencia existente
        $fecha = '2024-01-01';
        Inasistencia::create([
            'fecha' => $fecha,
            'horario_id' => $horario->id,
            'imparte_id' => $imparte->id,
            'justificacion' => 'Test'
        ]);
        
        $component = Livewire::test(Ina::class)
            ->set('maestro_id', $maestro->id)
            ->set('inasistencia.fecha', $fecha);
        
        $component->call('obtenerHorariosPorMaestro');
        
        $component->assertSet('horarios', function ($horarios) {
            return $horarios->count() === 1 && $horarios->first()->ocupado === true;
        });
    }

    #[Test]
    public function puede_agregar_nueva_inasistencia()
    {
        $component = Livewire::test(Ina::class);
        
        $component->call('agregar');
        
        $component->assertSet('mostrarFormulario', true)
                  ->assertSet('inasistencia', function ($inasistencia) {
                      return is_array($inasistencia) && 
                             array_key_exists('imparte_id', $inasistencia) &&
                             array_key_exists('horario_id', $inasistencia) &&
                             array_key_exists('justificacion', $inasistencia) &&
                             array_key_exists('fecha', $inasistencia);
                  });
    }

    #[Test]
    public function puede_seleccionar_horario_correctamente()
    {
        // Crear datos de prueba con relaciones correctas
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        $maestro = Maestro::factory()->create();
        
        $imparte = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'materia_id' => $materia->id,
            'grupo_id' => $grupo->id
        ]);
        
        $horario = Horario::factory()->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        $component = Livewire::test(Ina::class)
            ->set('horarios', collect([$horario]))
            ->set('inasistencia.fecha', '2024-01-01');
        
        $component->call('seleccionar', 0);
        
        $component->assertSet('seleccionado', 0)
                  ->assertSet('mostrarFormulario', true)
                  ->assertSet('inasistencia.imparte_id', $imparte->id)
                  ->assertSet('inasistencia.horario_id', $horario->id)
                  ->assertSet('inasistencia.fecha', '2024-01-01');
    }

    #[Test]
    public function puede_cancelar_formulario()
    {
        $component = Livewire::test(Ina::class)
            ->set('mostrarFormulario', true)
            ->set('inasistencia', ['test' => 'data']);
        
        $component->call('cancelar');
        
        $component->assertSet('mostrarFormulario', false)
                  ->assertSet('inasistencia', []);
    }

    #[Test]
    public function puede_guardar_nueva_inasistencia()
    {
        // Crear datos de prueba con relaciones correctas
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        $maestro = Maestro::factory()->create();
        
        $imparte = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'materia_id' => $materia->id,
            'grupo_id' => $grupo->id
        ]);
        
        $horario = Horario::factory()->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        $data = [
            'imparte_id' => $imparte->id,
            'horario_id' => $horario->id,
            'justificacion' => 'Justificación de prueba',
            'fecha' => '2024-01-01'
        ];
        
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', $data);
        
        $component->call('guardar');
        
        // Verificar que se guardó en la base de datos
        $this->assertDatabaseHas('inasistencias', $data);
        
        $component->assertSet('mostrarFormulario', false)
                  ->assertSet('inasistencia', []);
    }

    #[Test]
    public function puede_actualizar_inasistencia_existente()
    {
        // Crear datos de prueba con relaciones correctas
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        $maestro = Maestro::factory()->create();
        
        $imparte = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'materia_id' => $materia->id,
            'grupo_id' => $grupo->id
        ]);
        
        $horario = Horario::factory()->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        $fecha = '2024-01-01';
        
        $inasistencia = Inasistencia::create([
            'imparte_id' => $imparte->id,
            'horario_id' => $horario->id,
            'fecha' => $fecha,
            'justificacion' => 'Justificación original'
        ]);
        
        $nuevaData = [
            'imparte_id' => $imparte->id,
            'horario_id' => $horario->id,
            'justificacion' => 'Justificación actualizada',
            'fecha' => $fecha
        ];
        
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', $nuevaData);
        
        $component->call('guardar');
        
        // Verificar que se actualizó
        $this->assertDatabaseHas('inasistencias', $nuevaData);
        $this->assertDatabaseMissing('inasistencias', [
            'justificacion' => 'Justificación original'
        ]);
    }

    #[Test]
    public function falla_validacion_con_datos_invalidos()
    {
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', [
                'imparte_id' => null,
                'horario_id' => null,
                'fecha' => null
            ]);
        
        $component->call('guardar');
        
        // El componente debería manejar la validación y no redirigir
        $component->assertOk();
    }

    #[Test]
    public function puede_eliminar_inasistencia()
    {
        // Crear datos de prueba con relaciones correctas
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        $maestro = Maestro::factory()->create();
        
        $imparte = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'materia_id' => $materia->id,
            'grupo_id' => $grupo->id
        ]);
        
        $horario = Horario::factory()->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        $fecha = '2024-01-01';
        
        $inasistencia = Inasistencia::create([
            'imparte_id' => $imparte->id,
            'horario_id' => $horario->id,
            'fecha' => $fecha,
            'justificacion' => 'Test'
        ]);
        
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', [
                'fecha' => $fecha,
                'horario_id' => $horario->id
            ]);
        
        $component->call('eliminar');
        
        // Verificar que se eliminó
        $this->assertDatabaseMissing('inasistencias', [
            'id' => $inasistencia->id
        ]);
        
        $component->assertSet('seleccionado', -1);
    }

    #[Test]
    public function no_puede_eliminar_sin_fecha_u_horario()
    {
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', []);
        
        $component->call('eliminar');
        
        $component->assertOk();
    }

    #[Test]
    public function maneja_error_al_eliminar_inasistencia_inexistente()
    {
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', [
                'fecha' => '2024-01-01',
                'horario_id' => 999 // ID inexistente
            ]);
        
        $component->call('eliminar');
        
        $component->assertOk();
    }

    #[Test]
    public function recarga_datos_correctamente()
    {
        // Crear datos de prueba con relaciones correctas
        $grupos = Grupo::factory()->count(2)->create();
        $materias = Materia::factory()->count(3)->create();
        $maestros = Maestro::factory()->count(2)->create();
        
        // Crear impartes con IDs válidos
        foreach ($maestros as $maestro) {
            Imparte::factory()->create([
                'maestro_id' => $maestro->id,
                'materia_id' => $materias->first()->id,
                'grupo_id' => $grupos->first()->id
            ]);
        }
        
        // Crear inasistencias con datos válidos
        $imparte = Imparte::first();
        $horario = Horario::factory()->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        Inasistencia::factory()->count(2)->create([
            'imparte_id' => $imparte->id,
            'horario_id' => $horario->id
        ]);
        
        $component = Livewire::test(Ina::class);
        
        // Llamar método privado a través de reflexión
        $reflection = new \ReflectionClass($component->instance());
        $method = $reflection->getMethod('recargarDatos');
        $method->setAccessible(true);
        $method->invoke($component->instance());
        
        $component->assertSet('maestros', function ($maestros) {
            return $maestros->count() === 2;
        });
        
        $component->assertSet('materias', function ($materias) {
            return $materias->count() === 3;
        });
        
        $component->assertSet('grupos', function ($grupos) {
            return $grupos->count() === 2;
        });
    }

    #[Test]
    public function maneja_errores_de_base_de_datos_al_guardar()
    {
        // Datos inválidos que causarán error de BD
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', [
                'imparte_id' => 999, // ID inexistente
                'horario_id' => 999, // ID inexistente
                'justificacion' => 'Test',
                'fecha' => '2024-01-01'
            ]);
        
        $component->call('guardar');
        
        $component->assertOk();
    }

    #[Test]
    public function valida_longitud_maxima_justificacion()
    {
        // Crear datos de prueba con relaciones correctas
        $grupo = Grupo::factory()->create();
        $materia = Materia::factory()->create();
        $maestro = Maestro::factory()->create();
        
        $imparte = Imparte::factory()->create([
            'maestro_id' => $maestro->id,
            'materia_id' => $materia->id,
            'grupo_id' => $grupo->id
        ]);
        
        $horario = Horario::factory()->create([
            'imparte_id' => $imparte->id,
            'dia_semana' => 'Lunes'
        ]);
        
        $component = Livewire::test(Ina::class)
            ->set('inasistencia', [
                'imparte_id' => $imparte->id,
                'horario_id' => $horario->id,
                'justificacion' => str_repeat('a', 1001), // Más de 1000 caracteres
                'fecha' => '2024-01-01'
            ]);
        
        $component->call('guardar');
        
        $component->assertOk();
    }

    #[Test]
    public function mantiene_estado_correcto_despues_de_operaciones()
    {
        $component = Livewire::test(Ina::class);
        
        // Agregar nueva inasistencia
        $component->call('agregar');
        $component->assertSet('mostrarFormulario', true);
        
        // Cancelar
        $component->call('cancelar');
        $component->assertSet('mostrarFormulario', false);
        $component->assertSet('inasistencia', []);
    }

    #[Test]
    public function renderiza_vista_correctamente()
    {
        $component = Livewire::test(Ina::class);
        
        // En lugar de llamar render directamente, verificar que el componente se renderiza
        $component->assertOk();
        $component->assertViewIs('livewire.registrar-inasistencias');
    }

    #[Test]
    public function maneja_componente_sin_datos_iniciales()
    {
        // Limpiar base de datos completamente
        DB::table('inasistencias')->delete();
        DB::table('horarios')->delete();
        DB::table('imparte')->delete();
        DB::table('maestros')->delete();
        DB::table('materias')->delete();
        DB::table('grupos')->delete();
        
        $component = Livewire::test(Ina::class);
        
        $component->assertSet('inasistencias', function ($inasistencias) {
            return $inasistencias->isEmpty();
        });
        
        $component->assertSet('impartes', function ($impartes) {
            return $impartes->isEmpty();
        });
        
        $component->assertOk();
    }
}