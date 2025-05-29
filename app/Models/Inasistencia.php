<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Inasistencia extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = ['alumno_id', 'materia_id', 'fecha'];

    public $timestamps = true;

    public static function insertar($materia_id, $alumno_id)
    {
        $data = [
            'materia_id' => $materia_id,
            'alumno_id' => $alumno_id,
            'fecha' => now()->format('Y-m-d')
        ];

        $validator = Validator::make($data, [
            'materia_id' => 'required|exists:materias,id',
            'alumno_id' => 'required|exists:alumnos,id',
            'fecha' => 'required|date'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        self::create($data);
        return true;
    }

    public static function eliminar($materia_id, $alumno_id)
    {
        self::where([
            ['materia_id', $materia_id],
            ['alumno_id', $alumno_id],
            ['fecha', now()->format('Y-m-d')],
        ])->delete();
    }

    public static function obtenerPorGrupoMateriaFecha($grupo_id, $materia_id, $fecha)
    {
        return self::whereHas('alumno', function($query) use ($grupo_id) {
                    $query->where('grupo_id', $grupo_id);
                })
                ->where('materia_id', $materia_id)
                ->where('fecha', $fecha)
                ->get();
    }

    // la relacion para acceder a los filtros del alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
