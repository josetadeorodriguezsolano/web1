<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\InasistenciaInsertarRequest;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;


class Inasistencia extends Model implements AuditableContract
{


        use Auditable;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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
}
