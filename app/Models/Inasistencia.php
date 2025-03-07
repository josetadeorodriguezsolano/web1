<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\InasistenciaInsertarRequest;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inasistencia extends Model
{
    use HasFactory;

    public static function insertar($grupo_id,$alumno_id){
        $data = [
            'grupo_id' => $grupo_id,
            'alumno_id' => $alumno_id,
            'fecha' => now()->format('Y-m-d')
        ];
        $validator = Validator::make($data, (new InasistenciaInsertarRequest)->rules());
        if ($validator->fails()) {
            return $validator->errors();
        }
        self::create($data);
        return true;
    }

    public static function eliminar($grupo_id,$alumno_id){
        self::where([['grupo_id',$grupo_id],
                    ['alumno_id',$alumno_id],
                    ['fecha',Date::now()->format('Y-m-d')]])->delete();
    }
}
