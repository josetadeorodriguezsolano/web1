<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    // Configuración de auditoría
    protected $auditEvents = [
        'created',
        'updated',
        'deleted',
    ];

    protected $auditStrictMode = true;
    protected $auditTimestamps = true;

    // Relaciones
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function grupo()
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
}
