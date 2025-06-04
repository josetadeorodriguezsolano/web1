<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Calificacion extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'calificaciones';

    protected $fillable = [
        'alumno_id',
        'materia_id',
        'unidad',
        'calificacion',
    ];

    protected $casts = [
        'alumno_id' => 'integer',
        'materia_id' => 'integer',
        'unidad' => 'integer',
        'calificacion' => 'decimal:2',
    ];

    // Configuración de auditoría
    protected $auditEvents = [
        'created',
        'updated',
        'deleted',
    ];

    protected $auditStrictMode = true;
    protected $auditTimestamps = true;

    // Incluir datos relacionados en la auditoría
    protected $auditInclude = [
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

    /**
     * Transformar datos para auditoría
     */
    public function transformAudit(array $data): array
    {
        if (isset($data['new_values']['alumno_id'])) {
            $alumno = Alumno::find($data['new_values']['alumno_id']);
            $data['new_values']['alumno_info'] = $alumno ? $alumno->matricula . ' - ' . $alumno->nombres . ' ' . $alumno->apellidos : null;
        }

        if (isset($data['new_values']['materia_id'])) {
            $materia = Materia::find($data['new_values']['materia_id']);
            $data['new_values']['materia_info'] = $materia ? $materia->nombre : null;
        }

        return $data;
    }
}