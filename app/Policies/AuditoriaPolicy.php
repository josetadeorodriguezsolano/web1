<?php

namespace App\Policies;

use App\Models\Maestro;
use Illuminate\Auth\Access\Response;

class AuditoriaPolicy
{
    /**
     * Determinar si el usuario puede ver cualquier reporte de auditoría
     */
    public function viewAny(Maestro $maestro): bool
    {
        // Solo maestros autenticados pueden ver reportes básicos
        return $maestro !== null;
    }

    /**
     * Determinar si el usuario puede ver reportes de auditorías de calificaciones
     */
    public function viewCalificaciones(Maestro $maestro): bool
    {
        // Maestros con permisos especiales (directores/administradores) o maestros que imparten materias
        return $this->esAdministrador($maestro) || $this->tieneMateriasAsignadas($maestro);
    }

    /**
     * Determinar si el usuario puede ver reportes de auditorías de inscripciones
     */
    public function viewInscripciones(Maestro $maestro): bool
    {
        // Solo administradores pueden ver cambios de inscripciones por seguridad
        return $this->esAdministrador($maestro);
    }

    /**
     * Determinar si el usuario puede usar filtros avanzados en reportes de calificaciones
     */
    public function useAdvancedFiltersCalificaciones(Maestro $maestro): bool
    {
        // Administradores tienen acceso completo, maestros regulares solo a sus materias
        return $this->esAdministrador($maestro) || $this->tieneMateriasAsignadas($maestro);
    }

    /**
     * Determinar si el usuario puede usar filtros avanzados en reportes de inscripciones
     */
    public function useAdvancedFiltersInscripciones(Maestro $maestro): bool
    {
        // Solo administradores pueden usar filtros avanzados de inscripciones
        return $this->esAdministrador($maestro);
    }

    /**
     * Determinar si el usuario puede ver datos históricos (más de 6 meses)
     */
    public function viewHistoricalData(Maestro $maestro): bool
    {
        // Solo administradores pueden ver datos históricos extensos
        return $this->esAdministrador($maestro);
    }

    /**
     * Determinar si el usuario puede buscar por cualquier alumno
     */
    public function searchAnyStudent(Maestro $maestro): bool
    {
        // Solo administradores pueden buscar cualquier alumno
        // Maestros regulares solo alumnos de sus grupos
        return $this->esAdministrador($maestro);
    }

    /**
     * Determinar si el usuario puede ver auditorías de todos los maestros
     */
    public function viewAllTeachersAudits(Maestro $maestro): bool
    {
        // Solo administradores pueden ver auditorías de otros maestros
        return $this->esAdministrador($maestro);
    }

    /**
     * Determinar si el usuario puede exportar reportes de auditoría
     */
    public function exportAuditReports(Maestro $maestro): bool
    {
        // Administradores y maestros con materias asignadas pueden exportar
        return $this->esAdministrador($maestro) || $this->tieneMateriasAsignadas($maestro);
    }

    /**
     * Determinar si el usuario puede ver detalles completos de cada auditoría
     */
    public function viewFullAuditDetails(Maestro $maestro): bool
    {
        // Administradores ven detalles completos, maestros regulares solo básicos
        return $this->esAdministrador($maestro);
    }

    /**
     * Determinar si el usuario puede ver reportes de auditorías por rango de fechas personalizado
     */
    public function useCustomDateRange(Maestro $maestro): bool
    {
        // Todos los maestros pueden usar rangos de fecha, pero limitados
        if ($this->esAdministrador($maestro)) {
            return true; // Sin límites para administradores
        }

        // Maestros regulares limitados a los últimos 3 meses
        return $this->tieneMateriasAsignadas($maestro);
    }

    /**
     * Política específica para filtrar auditorías por materia
     */
    public function filterBySubject(Maestro $maestro, ?int $materiaId = null): bool
    {
        if ($this->esAdministrador($maestro)) {
            return true; // Administradores pueden filtrar por cualquier materia
        }

        if ($materiaId === null) {
            return true; // Permitir vista general
        }

        // Verificar si el maestro imparte esa materia
        return $this->maestroImparteMateria($maestro, $materiaId);
    }

    /**
     * Política específica para filtrar auditorías por grupo
     */
    public function filterByGroup(Maestro $maestro, ?int $grupoId = null): bool
    {
        if ($this->esAdministrador($maestro)) {
            return true; // Administradores pueden filtrar por cualquier grupo
        }

        if ($grupoId === null) {
            return true; // Permitir vista general
        }

        // Verificar si el maestro imparte en ese grupo
        return $this->maestroImparteEnGrupo($maestro, $grupoId);
    }

    /**
     * Determinar si el usuario es administrador/director
     */
    private function esAdministrador(Maestro $maestro): bool
    {
        // Solo el maestro con ID 10 es administrador según tu contexto
        // Removimos ID 1, 2, 3 que parecen ser maestros regulares
        return in_array($maestro->id, [10]); // Solo admin real
    }

    /**
     * Determinar si el maestro tiene materias asignadas
     */
    private function tieneMateriasAsignadas(Maestro $maestro): bool
    {
        // Verificar si el maestro tiene al menos una materia asignada
        return $maestro->imparte()->exists();
    }

    /**
     * Verificar si el maestro imparte una materia específica
     */
    private function maestroImparteMateria(Maestro $maestro, int $materiaId): bool
    {
        return $maestro->imparte()
            ->where('materia_id', $materiaId)
            ->exists();
    }

    /**
     * Verificar si el maestro imparte en un grupo específico
     */
    private function maestroImparteEnGrupo(Maestro $maestro, int $grupoId): bool
    {
        return $maestro->imparte()
            ->where('grupo_id', $grupoId)
            ->exists();
    }

    /**
     * Obtener mensaje de error personalizado para acceso denegado
     */
    public function denyWithMessage(string $action): Response
    {
        $messages = [
            'viewCalificaciones' => 'No tienes permisos para ver reportes de calificaciones.',
            'viewInscripciones' => 'Solo los administradores pueden ver reportes de inscripciones.',
            'viewHistoricalData' => 'No tienes permisos para acceder a datos históricos.',
            'searchAnyStudent' => 'Solo puedes buscar alumnos de tus grupos asignados.',
            'viewAllTeachersAudits' => 'Solo puedes ver tus propias auditorías.',
            'exportAuditReports' => 'No tienes permisos para exportar reportes.',
        ];

        return Response::deny($messages[$action] ?? 'Acceso denegado.');
    }

    /**
     * Política para determinar qué datos puede ver el maestro en los reportes
     */
    public function getViewableScope(Maestro $maestro): array
    {
        if ($this->esAdministrador($maestro)) {
            return [
                'scope' => 'all',
                'materias' => 'all',
                'grupos' => 'all',
                'maestros' => 'all',
                'date_limit' => null // Sin límite de fechas
            ];
        }

        // Maestros regulares - solo sus datos
        $materiasIds = $maestro->imparte()->pluck('materia_id')->toArray();
        $gruposIds = $maestro->imparte()->pluck('grupo_id')->toArray();

        return [
            'scope' => 'limited',
            'materias' => $materiasIds,
            'grupos' => $gruposIds,
            'maestros' => [$maestro->id], // Solo sus propias auditorías
            'date_limit' => now()->subMonths(3) // Últimos 3 meses
        ];
    }
}
