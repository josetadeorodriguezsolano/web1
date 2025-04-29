<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;



class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'alumno_id',
        'materia_id',
        'grupo_id',
        'imparte_id',
        'unidad',
        'calificacion',
    ];
    // Relaciones
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function imparte()
    {
        return $this->belongsTo(Imparte::class);
    }
    public function materia()
    {
        return $this->imparte->materia();
    }

    public function grupo()
    {
        return $this->imparte->grupo();
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($calificacion) {
            if ($calificacion->calificacion < 1.0 || $calificacion->calificacion > 10.0) {
                throw ValidationException::withMessages([
                    'calificacion' => ['La calificación debe estar entre 1.0 y 10.0'],
                ]);
            }
        });
    }
}



