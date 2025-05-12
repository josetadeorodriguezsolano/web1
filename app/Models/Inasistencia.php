<?php

namespace App\Models;

<<<<<<< Updated upstream
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\InasistenciaInsertarRequest;
use Illuminate\Support\Facades\Date;
=======
>>>>>>> Stashed changes
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inasistencia extends Model
{
    use HasFactory;

<<<<<<< Updated upstream
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
=======
    protected $table = 'inasistencias';

    protected $fillable = [
        'imparte_id',
        'horario_id',
        'justificacion',
        'fecha',
    ];

    // Relación con Imparte
   public function imparte()
{
    return $this->belongsTo(Imparte::class);
}
    // Relación con Horario
    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }
public function grupo()
{
    return $this->belongsTo(Imparte::class)->with('grupo');
}
    // Relación con Maestro (a través de Imparte)
    public function maestro()
    {
        return $this->hasOneThrough(Maestro::class, Imparte::class, 'id', 'id', 'imparte_id', 'maestro_id');
    }

    // Relación con Materia (a través de Imparte)
  public function materia()
{
    // Primero se relaciona con 'Imparte' y luego con 'Materia'
    return $this->belongsTo(Imparte::class)->with('materia');
}
>>>>>>> Stashed changes
}
