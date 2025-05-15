<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inscrito;
use App\Models\Alumno;
use Illuminate\Auth\Access\HandlesAuthorization;

class InscritoPolicy
{
    use HandlesAuthorization;

    /**
     * Realizar acciones sin verificar (para admins)
     */
    public function before(User $user, $ability)
    {
        // Si necesitas definir un super admin, podrías hacerlo aquí
        // Por ejemplo, si el usuario con id 1 es el admin principal
        if ($user->id === 1) {
            return true;
        }
    }

    /**
     * Determinar si el usuario puede ver cualquier inscrito
     */
    public function viewAny(User $user)
    {
        // Por ahora, todos los usuarios autenticados pueden ver la lista
        return true;
    }

    /**
     * Determinar si el usuario puede ver un inscrito específico
     */
    public function view(User $user, Inscrito $inscrito)
    {
        // Todos los usuarios autenticados pueden ver inscripciones
        return true;
    }

    /**
     * Determinar si el usuario puede crear inscritos
     */
    public function create(User $user)
    {
        // Por ahora, cualquier usuario autenticado puede crear
        // Deberías ajustar esto según tu lógica de negocio
        return true;
    }

    /**
     * Determinar si el usuario puede actualizar un inscrito
     */
    public function update(User $user, Inscrito $inscrito)
    {
        // Por ahora, cualquier usuario autenticado puede actualizar
        // Deberías ajustar esto según tu lógica de negocio
        return true;
    }

    /**
     * Determinar si el usuario puede eliminar inscritos
     */
    public function delete(User $user, Inscrito $inscrito)
    {
        // Por ahora, cualquier usuario autenticado puede eliminar
        // Deberías ajustar esto según tu lógica de negocio
        return true;
    }

    /**
     * Determinar si el usuario puede restaurar inscritos eliminados
     */
    public function restore(User $user, Inscrito $inscrito)
    {
        // Si implementas soft deletes
        return true;
    }

    /**
     * Determinar si el usuario puede eliminar permanentemente inscritos
     */
    public function forceDelete(User $user, Inscrito $inscrito)
    {
        // Si implementas soft deletes
        return true;
    }

    /**
     * Verificar si el usuario puede cambiar el estatus del inscrito
     */
    public function cambiarEstatus(User $user, Inscrito $inscrito)
    {
        // Por ahora, cualquier usuario autenticado puede cambiar estatus
        return true;
    }
}