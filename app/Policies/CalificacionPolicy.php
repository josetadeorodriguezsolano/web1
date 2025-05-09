<?php

namespace App\Policies;

use App\Models\Calificacion;
use App\Models\Maestro;
use App\Models\Imparte;
use Illuminate\Auth\Access\Response;

class CalificacionPolicy
{
    public function viewAny(Maestro $maestro): bool
    {
        return true;
    }

    public function view(Maestro $maestro, Calificacion $calificacion): bool
    {
        return $this->esMateriaDelMaestro($maestro, $calificacion);
    }

    public function create(Maestro $maestro): bool
    {
        return true;
    }

    public function update(Maestro $maestro, Calificacion $calificacion): bool
    {
        return $this->esMateriaDelMaestro($maestro, $calificacion);
    }

    public function delete(Maestro $maestro, Calificacion $calificacion): bool
    {
        return false;
    }

    public function restore(Maestro $maestro, Calificacion $calificacion): bool
    {
        return false;
    }

    public function forceDelete(Maestro $maestro, Calificacion $calificacion): bool
    {
        return false;
    }

// Verifica si la materia de la calificación es impartida por este maestro.

    private function esMateriaDelMaestro(Maestro $maestro, Calificacion $calificacion): bool
    {
        return Imparte::where('materia_id', $calificacion->materia_id)
                      ->where('maestro_id', $maestro->id)
                      ->exists();
    }
}
