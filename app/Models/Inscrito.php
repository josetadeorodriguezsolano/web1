<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Inscrito extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'estatus',
    ];

    protected $casts = [
        'alumno_id' => 'integer',
        'grupo_id' => 'integer',
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
        'grupo_id',
        'estatus',
    ];

    // Relaciones
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    // Accesores para datos relacionados del grupo
    public function getGradoAttribute()
    {
        return $this->grupo->grado ?? null;
    }

    public function getGeneracionGrupoAttribute()
    {
        return $this->grupo->generacion ?? null;
    }

    public function getSalonAttribute()
    {
        return $this->grupo->letra ?? null;
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

        if (isset($data['new_values']['grupo_id'])) {
            $grupo = Grupo::find($data['new_values']['grupo_id']);
            $data['new_values']['grupo_info'] = $grupo ? $grupo->grado . $grupo->letra . ' - ' . $grupo->generacion : null;
        }

        return $data;
    }
}