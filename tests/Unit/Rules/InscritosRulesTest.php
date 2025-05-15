<?php

namespace Tests\Unit\Rules;

use Tests\TestCase;
use App\Rules\InscritosRules;
use App\Rules\AlumnoNoInscritoEnGeneracionRule;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Inscrito;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

class InscritosRulesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reglas_de_creacion_requieren_campos_obligatorios()
    {
        $rules = InscritosRules::create();

        $validator = Validator::make([], $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('alumno_id'));
        $this->assertTrue($validator->errors()->has('grupo_id'));
        $this->assertTrue($validator->errors()->has('estatus'));
    }

    /** @test */
    public function alumno_id_debe_existir_en_tabla_alumnos()
    {
        $rules = InscritosRules::create();

        $data = [
            'alumno_id' => 999, // ID que no existe
            'grupo_id' => 1,
            'estatus' => 'vigente'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('alumno_id'));
    }

    /** @test */
    public function grupo_id_debe_existir_en_tabla_grupos()
    {
        $alumno = Alumno::create([
            'matricula' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'curp' => 'PERJ990101HDFRNN01',
            'contacto' => '1234567890',
            'tutor' => 'María Pérez'
        ]);

        $rules = InscritosRules::create();

        $data = [
            'alumno_id' => $alumno->id,
            'grupo_id' => 999, // ID que no existe
            'estatus' => 'vigente'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('grupo_id'));
    }

    /** @test */
    public function estatus_debe_ser_valido()
    {
        $alumno = Alumno::create([
            'matricula' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'curp' => 'PERJ990101HDFRNN01',
            'contacto' => '1234567890',
            'tutor' => 'María Pérez'
        ]);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $rules = InscritosRules::create();

        // Estatus inválido
        $data = [
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'invalido'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('estatus'));

        // Estatus válidos
        foreach (['vigente', 'baja', 'egresado'] as $estatus) {
            $data['estatus'] = $estatus;
            $validator = Validator::make($data, $rules);
            $this->assertTrue($validator->passes());
        }
    }

    /** @test */
    public function no_permite_alumno_inscrito_en_misma_generacion()
    {
        // Guardar la configuración actual del request
        $originalRequest = request();
        
        $grupo1 = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $grupo2 = Grupo::create([
            'grado' => '2',
            'letra' => 'B',
            'generacion' => 2024 // Misma generación
        ]);

        $alumno = Alumno::create([
            'matricula' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'curp' => 'PERJ990101HDFRNN01',
            'contacto' => '1234567890',
            'tutor' => 'María Pérez'
        ]);

        // Primera inscripción
        Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo1->id,
            'estatus' => 'vigente'
        ]);

        // Simular que el request tiene el grupo_id
        request()->merge(['grupo_id' => $grupo2->id]);

        // Intentar segunda inscripción en la misma generación
        $rules = InscritosRules::create();
        $data = [
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo2->id, // Diferente grupo pero misma generación
            'estatus' => 'vigente'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('alumno_id'));
        $this->assertStringContainsString('ya está inscrito en esta generación', $validator->errors()->first('alumno_id'));
        
        // Restaurar el request original
        app()->instance('request', $originalRequest);
    }

    /** @test */
    public function permite_alumno_con_baja_inscribirse_nuevamente()
    {
        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $alumno = Alumno::create([
            'matricula' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'curp' => 'PERJ990101HDFRNN01',
            'contacto' => '1234567890',
            'tutor' => 'María Pérez'
        ]);

        // Inscripción anterior con baja
        Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'baja'
        ]);

        // Intentar nueva inscripción
        $rules = InscritosRules::create();
        $data = [
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function permite_alumno_inscribirse_en_diferente_generacion()
    {
        $grupo2023 = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2023
        ]);

        $grupo2024 = Grupo::create([
            'grado' => '2',
            'letra' => 'A',
            'generacion' => 2024 // Diferente generación
        ]);

        $alumno = Alumno::create([
            'matricula' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'curp' => 'PERJ990101HDFRNN01',
            'contacto' => '1234567890',
            'tutor' => 'María Pérez'
        ]);

        // Primera inscripción en 2023
        Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo2023->id,
            'estatus' => 'vigente'
        ]);

        // Segunda inscripción en 2024 (debe permitirse)
        $rules = InscritosRules::create();
        $data = [
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo2024->id,
            'estatus' => 'vigente'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function reglas_de_actualizacion_son_opcionales()
    {
        // Crear un inscrito manualmente en lugar de usar factory
        $alumno = Alumno::create([
            'matricula' => '99999999',
            'nombres' => 'Test',
            'apellidos' => 'Actualización',
            'curp' => 'TEAC990101HDFRNN01',
            'contacto' => '9999999999',
            'tutor' => 'Tutor Test'
        ]);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'Z',
            'generacion' => 2024
        ]);

        $inscrito = Inscrito::create([
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ]);

        $rules = InscritosRules::update($inscrito->id);

        // Sin datos debe pasar
        $validator = Validator::make([], $rules);
        $this->assertTrue($validator->passes());

        // Con solo estatus debe pasar
        $validator = Validator::make(['estatus' => 'baja'], $rules);
        $this->assertTrue($validator->passes());

        // Con solo grupo_id debe pasar
        $nuevoGrupo = Grupo::create([
            'grado' => '2',
            'letra' => 'Y',
            'generacion' => 2024
        ]);
        
        $validator = Validator::make(['grupo_id' => $nuevoGrupo->id], $rules);
        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function validacion_funciona_con_todos_los_datos_correctos()
    {
        $alumno = Alumno::create([
            'matricula' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'curp' => 'PERJ990101HDFRNN01',
            'contacto' => '1234567890',
            'tutor' => 'María Pérez'
        ]);

        $grupo = Grupo::create([
            'grado' => '1',
            'letra' => 'A',
            'generacion' => 2024
        ]);

        $rules = InscritosRules::create();

        $data = [
            'alumno_id' => $alumno->id,
            'grupo_id' => $grupo->id,
            'estatus' => 'vigente'
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->passes());
        $this->assertEmpty($validator->errors()->all());
    }
}