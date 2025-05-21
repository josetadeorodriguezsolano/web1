<?php

namespace App\Policies;

use App\Models\Alumno;
use App\Models\Maestro;
use App\Models\Inscrito;
use App\Models\Imparte;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\DB;

class AlumnoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Maestro $maestro): bool
    {
        // Cualquier maestro autenticado puede ver la lista de alumnos
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Maestro $maestro, Alumno $alumno): bool
    {
        // El maestro puede ver información de un alumno si imparte una materia en algún grupo donde está el alumno
        return $this->maestroImparteAlAlumno($maestro, $alumno);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Maestro $maestro): bool
    {
        // Solo los maestros con permisos especiales (directores o administradores) pueden crear alumnos
        // Por ahora, solo permitiremos a maestros con ID específicos
        return in_array($maestro->id, [1,2,3]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Maestro $maestro, Alumno $alumno): bool
    {
        // El maestro puede actualizar información del alumno si imparte materia a ese alumno
        // o si tiene permisos especiales
        return $this->maestroImparteAlAlumno($maestro, $alumno) ||
               in_array($maestro->id, [1,2,3]); // IDs de maestros con permisos especiales
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Maestro $maestro, Alumno $alumno): bool
    {
        // Solo maestros con permisos especiales pueden eliminar alumnos
        return in_array($maestro->id, [1,2,3]); // Solo algunos maestros específicos
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Maestro $maestro, Alumno $alumno): bool
    {
        // Solo maestros con permisos especiales pueden restaurar alumnos
        return in_array($maestro->id, [1,2,3]); // Solo algunos maestros específicos
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Maestro $maestro, Alumno $alumno): bool
    {
        // Solo maestros con permisos especiales pueden eliminar permanentemente alumnos
        return in_array($maestro->id, [1]); // Muy restrictivo, solo el director
    }

    /**
     * Determine whether the user can change the estatus of the model.
     */
    public function changeStatus(Maestro $maestro, Alumno $alumno): bool
    {
        // El maestro puede cambiar el estatus del alumno si imparte materia a ese alumno
        // o si tiene permisos especiales
        return $this->maestroImparteAlAlumno($maestro, $alumno) ||
               in_array($maestro->id, [1,2,3]); // IDs de maestros con permisos especiales
    }

    /**
     * Determine if the teacher teaches to the student.
     */
    private function maestroImparteAlAlumno(Maestro $maestro, Alumno $alumno): bool
    {
        // Obtener los grupos donde está inscrito el alumno
        $gruposDelAlumno = Inscrito::where('alumno_id', $alumno->id)
                                 ->where('estatus', 'vigente')
                                 ->pluck('grupo_id');

        // Verificar si el maestro imparte alguna materia en alguno de esos grupos
        return Imparte::where('maestro_id', $maestro->id)
                   ->whereIn('grupo_id', $gruposDelAlumno)
                   ->exists();
    }
}