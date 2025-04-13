<?php

namespace Tests\Unit;

use App\Models\Alumno;
use App\Models\Falta;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlumnoTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic unit test example.
     */
    public function test_falto(): void
    {
        $alumno = Alumno::factory(1)->create()->first();
        $falta = Falta::create(['alumno_id'=>$alumno->id,
                        'fecha'=>date('Y-m-d')]);
        $this->assertTrue($alumno->falto(date('Y-m-d'))->id == $falta->id);
    }
}
