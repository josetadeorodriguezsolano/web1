<?php

namespace App\Rules;

class AuditoriaInscripcionesRules
{
    public static function generationRules(): array
    {
        return [
            'required',
            'integer',
            'between:2000,2099',
        ];
    }

    public static function groupRules(): array
    {
        return [
            'required',
            'exists:grupos,id',
        ];
    }
}