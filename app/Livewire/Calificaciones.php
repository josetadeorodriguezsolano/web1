<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Imparte;
use App\Models\Calificacion;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Alumno;
use Illuminate\Support\Facades\Auth;

class Calificaciones extends Component
{
    public $gruposImpartidos = [];
    public $materiasImpartidas = [];
    public $alumnos = [];
    public $calificaciones = [];

    public $grupoSeleccionado = null;
    public $materiaSeleccionada = null;
    public $imparteSeleccionado = null;

    // Propiedades para controlar la UI
    public $cargando = false;
    public $mensaje = '';
    public $tipoMensaje = '';

    public function mount()
    {
        // Cargar los grupos que imparte el maestro
        $this->cargarGruposImpartidos();
    }

    public function render()
    {
        return view('livewire.calificaciones');
    }

    public function cargarGruposImpartidos()
    {
        try {
            $maestro = Auth::user();
            $año = date('Y');

            // Obtener grupos únicos que imparte el maestro
            $imparte = $maestro->imparte()
                ->with(['grupo', 'materia'])
                ->whereHas('grupo', function($query) use ($año) {
                    $query->where('generacion', '<=', $año);
                })
                ->get();

            // Agrupar por grupo para el dropdown
            $gruposUnicos = $imparte->pluck('grupo')->unique('id')->values();

            $this->gruposImpartidos = $gruposUnicos->map(function($grupo) {
                return [
                    'id' => $grupo->id,
                    'nombre' => $grupo->grado . '°' . $grupo->letra . ' (Gen. ' . $grupo->generacion . ')'
                ];
            })->toArray();
        } catch (\Exception $e) {
            // Para desarrollo, muestra detalles de error
            $this->mensaje = 'Error al cargar grupos: ' . $e->getMessage();
            $this->tipoMensaje = 'error';

            // Valores de prueba en caso de error
            $this->gruposImpartidos = [
                ['id' => 1, 'nombre' => '1°A (Gen. 2024)'],
                ['id' => 2, 'nombre' => '2°B (Gen. 2023)'],
                ['id' => 3, 'nombre' => '3°C (Gen. 2022)']
            ];
        }
    }

    public function updatedGrupoSeleccionado($valor)
    {
        $this->alumnos = [];
        $this->calificaciones = [];
        $this->materiaSeleccionada = null;
        $this->imparteSeleccionado = null;

        if (!$valor) {
            $this->materiasImpartidas = [];
            return;
        }

        try {
            $maestro = Auth::user();

            // Obtener las materias que imparte el maestro en este grupo
            $imparte = $maestro->imparte()
                ->with(['materia'])
                ->where('grupo_id', $valor)
                ->get();

            $this->materiasImpartidas = $imparte->map(function($relacion) {
                return [
                    'id' => $relacion->materia_id,
                    'imparte_id' => $relacion->id,
                    'nombre' => $relacion->materia->nombre . ' (' . $relacion->materia->clave . ')'
                ];
            })->toArray();
        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar materias: ' . $e->getMessage();
            $this->tipoMensaje = 'error';

            // Valores de prueba en caso de error
            $this->materiasImpartidas = [
                ['id' => 1, 'imparte_id' => 1, 'nombre' => 'Matemáticas (MAT101)'],
                ['id' => 2, 'imparte_id' => 2, 'nombre' => 'Español (ESP102)']
            ];
        }
    }

    public function updatedMateriaSeleccionada($valor)
    {
        if (!$valor || !$this->grupoSeleccionado) {
            $this->alumnos = [];
            $this->calificaciones = [];
            return;
        }

        // Buscar el imparte_id correspondiente
        foreach ($this->materiasImpartidas as $materia) {
            if ($materia['id'] == $valor) {
                $this->imparteSeleccionado = $materia['imparte_id'];
                break;
            }
        }

        $this->cargarAlumnosConCalificaciones();
    }

