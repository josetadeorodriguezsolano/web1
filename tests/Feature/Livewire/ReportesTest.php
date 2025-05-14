<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reportes;
use App\Livewire\ReportesGrupo;
use App\Models\Grupo;
use App\Models\Imparte;
use App\Models\Maestro;
use App\Models\Materia;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function test_Vista_Principal(): void
    {
        $response = $this->get('/reportes'); //intenta cargar la pagina de reportes
        $response->assertStatus(200); //si devuelve un codigo 200, la carga fue exitosa
    }

    public function test_Funciones_Reportes(): void
    {
        try {
            //esta es una solucion temporal, realmente no he podido ver si funciona, pero sigo buscando
            $grupo = Grupo::where('generacion', 2024)->first();
            $imparte = Imparte::where('grupo_id', $grupo->id)->first();
            $maestro = $imparte->maestro;
            $maestro = Maestro::find($maestro->id);

            $reporte = Livewire::actingAs($maestro)
                ->test(Reportes::class)
                ->set('anio', '2024')           //hacemos un par de asignaciones para poder realizar las consultas
                ->assertSet('anio', '2024')
                ->set('grado', '1')
                ->assertset('grado', '1')
                ->set('busqueda', $maestro->email) //en esta ocacion buscaremos al maestro por su email
                ->assertset('busqueda', $maestro->email);
            $reporte->call('maestrosPorMateria');
            $reporte->call('materiasSinMaestro');
            $reporte->call('gruposSinMaestro');
            $reporte->call('maestrosSobrecargados');
            $reporte->call('maestrosDisponibles');
            $reporte->call('materiasConMaestro');
            $this->assertTrue(true); //si llega hasta este punto sin que salte una excepcion, la prueba se considerará exitosa
            //actualizacion: aun no encuentro como hacerle, voy a necesitar bastante ayuda
            //act2: aparecen bastantes fallos que no se por donde arreglar, creo que lo voy a dejar por aquí
            //      por este avance e intentar que quede para el tercero, una disculpa.
        } catch (\Throwable $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }

    public function test_Funciones_Reporte_Group(): void
    {
        try {
            //no puedo comprobar si esto realmente puede funcionar, lo dejo para el siguiente avance
            $grupo = Grupo::where('generacion', 2024)->first();         //obtenemos grado, grupo y maestro
            $imparte = Imparte::where('grupo_id', $grupo->id)->first();
            $maestro = $imparte->maestro;
            $maestro = Maestro::find($maestro->id);
            $reporte = Livewire::actingAs($maestro)
                ->test(ReportesGrupo::class)
                ->set('grupo', $grupo->grado . $grupo->letra)     //asignamos algunas propiedades para poder probar las fuunciones
                ->assertSet('grupo', $grupo->grado . $grupo->letra)
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
            $reporte->call('controlEscolar');               //mandamos a llamar a las funciones para cambiar los datos de las tablas y obserbamos que no no de error
            $reporte->call('controlEscolarGrupos');
            $reporte->call('controlEscolarMaestros');
            $reporte->call('reporteMaestroPorMateria');
            $reporte->call('reporteMateriasSinMaestro');
            $reporte->call('reporteGruposSinMaestro');
            $reporte->call('controlEscolarMaestros');
            $this->assertTrue(true); //si consigue llegar hasta aquí significa que no se lanzaron escepciones
        } catch (\Throwable $ex) {
            $this->fail('se encontró un problema en: ' . $ex->getMessage());
        }
    }
}
