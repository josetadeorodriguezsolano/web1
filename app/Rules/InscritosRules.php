<?php

namespace App\Rules;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Inscrito;
use App\Models\Grupo;

class InscritosRules
{
    /**
     * Obtener las reglas de validación para el componente Inscritos.
     *
     * @return array
     */
    public static function getRules()
    {
        return [
            'generacionSeleccionada' => [
                'sometimes',
                'nullable',
                'numeric',
                'exists:grupos,generacion'
            ],
            'grupoSeleccionado' => [
                'sometimes',
                'nullable',
                'exists:grupos,id'
            ],
            'idEliminar' => [
                'sometimes',
                'required',
                'exists:inscritos,id'
            ]
        ];
    }

    /**
     * Obtener los mensajes de validación personalizados.
     *
     * @return array
     */
    public static function getMessages()
    {
        return [
            'generacionSeleccionada.numeric' => 'La generación seleccionada debe ser un número.',
            'generacionSeleccionada.exists' => 'La generación seleccionada no existe.',
            
            'grupoSeleccionado.exists' => 'El grupo seleccionado no existe.',
            
            'idEliminar.required' => 'Se requiere un ID para eliminar.',
            'idEliminar.exists' => 'El inscrito que intenta eliminar no existe.'
        ];
    }
    
    /**
     * Obtener las reglas para la actualización de estatus de un inscrito.
     *
     * @return array
     */
    public static function getEstatusRules()
    {
        return [
            'estatus' => [
                'required',
                'in:vigente,baja,egresado'
            ]
        ];
    }
    
    /**
     * Obtener los mensajes para la actualización de estatus.
     *
     * @return array
     */
    public static function getEstatusMessages()
    {
        return [
            'estatus.required' => 'El estatus es obligatorio.',
            'estatus.in' => 'El estatus debe ser vigente, baja o egresado.'
        ];
    }
}