<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\InasistenciaInsertarRequest;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inasistencia extends Model
{
    protected $fillable = ['alumno_id', 'materia_id', 'fecha'];

    public $timestamps = true;
    use HasFactory;

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
}