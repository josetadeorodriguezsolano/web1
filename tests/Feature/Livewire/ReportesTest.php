<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reportes;
use App\Livewire\ReportesGrupo;
use App\Models\Grupo;
use App\Models\Imparte;
use App\Models\Maestro;
use App\Models\Materia;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function test_Vista_pantalla_reportes(): void
    {
        $response = $this->get('/reportes'); //intenta cargar la pagina de reportes
        $response->assertStatus(200); //si devuelve un codigo 200, la carga fue exitosa
    }



    //en esta seccion empuezan los test de la clase ReportesGrupo

    public function test_Control_escolar(): void
    {
        $grupo = Grupo::where('generacion', 2024)->first();         //obtenemos grado, grupo y maestro
        $imparte = Imparte::where('grupo_id', $grupo->id)
                            ->whereNotNull('maestro_id')
                            ->first();
        $maestro = $imparte->maestro;
        $maestro = Maestro::find($maestro->id);
        $reporte = Livewire::actingAs($maestro)
            ->test(ReportesGrupo::class)
            ->set('grupo_id', $grupo->id)     //asignamos algunas propiedades para poder probar las fuunciones
            ->assertSet('grupo_id', $grupo->id)
            ->set('generacion', '2024')
            ->assertset('generacion', '2024')
            ->set('grado', $grupo->grado)
            ->assertSet('grado', $grupo->grado)
            ->set('letra', $grupo->letra)
            ->assertset('letra', $grupo->letra)
            ->set('materia_id', '1')
            ->assertSet('materia_id', '1')
            ->set('maestro_id', $maestro->id)
            ->assertset('maestro_id', $maestro->id);
        try {
            //no puedo comprobar si esto realmente puede funcionar, lo dejo para el siguiente avance
            $reporte->call('controlEscolar');
            $this->assertTrue(true); //si consigue llegar hasta aquí significa que no se lanzaron escepciones
        } catch (\Throwable $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }

    public function test_Control_escolar_grupos(): void
    {
        $grupo = Grupo::where('generacion', 2024)->first();         //obtenemos grado, grupo y maestro
        $imparte = Imparte::where('grupo_id', $grupo->id)
                            ->whereNotNull('maestro_id')
                            ->first();
        $maestro = $imparte->maestro;
        $maestro = Maestro::find($maestro->id);
        $reporte = Livewire::actingAs($maestro)
            ->test(ReportesGrupo::class)
            ->set('grupo_id', $grupo->id)     //asignamos algunas propiedades para poder probar las fuunciones
            ->assertSet('grupo_id', $grupo->id)
            ->set('generacion', '2024')
            ->assertset('generacion', '2024')
            ->set('grado', $grupo->grado)
            ->assertSet('grado', $grupo->grado)
            ->set('letra', $grupo->letra)
            ->assertset('letra', $grupo->letra)
            ->set('materia_id', '1')
            ->assertSet('materia_id', '1')
            ->set('maestro_id', $maestro->id)
            ->assertset('maestro_id', $maestro->id);
        try {
            //no puedo comprobar si esto realmente puede funcionar, lo dejo para el siguiente avance
            $reporte->call('controlEscolarGrupos');
            $this->assertTrue(true); //si consigue llegar hasta aquí significa que no se lanzaron escepciones
        } catch (Exception $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }

    public function test_control_escolar_maestros(): void
    {
        $grupo = Grupo::where('generacion', 2024)->first();         //obtenemos grado, grupo y maestro
        $imparte = Imparte::where('grupo_id', $grupo->id)
                            ->whereNotNull('maestro_id')
                            ->first();
        $maestro = $imparte->maestro;
        $maestro = Maestro::find($maestro->id);
        $reporte = Livewire::actingAs($maestro)
            ->test(ReportesGrupo::class)
            ->set('grupo_id', $grupo->id)     //asignamos algunas propiedades para poder probar las fuunciones
            ->assertSet('grupo_id', $grupo->id)
            ->set('generacion', '2024')
            ->assertset('generacion', '2024')
            ->set('grado', $grupo->grado)
            ->assertSet('grado', $grupo->grado)
            ->set('letra', $grupo->letra)
            ->assertset('letra', $grupo->letra)
            ->set('materia_id', '1')
            ->assertSet('materia_id', '1')
            ->set('maestro_id', $maestro->id)
            ->assertset('maestro_id', $maestro->id);
        try {
            //no puedo comprobar si esto realmente puede funcionar, lo dejo para el siguiente avance
            $reporte->call('controlEscolarMaestros');
            $this->assertTrue(true); //si consigue llegar hasta aquí significa que no se lanzaron escepciones
        } catch (\Throwable $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }

    public function test_vista_reporte_maestros_materia(): void
    {
        $grupo = Grupo::where('generacion', 2024)->first();         //obtenemos grado, grupo y maestro
        $imparte = Imparte::where('grupo_id', $grupo->id)
                            ->whereNotNull('maestro_id')
                            ->first();
        $maestro = $imparte->maestro;
        $maestro = Maestro::find($maestro->id);
        $reporte = Livewire::actingAs($maestro)
            ->test(ReportesGrupo::class)
            ->set('grupo_id', $grupo->id)     //asignamos algunas propiedades para poder probar las fuunciones
            ->assertSet('grupo_id', $grupo->id)
            ->set('generacion', '2024')
            ->assertset('generacion', '2024')
            ->set('grado', $grupo->grado)
            ->assertSet('grado', $grupo->grado)
            ->set('letra', $grupo->letra)
            ->assertset('letra', $grupo->letra)
            ->set('materia_id', '1')
            ->assertSet('materia_id', '1')
            ->set('maestro_id', $maestro->id)
            ->assertset('maestro_id', $maestro->id);
        try {
            //no puedo comprobar si esto realmente puede funcionar, lo dejo para el siguiente avance
            $reporte->call('reporteMaestroPorMateria');
            $this->assertTrue(true); //si consigue llegar hasta aquí significa que no se lanzaron escepciones
        } catch (\Throwable $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }

    public function test_vista_reporte_materias_sin_maestro(): void
    {
        $grupo = Grupo::where('generacion', 2024)->first();         //obtenemos grado, grupo y maestro
        $imparte = Imparte::where('grupo_id', $grupo->id)
                            ->whereNotNull('maestro_id')
                            ->first();
        echo json_encode($imparte).'/n';
        $maestro = $imparte->maestro;
        $maestro = Maestro::find($maestro->id);
        $reporte = Livewire::actingAs($maestro)
            ->test(ReportesGrupo::class)
            ->set('grupo_id', $grupo->id)     //asignamos algunas propiedades para poder probar las fuunciones
            ->assertSet('grupo_id', $grupo->id)
            ->set('generacion', '2024')
            ->assertset('generacion', '2024')
            ->set('grado', $grupo->grado)
            ->assertSet('grado', $grupo->grado)
            ->set('letra', $grupo->letra)
            ->assertset('letra', $grupo->letra)
            ->set('materia_id', '1')
            ->assertSet('materia_id', '1')
            ->set('maestro_id', $maestro->id)
            ->assertset('maestro_id', $maestro->id);
        try {
            //no puedo comprobar si esto realmente puede funcionar, lo dejo para el siguiente avance
            $reporte->call('reporteMateriasSinMaestro');
            $this->assertTrue(true); //si consigue llegar hasta aquí significa que no se lanzaron escepciones
        } catch (\Throwable $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }

    public function test_vista_reporte_grupos_sin_maestro(): void
    {
                //obtenemos grado, grupo y maestro
        $imparte = Imparte::whereNull('materia_id')
                            ->first();
        echo json_encode($imparte).'/n';
        if ($imparte == null) {
            $this->fail('no se encontró un grupo sin maestro');
        }
        $grupo = Grupo::where('id', $imparte->grupo_id)->first();
        echo json_encode($grupo).'/n';
        $maestro = Maestro::first();
        $reporte = Livewire::actingAs($maestro)
            ->test(ReportesGrupo::class);
        try {
            //no puedo comprobar si esto realmente puede funcionar, lo dejo para el siguiente avance
            $reporte->call('reporteGruposSinMaestro');
            $this->assertTrue(true); //si consigue llegar hasta aquí significa que no se lanzaron escepciones
        } catch (\Throwable $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }
}
