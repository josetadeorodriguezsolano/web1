<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'alumno_id',
        'materia_id',
        'unidad',
        'calificacion',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    protected static function boot()
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

    /**
     * Actualiza o crea una calificación para un alumno en una materia y unidad específica
     * 
     * @param int $alumnoId ID del alumno
     * @param int $materiaId ID de la materia
     * @param int $unidad Número de unidad (1-4)
     * @param float|null $valor Valor de la calificación
     * @param int|null $calificacionId ID de la calificación existente (opcional)
     * @return \App\Models\Calificacion
     */
    public static function actualizarCalificacion($alumnoId, $materiaId, $unidad, $valor, $calificacionId = null)
    {
        $datosCalificacion = [
            'alumno_id' => $alumnoId,
            'materia_id' => $materiaId,
            'unidad' => $unidad,
            'calificacion' => $valor ?? 0
        ];

        if ($calificacionId) {
            $calificacion = self::find($calificacionId);
            if ($calificacion) {
                $calificacion->update($datosCalificacion);
                return $calificacion;
            }
        }

        return self::create($datosCalificacion);
    }
}
