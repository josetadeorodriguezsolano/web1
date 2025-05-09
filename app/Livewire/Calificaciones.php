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
    public $errores = [];

    // Método para definir las reglas de validación
    protected function rules()
    {
        return [
            'calificaciones.*.*.valor' => ['nullable', 'numeric', 'min:0', 'max:10', 'regex:/^\d(\.\d{0,1})?$/'],
        ];
    }

    // Método para personalizar los mensajes de error
    protected function messages()
    {
        return [
            'calificaciones.*.*.valor.numeric' => 'La calificación debe ser un número',
            'calificaciones.*.*.valor.min' => 'La calificación mínima es 0',
            'calificaciones.*.*.valor.max' => 'La calificación máxima es 10',
            'calificaciones.*.*.valor.regex' => 'La calificación debe tener formato de número con máximo un decimal',
        ];
    }

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
            $this->gruposImpartidos = $maestro->obtenerGruposImpartidos()->toArray();
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
        $this->errores = []; // Limpiar errores al cambiar de grupo

        if (!$valor) {
            $this->materiasImpartidas = [];
            return;
        }

        try {
            $maestro = Auth::user();
            $this->materiasImpartidas = $maestro->obtenerMateriasImpartidasEnGrupo($valor);
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
        $this->errores = []; // Limpiar errores al cambiar de materia

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
        $this->errores = []; // Limpiar errores al cargar alumnos

        try {
            // Obtener los alumnos y sus calificaciones usando el método del modelo
            $grupo = Grupo::find($this->grupoSeleccionado);

            if (!$grupo) {
                $this->cargando = false;
                $this->mensaje = 'No se encontró el grupo seleccionado';
                $this->tipoMensaje = 'error';
                return;
            }

            $resultado = $grupo->obtenerAlumnosConCalificaciones($this->materiaSeleccionada);

            $this->alumnos = $resultado['alumnos'];
            $this->calificaciones = $resultado['calificaciones'];
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

    // Método para validar una calificación individual
    public function validarCalificacion($valor, $alumnoId, $unidad)
    {
        // Limpiar error específico
        unset($this->errores["alumno_{$alumnoId}_unidad_{$unidad}"]);

        // Si el valor es una cadena vacía, establecerlo como null
        if ($valor === '') {
            return null;
        }

        // Si es null, es válido (calificación no asignada)
        if ($valor === null) {
            return null;
        }

        // Convertir a número para asegurar el tipo correcto
        $valor = floatval($valor);

        // Validar rango
        if ($valor < 0 || $valor > 10) {
            $this->errores["alumno_{$alumnoId}_unidad_{$unidad}"] = 'La calificación debe ser entre 0 y 10';
            return false;
        }

        // Validar formato (máximo un decimal)
        if (!preg_match('/^(10|[0-9])(\.\d{0,1})?$/', (string)$valor)) {
            $this->errores["alumno_{$alumnoId}_unidad_{$unidad}"] = 'La calificación debe tener formato X.X (máximo un decimal)';
            return false;
        }
        return $valor;
    }

    public function actualizarCalificacion($alumnoId, $unidad, $valor)
    {
        // Limpiar mensajes previos
        $this->mensaje = '';
        $this->tipoMensaje = '';

        // Validar la calificación
        $valorValidado = $this->validarCalificacion($valor, $alumnoId, $unidad);

        // Si hay error de validación, detener
        if ($valorValidado === false) {
            $this->mensaje = 'Hay errores de validación';
            $this->tipoMensaje = 'error';
            return;
        }

        // Usar el valor validado (puede ser null)
        $valor = $valorValidado;

        // Guardar la calificación en la base de datos
        try {
            // Obtener el ID de la calificación si existe
            $calificacionId = isset($this->calificaciones[$alumnoId][$unidad]['id']) ?
                $this->calificaciones[$alumnoId][$unidad]['id'] : null;

            // Usar el método del modelo para actualizar o crear la calificación
            $calificacion = Calificacion::actualizarCalificacion(
                $alumnoId,
                $this->materiaSeleccionada,
                $unidad,
                $valor,
                $calificacionId
            );

            // Actualizar el ID en el arreglo local si es una nueva calificación
            if (!$calificacionId) {
                $this->calificaciones[$alumnoId][$unidad]['id'] = $calificacion->id;
            }

            $this->calificaciones[$alumnoId][$unidad]['valor'] = $valor;
            // MENSAJE ELIMINADO
            //$this->mensaje = 'Calificación guardada correctamente';
            //$this->tipoMensaje = 'success';

        } catch (\Exception $e) {
            $this->errores["alumno_{$alumnoId}_unidad_{$unidad}"] = 'Error al guardar';
            $this->mensaje = 'Error al guardar la calificación: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    public function guardarTodasLasCalificaciones()
    {
        $this->cargando = true;
        $hayErrores = false;
        $this->errores = [];

        // Validar todas las calificaciones primero
        foreach ($this->calificaciones as $alumnoId => $unidades) {
            foreach ($unidades as $unidad => $datos) {
                if ($datos['valor'] !== null && $datos['valor'] !== '') {
                    $valorValidado = $this->validarCalificacion($datos['valor'], $alumnoId, $unidad);
                    if ($valorValidado === false) {
                        $hayErrores = true;
                    } else {
                        // Actualizar con el valor validado
                        $this->calificaciones[$alumnoId][$unidad]['valor'] = $valorValidado;
                    }
                }
            }
        }

        // Si hay errores, no continuar
        if ($hayErrores) {
            $this->cargando = false;
            $this->mensaje = 'Hay calificaciones con formato incorrecto. Por favor, corríjalas antes de guardar.';
            $this->tipoMensaje = 'error';
            return;
        }

        // Guardar todas las calificaciones
        $errorAlGuardar = false;
        foreach ($this->calificaciones as $alumnoId => $unidades) {
            foreach ($unidades as $unidad => $datos) {
                if ($datos['valor'] !== null && $datos['valor'] !== '') {
                    try {
                        // Obtener el ID de la calificación si existe
                        $calificacionId = isset($datos['id']) ? $datos['id'] : null;

                        // Usar el método del modelo para actualizar o crear la calificación
                        $calificacion = Calificacion::actualizarCalificacion(
                            $alumnoId,
                            $this->materiaSeleccionada,
                            $unidad,
                            $datos['valor'],
                            $calificacionId
                        );

                        // Actualizar el ID en el arreglo local si es una nueva calificación
                        if (!$calificacionId) {
                            $this->calificaciones[$alumnoId][$unidad]['id'] = $calificacion->id;
                        }
                    } catch (\Exception $e) {
                        $errorAlGuardar = true;
                        $this->errores["alumno_{$alumnoId}_unidad_{$unidad}"] = 'Error al guardar: ' . $e->getMessage();
                    }
                }
            }
        }

        $this->cargando = false;

        if ($errorAlGuardar) {
            $this->mensaje = 'Se guardaron algunas calificaciones, pero hubo errores';
            $this->tipoMensaje = 'warning';
        } else {
            $this->mensaje = 'Todas las calificaciones se guardaron correctamente';
            $this->tipoMensaje = 'success';
        }
    }
}
