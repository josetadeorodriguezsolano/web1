<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
    use HasFactory;


    protected $fillable = [
        'grado',
        'letra',
        'generacion',
    ];

    public function inscritos()
    { //Collection
        return $this->hasMany(Inscrito::class); //,'grupo_id','id');
    }

    public function impartes()
    {
        return $this->hasMany(Imparte::class);
    }

    public function alumnos()
    {
        return $this->hasManyThrough(
            Alumno::class,
            Inscrito::class,
            'grupo_id',
            'id',
            'id',
            'alumno_id'
        )->orderBy('apellidos');
    }

    public function materias() //Cambio para poder obtener las materias que cursa un grupo
    {
        return $this->hasManyThrough(
            Materia::class,
            Imparte::class,
            'grupo_id',
            'id',
            'id',
            'materia_id'
        );
    }

    /**
     * Obtiene los alumnos del grupo con sus calificaciones para una materia específica
     * 
     * @param int $materiaId El ID de la materia
     * @return array
     */
    public function obtenerAlumnosConCalificaciones($materiaId)
    {
        $alumnos = $this->alumnos()->orderBy('apellidos')->get();

        $calificacionesExistentes = \App\Models\Calificacion::where('materia_id', $materiaId)
            ->whereIn('alumno_id', $alumnos->pluck('id'))
            ->get()
            ->groupBy('alumno_id')
            ->toArray();

        $alumnosArray = $alumnos->toArray();
        $calificacionesFormateadas = [];

        foreach ($alumnosArray as $alumno) {
            $alumnoId = $alumno['id'];

            // Inicializar calificaciones para las 4 unidades
            $calificacionesFormateadas[$alumnoId] = [
                1 => ['valor' => null, 'id' => null],
                2 => ['valor' => null, 'id' => null],
                3 => ['valor' => null, 'id' => null],
                4 => ['valor' => null, 'id' => null],
            ];

            // Si hay calificaciones existentes, cargarlas
            if (isset($calificacionesExistentes[$alumnoId])) {
                foreach ($calificacionesExistentes[$alumnoId] as $calificacion) {
                    $unidad = $calificacion['unidad'];
                    if ($unidad >= 1 && $unidad <= 4) {
                        $calificacionesFormateadas[$alumnoId][$unidad] = [
                            'valor' => floatval($calificacion['calificacion']),
                            'id' => $calificacion['id']
                        ];
                    }
                }
            }
        }

        return [
            'alumnos' => $alumnosArray,
            'calificaciones' => $calificacionesFormateadas
        ];
    }
}
