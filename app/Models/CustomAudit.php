<?php

namespace App\Models;

use OwenIt\Auditing\Models\Audit as AuditModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomAudit extends AuditModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_type',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags',
        'academic_year',
        'grupo_id',
        'materia_id',
        'academic_context',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
        'academic_year' => 'integer',
    ];

    /**
     * Relación con el grupo relacionado
     */
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    /**
     * Relación con la materia relacionada
     */
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    /**
     * Scope para filtrar por año académico
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope para filtrar por grupo
     */
    public function scopeByGrupo($query, $grupoId)
    {
        return $query->where('grupo_id', $grupoId);
    }

    /**
     * Scope para filtrar por materia
     */
    public function scopeByMateria($query, $materiaId)
    {
        return $query->where('materia_id', $materiaId);
    }

    /**
     * Scope para reportes de calificaciones
     */
    public function scopeCalificacionesReport($query, $alumnoId, $materiaId = null)
    {
        $query->where('auditable_type', 'App\Models\Calificacion')
              ->whereHas('auditable', function ($q) use ($alumnoId) {
                  $q->where('alumno_id', $alumnoId);
              });

        if ($materiaId) {
            $query->where('materia_id', $materiaId);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope para reportes de inscripciones
     */
    public function scopeInscripcionesReport($query, $grupoId = null, $academicYear = null)
    {
        $query->where('auditable_type', 'App\Models\Inscrito');

        if ($grupoId) {
            $query->where('grupo_id', $grupoId);
        }

        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Obtener el nombre del usuario que realizó el cambio
     */
    public function getUserNameAttribute()
    {
        if ($this->user) {
            return $this->user->name ?? 'Usuario desconocido';
        }
        return 'Sistema';
    }

    /**
     * Obtener descripción legible del evento
     */
    public function getEventDescriptionAttribute()
    {
        return match($this->event) {
            'created' => 'Creado',
            'updated' => 'Modificado',
            'deleted' => 'Eliminado',
            'restored' => 'Restaurado',
            default => ucfirst($this->event)
        };
    }

    /**
     * Obtener diferencias en formato legible
     */
    public function getReadableDifferencesAttribute()
    {
        $differences = [];
        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];

        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $differences[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $differences;
    }
}
