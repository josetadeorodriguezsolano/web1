<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Inscrito;
use App\Models\Grupo;

class InscritosRules
{
    /**
     * Reglas de validación para crear un nuevo inscrito
     */
    public static function create()
    {
        return [
            'alumno_id' => [
                'required',
                'exists:alumnos,id',
                new AlumnoNoInscritoEnGeneracionRule
            ],
            'grupo_id' => [
                'required',
                'exists:grupos,id',
            ],
            'estatus' => 'required|in:vigente,baja,egresado',
        ];
    }

    /**
     * Reglas de validación para actualizar un inscrito
     */
    public static function update($inscritoId)
    {
        return [
            'grupo_id' => [
                'sometimes',
                'required',
                'exists:grupos,id',
            ],
            'estatus' => 'sometimes|required|in:vigente,baja,egresado',
        ];
    }
}

/**
 * Regla para verificar que un alumno no esté inscrito en la misma generación
 */
class AlumnoNoInscritoEnGeneracionRule implements Rule
{
    protected $alumnoId;
    protected $grupoId;

    public function __construct($alumnoId = null, $grupoId = null)
    {
        $this->alumnoId = $alumnoId;
        $this->grupoId = $grupoId;
    }

    public function passes($attribute, $value)
    {
        // Si no se proporciona grupo_id, usamos el valor del request
        $grupoId = $this->grupoId ?? request('grupo_id');
        
        if (!$grupoId) {
            return true; // Si no hay grupo, no podemos validar la generación
        }

        $grupo = Grupo::find($grupoId);
        
        if (!$grupo) {
            return true; // Si el grupo no existe, otra validación lo manejará
        }

        // Verificar si el alumno ya está inscrito en la misma generación
        // Solo consideramos inscripciones vigentes (ya que ahora no eliminas, solo cambias a baja)
        $inscripcionExistente = Inscrito::whereHas('grupo', function ($query) use ($grupo) {
                $query->where('generacion', $grupo->generacion);
            })
            ->where('alumno_id', $value)
            ->where('estatus', 'vigente')
            ->exists();

        return !$inscripcionExistente;
    }

    public function message()
    {
        return 'El alumno ya está inscrito en esta generación.';
    }
}