    public function cargarAlumnosConCalificaciones()
    {
        $this->cargando = true;

        try {
            // Obtener los alumnos del grupo seleccionado
            $grupo = Grupo::with(['alumnos' => function($query) {
                $query->orderBy('apellidos');
            }])->find($this->grupoSeleccionado);

            if (!$grupo) {
                $this->cargando = false;
                $this->mensaje = 'No se encontró el grupo seleccionado';
                $this->tipoMensaje = 'error';
                return;
            }

            $this->alumnos = $grupo->alumnos->toArray();

            // Obtener las calificaciones existentes para estos alumnos
            // Modificado para no usar imparte_id
            $calificacionesExistentes = Calificacion::where('materia_id', $this->materiaSeleccionada)
                ->whereIn('alumno_id', $grupo->alumnos->pluck('id'))
                ->get()
                ->groupBy('alumno_id')
                ->toArray();

            // Inicializar la estructura de calificaciones
            $this->calificaciones = [];

            foreach ($this->alumnos as $alumno) {
                $alumnoId = $alumno['id'];

                // Inicializar las calificaciones para las 4 unidades
                $this->calificaciones[$alumnoId] = [
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
                            $this->calificaciones[$alumnoId][$unidad] = [
                                'valor' => floatval($calificacion['calificacion']),
                                'id' => $calificacion['id']
                            ];
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->mensaje = 'Error al cargar alumnos: ' . $e->getMessage();
            $this->tipoMensaje = 'error';

            // Datos de prueba en caso de error
            $this->alumnos = [
                ['id' => 1, 'nombres' => 'Juan', 'apellidos' => 'Pérez', 'matricula' => 'A12345'],
                ['id' => 2, 'nombres' => 'María', 'apellidos' => 'González', 'matricula' => 'A12346']
            ];

            foreach ($this->alumnos as $alumno) {
                $this->calificaciones[$alumno['id']] = [
                    1 => ['valor' => 8.5, 'id' => null],
                    2 => ['valor' => 9.0, 'id' => null],
                    3 => ['valor' => null, 'id' => null],
                    4 => ['valor' => null, 'id' => null],
                ];
            }
        }

        $this->cargando = false;
    }

    public function calcularPromedioAlumno($alumnoId)
    {
        if (!isset($this->calificaciones[$alumnoId])) {
            return null;
        }

        $total = 0;
        $cantidad = 0;

        foreach ($this->calificaciones[$alumnoId] as $unidad => $datos) {
            if ($datos['valor'] !== null) {
                $total += $datos['valor'];
                $cantidad++;
            }
        }

        if ($cantidad == 0) {
            return null;
        }

        return round($total / $cantidad, 1);
    }

    public function actualizarCalificacion($alumnoId, $unidad, $valor)
    {
        // Validar que el valor esté entre 0 y 10
        if ($valor !== null && ($valor < 0 || $valor > 10)) {
            $this->mensaje = 'La calificación debe ser entre 0 y 10';
            $this->tipoMensaje = 'error';
            return;
        }

        // Guardar la calificación en la base de datos
        try {
            // Modificado para no usar grupo_id e imparte_id
            $datosCalificacion = [
                'alumno_id' => $alumnoId,
                'materia_id' => $this->materiaSeleccionada,
                'unidad' => $unidad,
                'calificacion' => $valor ?? 0
            ];

            // Si ya existe un ID, actualizar, si no, crear
            if (isset($this->calificaciones[$alumnoId][$unidad]['id']) &&
                $this->calificaciones[$alumnoId][$unidad]['id']) {
                $calificacion = Calificacion::find($this->calificaciones[$alumnoId][$unidad]['id']);
                if ($calificacion) {
                    $calificacion->update($datosCalificacion);
                }
            } else {
                $calificacion = Calificacion::create($datosCalificacion);
                $this->calificaciones[$alumnoId][$unidad]['id'] = $calificacion->id;
            }

            $this->calificaciones[$alumnoId][$unidad]['valor'] = $valor;
            $this->mensaje = 'Calificación guardada correctamente';
            $this->tipoMensaje = 'success';

        } catch (\Exception $e) {
            $this->mensaje = 'Error al guardar la calificación: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function guardarTodasLasCalificaciones()
    {
        $this->cargando = true;
        $hayErrores = false;

        foreach ($this->calificaciones as $alumnoId => $unidades) {
            foreach ($unidades as $unidad => $datos) {
                if ($datos['valor'] !== null) {
                    try {
                        $this->actualizarCalificacion($alumnoId, $unidad, $datos['valor']);
                    } catch (\Exception $e) {
                        $hayErrores = true;
                    }
                }
            }
        }

        $this->cargando = false;

        if ($hayErrores) {
            $this->mensaje = 'Se guardaron algunas calificaciones, pero hubo errores';
            $this->tipoMensaje = 'warning';
        } else {
            $this->mensaje = 'Todas las calificaciones se guardaron correctamente';
            $this->tipoMensaje = 'success';
        }
    }
}
