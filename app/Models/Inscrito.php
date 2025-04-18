<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscrito extends Model
{
    use HasFactory;

    // Definimos que estos son los campos que pueden ser llenados masivamente
    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'estatus',
    ];

     // Definición de los posibles valores para 'estatus'
     const ESTATUS_VIGENTE = 'vigente';
     const ESTATUS_BAJA = 'baja';
     const ESTATUS_EGRESADO = 'egresado';

     // Lista de valores válidos para 'estatus'
     public static $estatus = [
         self::ESTATUS_VIGENTE,
         self::ESTATUS_BAJA,
         self::ESTATUS_EGRESADO,
     ];


    // Validación de 'estatus' al momento de la creación o actualización
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!in_array($model->estatus, self::$estatus)) {
                throw new \InvalidArgumentException('El estatus no es válido.');
            }
        });

        static::updating(function ($model) {
            if (!in_array($model->estatus, self::$estatus)) {
                throw new \InvalidArgumentException('El estatus no es válido.');
            }
        });
    }

    public static function estatusOptions()
    {
        return self::$estatus;
    }

    // Relaciones
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    // Accesores para datos del grupo
    public function getGradoAttribute()
    {
        return $this->grupo->grado ?? null;
    }

    public function getGeneracionAttribute()
    {
        return $this->grupo->generacion ?? null;
    }

    public function getSalonAttribute()
    {
        return $this->grupo->letra ?? null;
    }

}
