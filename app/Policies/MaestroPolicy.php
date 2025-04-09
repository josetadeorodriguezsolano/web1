<?php

namespace App\Policies;

use App\Models\Maestro;
use Illuminate\Auth\Access\Response;

class MaestroPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Maestro $maestro): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Maestro $usuario, Maestro $maestro): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Maestro $maestro): bool
    {
        if ($maestro->id == 10)
            return true;
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Maestro $usuario, Maestro $maestro): bool
    {
        if ($usuario->id == 10 && !$maestro->isDirty('email'))
            return true;
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Maestro $usuario, Maestro $maestro): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Maestro $usuario, Maestro $maestro): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Maestro $usuario, Maestro $maestro): bool
    {
        return false;
    }
}
