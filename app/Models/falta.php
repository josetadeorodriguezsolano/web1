<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Falta extends Model
{
    use HasFactory;
    public $table="faltas";
    public $timestamps=true;
    protected $fillable = ['alumno_id', 'fecha', 'justificada'];

    // Relación con Alumnos
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public static function insertar($alumno_id){
        $data = [
            'alumno_id' => $alumno_id,
            'fecha' => now()->format('Y-m-d')
        ];
        /*$validator = Validator::make($data);
        if ($validator->fails()) {
            return $validator->errors();
        }*/
        self::create($data);
        return true;
    }

    public static function eliminar($alumno_id){
        self::where([['alumno_id',$alumno_id],
                     ['fecha',date('Y-m-d')]])->delete();
    }
}
