<?php

namespace App\Rules;

use App\Models\Grupo;

class AuditoriaInscripcionesRules
{
    /**
     * Obtener las reglas de validación para generación.
     *
     * @return array
     */
    public static function generationRules(): array
    {
        return [
            'nullable',
            'integer',
            'between:2000,2099',
            'exists:grupos,generacion'
        ];
    }

    /**
     * Obtener las reglas de validación para grupo.
     *
     * @return array
     */
    public static function groupRules(): array
    {
        return [
            'nullable',
            'integer',
            'exists:grupos,id'
        ];
    }

    /**
     * Obtener los mensajes de validación personalizados para generación.
     *
     * @return array
     */
    public static function generationMessages(): array
    {
        return [
            'generacionSeleccionada.integer' => 'La generación debe ser un número entero.',
            'generacionSeleccionada.between' => 'La generación debe estar entre 2000 y 2099.',
            'generacionSeleccionada.exists' => 'La generación seleccionada no existe en los registros.'
        ];
    }

    /**
     * Obtener los mensajes de validación personalizados para grupo.
     *
     * @return array
     */
    public static function groupMessages(): array
    {
        return [
            'grupoSeleccionado.integer' => 'El grupo debe ser un número entero.',
            'grupoSeleccionado.exists' => 'El grupo seleccionado no existe en los registros.'
        ];
    }

    /**
     * Validar que el grupo pertenezca a la generación seleccionada.
     *
     * @param int|null $grupoId
     * @param int|null $generacion
     * @return bool
     */
    public static function validateGroupBelongsToGeneration($grupoId, $generacion): bool
    {
        if (!$grupoId || !$generacion) {
            return true; // Si alguno es null, no validamos la relación
        }

        return Grupo::where('id', $grupoId)
            ->where('generacion', $generacion)
            ->exists();
    }

    /**
     * Obtener todas las reglas de validación para filtros de reportes.
     *
     * @return array
     */
    public static function getFilterRules(): array
    {
        return [
            'generacionSeleccionada' => self::generationRules(),
            'grupoSeleccionado' => self::groupRules(),
        ];
    }

    /**
     * Obtener todos los mensajes de validación para filtros de reportes.
     *
     * @return array
     */
    public static function getFilterMessages(): array
    {
        return array_merge(
            self::generationMessages(),
            self::groupMessages()
        );
    }
}
