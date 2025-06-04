<?php

namespace App\Observers;

use App\Models\CustomAudit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditObserver
{
    /**
     * Handle the Audit "created" event.
     */
    public function created(CustomAudit $audit): void
    {
        $this->enrichAuditData($audit);
    }

    /**
     * Handle the Audit "updated" event.
     */
    public function updated(CustomAudit $audit): void
    {
        $this->enrichAuditData($audit);
    }

    /**
     * Enriquecer datos de auditoría con información adicional
     */
    private function enrichAuditData(CustomAudit $audit): void
    {
        try {
            // Agregar información del usuario autenticado
            if (Auth::check()) {
                $user = Auth::user();

                // Si es un maestro, agregar información adicional
                if ($user instanceof \App\Models\Maestro) {
                    $tags = $audit->tags ? explode(',', $audit->tags) : [];
                    $tags[] = "maestro_id:{$user->id}";
                    $tags[] = "maestro_name:{$user->name}";

                    $audit->tags = implode(',', array_unique($tags));
                }
            }

            // Para auditorías de calificaciones, agregar contexto específico
            if ($audit->auditable_type === 'App\Models\Calificacion') {
                $this->enrichCalificacionAudit($audit);
            }

            // Para auditorías de inscripciones, agregar contexto específico
            if ($audit->auditable_type === 'App\Models\Inscrito') {
                $this->enrichInscripcionAudit($audit);
            }

            $audit->save();

        } catch (\Exception $e) {
            Log::error('Error enriching audit data: ' . $e->getMessage());
        }
    }

    /**
     * Enriquecer auditorías de calificaciones
     */
    private function enrichCalificacionAudit(CustomAudit $audit): void
    {
        $oldValues = $audit->old_values ?? [];
        $newValues = $audit->new_values ?? [];

        // Obtener información del alumno y materia
        $alumnoId = $newValues['alumno_id'] ?? $oldValues['alumno_id'] ?? null;
        $materiaId = $newValues['materia_id'] ?? $oldValues['materia_id'] ?? null;

        if ($alumnoId && $materiaId) {
            $alumno = \App\Models\Alumno::find($alumnoId);
            $materia = \App\Models\Materia::find($materiaId);

            if ($alumno && $materia) {
                $tags = $audit->tags ? explode(',', $audit->tags) : [];
                $tags[] = "alumno_matricula:{$alumno->matricula}";
                $tags[] = "materia_clave:{$materia->clave}";
                $tags[] = "grado:{$materia->grado}";

                // Agregar información de la unidad si está disponible
                if (isset($newValues['unidad'])) {
                    $tags[] = "unidad:{$newValues['unidad']}";
                }

                $audit->tags = implode(',', array_unique($tags));
            }
        }
    }

    /**
     * Enriquecer auditorías de inscripciones
     */
    private function enrichInscripcionAudit(CustomAudit $audit): void
    {
        $oldValues = $audit->old_values ?? [];
        $newValues = $audit->new_values ?? [];

        // Obtener información del alumno y grupo
        $alumnoId = $newValues['alumno_id'] ?? $oldValues['alumno_id'] ?? null;
        $grupoId = $newValues['grupo_id'] ?? $oldValues['grupo_id'] ?? null;

        if ($alumnoId && $grupoId) {
            $alumno = \App\Models\Alumno::find($alumnoId);
            $grupo = \App\Models\Grupo::find($grupoId);

            if ($alumno && $grupo) {
                $tags = $audit->tags ? explode(',', $audit->tags) : [];
                $tags[] = "alumno_matricula:{$alumno->matricula}";
                $tags[] = "grupo_completo:{$grupo->grado}{$grupo->letra}";
                $tags[] = "generacion:{$grupo->generacion}";

                // Agregar información del cambio de estatus
                if (isset($oldValues['estatus']) && isset($newValues['estatus'])) {
                    $tags[] = "cambio_estatus:{$oldValues['estatus']}_to_{$newValues['estatus']}";
                }

                $audit->tags = implode(',', array_unique($tags));
            }
        }
    }
}
