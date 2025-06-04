<?php

namespace App\Observers;

use App\Models\Inscrito;
use App\Models\CustomAudit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InscripcionStatusObserver
{
    /**
     * Handle the Inscrito "updated" event.
     * Se ejecuta cuando se actualiza una inscripción
     */
    public function updated(Inscrito $inscrito): void
    {
        // Solo procesar si cambió el estatus
        if ($inscrito->isDirty('estatus')) {
            $this->registrarCambioEstatus($inscrito);
        }
    }

    /**
     * Handle the Inscrito "created" event.
     */
    public function created(Inscrito $inscrito): void
    {
        $this->registrarEventoInscripcion($inscrito, 'inscripcion_creada');
    }

    /**
     * Registrar cambio de estatus específico
     */
    private function registrarCambioEstatus(Inscrito $inscrito): void
    {
        try {
            $estatusAnterior = $inscrito->getOriginal('estatus');
            $estatusNuevo = $inscrito->estatus;

            // Crear registro de auditoría específico para cambio de estatus
            $auditData = [
                'user_type' => Auth::check() ? get_class(Auth::user()) : null,
                'user_id' => Auth::id(),
                'event' => 'cambio_estatus_inscripcion',
                'auditable_type' => 'App\Models\Inscrito',
                'auditable_id' => $inscrito->id,
                'old_values' => [
                    'estatus' => $estatusAnterior,
                    'alumno_id' => $inscrito->alumno_id,
                    'grupo_id' => $inscrito->grupo_id,
                ],
                'new_values' => [
                    'estatus' => $estatusNuevo,
                    'alumno_id' => $inscrito->alumno_id,
                    'grupo_id' => $inscrito->grupo_id,
                ],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'academic_year' => $this->obtenerAñoAcademico($inscrito),
                'grupo_id' => $inscrito->grupo_id,
                'academic_context' => $this->generarContextoAcademico($inscrito),
                'tags' => $this->generarTagsCambioEstatus($inscrito, $estatusAnterior, $estatusNuevo),
            ];

            CustomAudit::create($auditData);

            // Log para seguimiento
            Log::info("Cambio de estatus de inscripción registrado", [
                'inscrito_id' => $inscrito->id,
                'alumno_id' => $inscrito->alumno_id,
                'estatus_anterior' => $estatusAnterior,
                'estatus_nuevo' => $estatusNuevo,
                'usuario' => Auth::user()->name ?? 'Sistema'
            ]);

            // Verificar si es un cambio crítico que requiere alerta
            $this->verificarCambioCritico($inscrito, $estatusAnterior, $estatusNuevo);
        } catch (\Exception $e) {
            Log::error('Error al registrar cambio de estatus de inscripción: ' . $e->getMessage(), [
                'inscrito_id' => $inscrito->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Registrar evento general de inscripción
     */
    private function registrarEventoInscripcion(Inscrito $inscrito, string $tipoEvento): void
    {
        try {
            $auditData = [
                'user_type' => Auth::check() ? get_class(Auth::user()) : null,
                'user_id' => Auth::id(),
                'event' => $tipoEvento,
                'auditable_type' => 'App\Models\Inscrito',
                'auditable_id' => $inscrito->id,
                'old_values' => [],
                'new_values' => [
                    'estatus' => $inscrito->estatus,
                    'alumno_id' => $inscrito->alumno_id,
                    'grupo_id' => $inscrito->grupo_id,
                ],
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'academic_year' => $this->obtenerAñoAcademico($inscrito),
                'grupo_id' => $inscrito->grupo_id,
                'academic_context' => $this->generarContextoAcademico($inscrito),
                'tags' => $this->generarTagsCreacion($inscrito),
            ];

            CustomAudit::create($auditData);
        } catch (\Exception $e) {
            Log::error('Error al registrar evento de inscripción: ' . $e->getMessage());
        }
    }

    /**
     * Obtener el año académico basado en el grupo
     */
    private function obtenerAñoAcademico(Inscrito $inscrito): ?int
    {
        if ($inscrito->grupo) {
            return $inscrito->grupo->generacion;
        }
        return date('Y');
    }

    /**
     * Generar contexto académico legible
     */
    private function generarContextoAcademico(Inscrito $inscrito): string
    {
        $contexto = [];

        if ($inscrito->alumno) {
            $contexto[] = "Alumno: {$inscrito->alumno->matricula} - {$inscrito->alumno->nombres} {$inscrito->alumno->apellidos}";
        }

        if ($inscrito->grupo) {
            $contexto[] = "Grupo: {$inscrito->grupo->grado}°{$inscrito->grupo->letra} Generación {$inscrito->grupo->generacion}";
        }

        return implode(' | ', $contexto);
    }

    /**
     * Generar tags para cambio de estatus
     */
    private function generarTagsCambioEstatus(Inscrito $inscrito, string $estatusAnterior, string $estatusNuevo): string
    {
        $tags = [
            "cambio_estatus:{$estatusAnterior}_to_{$estatusNuevo}",
            "tipo:cambio_estatus_inscripcion"
        ];

        if ($inscrito->alumno) {
            $tags[] = "matricula:{$inscrito->alumno->matricula}";
        }

        if ($inscrito->grupo) {
            $tags[] = "grupo:{$inscrito->grupo->grado}{$inscrito->grupo->letra}";
            $tags[] = "generacion:{$inscrito->grupo->generacion}";
        }

        if (Auth::check()) {
            $user = Auth::user();
            $tags[] = "usuario_id:{$user->id}";
            $tags[] = "usuario_nombre:{$user->name}";
        }

        // Tags específicos según el tipo de cambio
        $tags = array_merge($tags, $this->getTagsEspecificosCambio($estatusAnterior, $estatusNuevo));

        return implode(',', $tags);
    }

    /**
     * Generar tags para creación de inscripción
     */
    private function generarTagsCreacion(Inscrito $inscrito): string
    {
        $tags = [
            "tipo:nueva_inscripcion",
            "estatus_inicial:{$inscrito->estatus}"
        ];

        if ($inscrito->alumno) {
            $tags[] = "matricula:{$inscrito->alumno->matricula}";
        }

        if ($inscrito->grupo) {
            $tags[] = "grupo:{$inscrito->grupo->grado}{$inscrito->grupo->letra}";
            $tags[] = "generacion:{$inscrito->grupo->generacion}";
        }

        return implode(',', $tags);
    }

    /**
     * Obtener tags específicos según el tipo de cambio
     */
    private function getTagsEspecificosCambio(string $estatusAnterior, string $estatusNuevo): array
    {
        $tags = [];

        // Cambios críticos
        if ($estatusAnterior === 'vigente' && $estatusNuevo === 'baja') {
            $tags[] = "alerta:baja_alumno";
            $tags[] = "critico:perdida_alumno";
        }

        if ($estatusAnterior === 'baja' && $estatusNuevo === 'vigente') {
            $tags[] = "alerta:reactivacion_alumno";
            $tags[] = "positivo:recuperacion_alumno";
        }

        if ($estatusNuevo === 'egresado') {
            $tags[] = "positivo:egreso_exitoso";
            $tags[] = "milestone:graduacion";
        }

        // Patrones temporales
        $tags[] = "fecha:" . date('Y-m-d');
        $tags[] = "hora:" . date('H:i');
        $tags[] = "dia_semana:" . date('N'); // 1=lunes, 7=domingo

        return $tags;
    }

    /**
     * Verificar si es un cambio crítico que requiere alerta
     */
    private function verificarCambioCritico(Inscrito $inscrito, string $estatusAnterior, string $estatusNuevo): void
    {
        $cambiosCriticos = [
            'vigente_to_baja' => $estatusAnterior === 'vigente' && $estatusNuevo === 'baja',
            'baja_masiva' => $this->detectarBajaMasiva($inscrito->grupo_id),
            'reactivacion_multiple' => $estatusAnterior === 'baja' && $estatusNuevo === 'vigente' && $this->detectarReactivacionMultiple($inscrito->alumno_id),
        ];

        foreach ($cambiosCriticos as $tipoCritico => $esCritico) {
            if ($esCritico) {
                $this->generarAlertaCritica($inscrito, $tipoCritico, $estatusAnterior, $estatusNuevo);
            }
        }
    }

    /**
     * Detectar si hay una baja masiva en el grupo (más de 3 bajas en 24 horas)
     */
    private function detectarBajaMasiva(int $grupoId): bool
    {
        $bajasRecientes = CustomAudit::where('auditable_type', 'App\Models\Inscrito')
            ->where('grupo_id', $grupoId)
            ->where('event', 'cambio_estatus_inscripcion')
            ->where('created_at', '>=', now()->subDay())
            ->whereJsonContains('new_values->estatus', 'baja')
            ->count();

        return $bajasRecientes >= 3;
    }

    /**
     * Detectar reactivación múltiple del mismo alumno
     */
    private function detectarReactivacionMultiple(int $alumnoId): bool
    {
        $reactivacionesRecientes = CustomAudit::where('auditable_type', 'App\Models\Inscrito')
            ->whereJsonContains('new_values->alumno_id', $alumnoId)
            ->where('event', 'cambio_estatus_inscripcion')
            ->where('created_at', '>=', now()->subWeek())
            ->whereJsonContains('old_values->estatus', 'baja')
            ->whereJsonContains('new_values->estatus', 'vigente')
            ->count();

        return $reactivacionesRecientes >= 2;
    }

    /**
     * Generar alerta crítica
     */
    private function generarAlertaCritica(Inscrito $inscrito, string $tipoCritico, string $estatusAnterior, string $estatusNuevo): void
    {
        $mensaje = match ($tipoCritico) {
            'vigente_to_baja' => "ALERTA: Alumno {$inscrito->alumno->matricula} dado de baja",
            'baja_masiva' => "ALERTA CRÍTICA: Baja masiva detectada en grupo {$inscrito->grupo->grado}{$inscrito->grupo->letra}",
            'reactivacion_multiple' => "ALERTA: Múltiples reactivaciones para alumno {$inscrito->alumno->matricula}",
            default => "Cambio crítico detectado en inscripción"
        };

        Log::warning($mensaje, [
            'tipo_critico' => $tipoCritico,
            'inscrito_id' => $inscrito->id,
            'alumno_matricula' => $inscrito->alumno->matricula ?? 'N/A',
            'grupo' => ($inscrito->grupo->grado ?? '') . ($inscrito->grupo->letra ?? ''),
            'estatus_anterior' => $estatusAnterior,
            'estatus_nuevo' => $estatusNuevo,
            'timestamp' => now()->toISOString()
        ]);
    }
}
