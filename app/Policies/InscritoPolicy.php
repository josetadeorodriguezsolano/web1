<?php

namespace App\Policies;

use App\Models\Inscrito;
use App\Models\Maestro;
use Illuminate\Auth\Access\Response;

class InscritoPolicy
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
    public function view(Maestro $maestro, Inscrito $inscrito): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Maestro $maestro): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Maestro $maestro, Inscrito $inscrito): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Maestro $maestro, Inscrito $inscrito): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Maestro $maestro, Inscrito $inscrito): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Maestro $maestro, Inscrito $inscrito): bool
    {
        return false;
    }
}
