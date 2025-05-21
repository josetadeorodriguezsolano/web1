<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Alumno;
use App\Models\Inscrito;
use App\Models\Grupo;
use App\Models\Calificacion;
use App\Models\Materia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InfoAlumno extends Component
{
    public $alumnoId;
    public $alumno;
    public $inscripcion;
    public $grupo;
    public $calificaciones = [];
    public $materiasGrupo = [];

    // Para el modal de confirmación de eliminación
    public $mostrarModalEliminar = false;

    // Para mensajes
    public $mensaje = '';
    public $tipoMensaje = '';

    // Para edición
    public $editando = false;
    public $alumnoData = [
        'matricula' => '',
        'nombres' => '',
        'apellidos' => '',
        'estatus' => '',
        'curp' => '',
        'contacto' => '',
        'tutor' => ''
    ];

    // Montar el componente con el ID del alumno
    public function mount($alumnoId = null)
    {
        try {
            $this->alumnoId = $alumnoId;

            if ($this->alumnoId) {
                $this->alumno = Alumno::findOrFail($this->alumnoId);

                // Aplica la policy:
                $this->authorize('view', $this->alumno);

                $this->cargarAlumno();
            } else {
                Log::info('No se proporcionó ID de alumno');
            }
        } catch (\Exception $e) {
            Log::error('Error en mount de InfoAlumno: ' . $e->getMessage());
            $this->mensaje = 'Error al cargar la información: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    // Cargar los datos del alumno
    public function cargarAlumno()
    {
        try {
            Log::info('Cargando alumno con ID: ' . $this->alumnoId);

            // Obtener el alumno con sus datos básicos
            $this->alumno = Alumno::findOrFail($this->alumnoId);

            // Obtener la inscripción vigente
            $this->inscripcion = Inscrito::where('alumno_id', $this->alumnoId)
                                        ->where('estatus', 'vigente')
                                        ->with('grupo')
                                        ->first();

            if ($this->inscripcion) {
                $this->grupo = $this->inscripcion->grupo;

                // Preparar datos para el formulario de edición
                $this->alumnoData = [
                    'matricula' => $this->alumno->matricula,
                    'nombres' => $this->alumno->nombres,
                    'apellidos' => $this->alumno->apellidos,
                    'estatus' => $this->alumno->estatus,
                    'curp' => $this->alumno->curp,
                    'contacto' => $this->alumno->contacto,
                    'tutor' => $this->alumno->tutor
                ];

                // Cargar las materias del grupo actual
                $this->cargarMateriasGrupo();

                // Cargar las calificaciones del alumno
                $this->cargarCalificaciones();
            } else {
                Log::info('No se encontró inscripción vigente para el alumno ID: ' . $this->alumnoId);
                $this->alumnoData = [
                    'matricula' => $this->alumno->matricula,
                    'nombres' => $this->alumno->nombres,
                    'apellidos' => $this->alumno->apellidos,
                    'estatus' => $this->alumno->estatus,
                    'curp' => $this->alumno->curp,
                    'contacto' => $this->alumno->contacto,
                    'tutor' => $this->alumno->tutor
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error al cargar los datos del alumno: ' . $e->getMessage());
            $this->mensaje = 'Error al cargar los datos del alumno: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    // Cargar las materias del grupo
    private function cargarMateriasGrupo()
    {
        if (!$this->grupo) {
            return;
        }

        $this->materiasGrupo = $this->grupo->materias->pluck('id')->toArray();
    }

    // Cargar las calificaciones del alumno
    private function cargarCalificaciones()
    {
        if (!$this->alumno || !$this->grupo) {
            return;
        }

        // Obtener todas las materias del grupo
        $materias = $this->grupo->materias;

        // Estructura para almacenar calificaciones por materia
        $calificacionesPorMateria = [];

        foreach ($materias as $materia) {
            // Obtener calificaciones para esta materia
            $calificacionesMateria = Calificacion::where('alumno_id', $this->alumnoId)
                                                ->where('materia_id', $materia->id)
                                                ->get();

            // Inicializar estructura para esta materia
            $datosMateria = [
                'nombre' => $materia->nombre,
                'unidades' => [
                    1 => null,
                    2 => null,
                    3 => null,
                    4 => null
                ],
                'promedio' => null
            ];

            // Totales para calcular promedio
            $totalCalificaciones = 0;
            $cantidadCalificaciones = 0;

            // Llenar calificaciones por unidad
            foreach ($calificacionesMateria as $calificacion) {
                $unidad = (int)$calificacion->unidad;
                if ($unidad >= 1 && $unidad <= 4) {
                    $datosMateria['unidades'][$unidad] = $calificacion->calificacion;
                    $totalCalificaciones += $calificacion->calificacion;
                    $cantidadCalificaciones++;
                }
            }

            // Calcular promedio si hay calificaciones
            if ($cantidadCalificaciones > 0) {
                $datosMateria['promedio'] = round($totalCalificaciones / $cantidadCalificaciones, 1);
            }

            $calificacionesPorMateria[] = $datosMateria;
        }

        $this->calificaciones = $calificacionesPorMateria;
    }

    // Activar edición
    public function activarEdicion()
    {
        $this->editando = true;
    }

    // Cancelar edición
    public function cancelarEdicion()
    {
        $this->editando = false;

        // Restaurar datos originales
        $this->alumnoData = [
            'matricula' => $this->alumno->matricula,
            'nombres' => $this->alumno->nombres,
            'apellidos' => $this->alumno->apellidos,
            'estatus' => $this->alumno->estatus,
            'curp' => $this->alumno->curp,
            'contacto' => $this->alumno->contacto,
            'tutor' => $this->alumno->tutor
        ];
    }

    // Reglas de validación
    protected function rules()
    {
        return [
            'alumnoData.matricula' => [
                'required',
                'string',
                'max:10',
                'min:5',
                'unique:alumnos,matricula,' . $this->alumnoId,
            ],
            'alumnoData.nombres' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo permite letras, espacios y caracteres acentuados
            ],
            'alumnoData.apellidos' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo permite letras, espacios y caracteres acentuados
            ],
            'alumnoData.estatus' => [
                'required',
                'in:vigente,egresado,baja'
            ],
            'alumnoData.curp' => [
                'required',
                'string',
                'size:18',
                'unique:alumnos,curp,' . $this->alumnoId,
                'regex:/^[A-Z0-9]+$/' // Solo mayúsculas y números
            ],
            'alumnoData.contacto' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/' // Solo números
            ],
            'alumnoData.tutor' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo permite letras, espacios y caracteres acentuados
            ],
        ];
    }

    // Guardar cambios
    public function guardarCambios()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Actualizar alumno
            $this->alumno->update([
                'matricula' => strtoupper($this->alumnoData['matricula']),
                'nombres' => ucwords(strtolower($this->alumnoData['nombres'])),
                'apellidos' => ucwords(strtolower($this->alumnoData['apellidos'])),
                'curp' => strtoupper($this->alumnoData['curp']),
                'contacto' => $this->alumnoData['contacto'],
                'tutor' => ucwords(strtolower($this->alumnoData['tutor'])),
                'estatus' => $this->alumnoData['estatus']
            ]);

            // Si el estatus cambió a 'baja', actualizar la inscripción también
            if ($this->alumnoData['estatus'] === 'baja' && $this->inscripcion && $this->inscripcion->estatus === 'vigente') {
                $this->inscripcion->update(['estatus' => 'baja']);
            }

            DB::commit();

            $this->mensaje = 'Alumno actualizado correctamente.';
            $this->tipoMensaje = 'success';
            $this->editando = false;

            // Recargar datos
            $this->cargarAlumno();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->mensaje = 'Error al actualizar alumno: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    // Confirmar eliminación
    public function confirmarEliminacion()
    {
        $this->mostrarModalEliminar = true;
    }

    // Cancelar eliminación
    public function cancelarEliminacion()
    {
        $this->mostrarModalEliminar = false;
    }

    // Eliminar alumno (baja lógica)
    public function eliminarAlumno()
    {
        try {
            DB::beginTransaction();

            // Actualizar estado del alumno a 'baja'
            $this->alumno->update(['estatus' => 'baja']);

            // Actualizar estado de la inscripción vigente
            if ($this->inscripcion) {
                $this->inscripcion->update(['estatus' => 'baja']);
            }

            DB::commit();

            $this->mensaje = 'Alumno dado de baja correctamente.';
            $this->tipoMensaje = 'success';
            $this->mostrarModalEliminar = false;

            // Recargar datos
            $this->cargarAlumno();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->mensaje = 'Error al dar de baja al alumno: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }
    }

    // Calcular promedio general
    public function calcularPromedioGeneral()
    {
        if (empty($this->calificaciones)) {
            return null;
        }

        $totalPromedios = 0;
        $cantidadMaterias = 0;

        foreach ($this->calificaciones as $materia) {
            if ($materia['promedio'] !== null) {
                $totalPromedios += $materia['promedio'];
                $cantidadMaterias++;
            }
        }

        if ($cantidadMaterias === 0) {
            return null;
        }

        return round($totalPromedios / $cantidadMaterias, 1);
    }

    // Método updated para validación en tiempo real y transformaciones
    public function updated($property)
    {
        // Validar solo la propiedad actualizada
        $this->validateOnly($property);

        // Transformaciones específicas
        if ($property === 'alumnoData.curp') {
            $this->alumnoData['curp'] = strtoupper($this->alumnoData['curp']);
        }

        if (in_array($property, ['alumnoData.nombres', 'alumnoData.apellidos', 'alumnoData.tutor'])) {
            $field = explode('.', $property)[1];
            // Eliminar números
            $this->alumnoData[$field] = preg_replace('/[0-9]/', '', $this->alumnoData[$field]);
        }

        if ($property === 'alumnoData.contacto') {
            // Eliminar no números
            $this->alumnoData['contacto'] = preg_replace('/[^0-9]/', '', $this->alumnoData['contacto']);
        }
    }

    public function render()
    {
        return view('livewire.info-alumno', [
            'promedioGeneral' => $this->calcularPromedioGeneral()
        ]);
    }
}