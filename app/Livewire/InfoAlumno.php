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
use Illuminate\Validation\Rule;

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

                // Aplica la policy si existe:
                // $this->authorize('view', $this->alumno);

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
        $this->resetValidation(); // Limpiar errores previos
        $this->mensaje = '';
        $this->tipoMensaje = '';
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

        // Limpiar mensajes y errores
        $this->resetValidation();
        $this->mensaje = '';
        $this->tipoMensaje = '';
    }

    // Reglas de validación CORREGIDAS
    protected function rules()
    {
        return [
            'alumnoData.matricula' => [
                'required',
                'string',
                'max:10',
                'min:5',
                'regex:/^[A-Z0-9]+$/', // Solo mayúsculas y números
                Rule::unique('alumnos', 'matricula')->ignore($this->alumnoId),
            ],
            'alumnoData.nombres' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo letras, espacios y acentos
            ],
            'alumnoData.apellidos' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo letras, espacios y acentos
            ],
            'alumnoData.estatus' => [
                'required',
                'in:vigente,egresado,baja'
            ],
            'alumnoData.curp' => [
                'required',
                'string',
                'size:18',
                'regex:/^[A-Z0-9]+$/', // Solo mayúsculas y números
                Rule::unique('alumnos', 'curp')->ignore($this->alumnoId),
            ],
            'alumnoData.contacto' => [
                'required',
                'string',
                'min:10',
                'max:10',
                'regex:/^[0-9]{10}$/' // Solo números, exactamente 10
            ],
            'alumnoData.tutor' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo letras, espacios y acentos
            ],
        ];
    }

    // Mensajes de validación CORREGIDOS
    protected function messages()
    {
        return [
            'alumnoData.matricula.required' => 'La matrícula es obligatoria.',
            'alumnoData.matricula.min' => 'La matrícula debe tener al menos 5 caracteres.',
            'alumnoData.matricula.max' => 'La matrícula no puede tener más de 10 caracteres.',
            'alumnoData.matricula.regex' => 'La matrícula solo debe contener letras mayúsculas y números.',
            'alumnoData.matricula.unique' => 'Esta matrícula ya está registrada.',

            'alumnoData.nombres.required' => 'El nombre es obligatorio.',
            'alumnoData.nombres.min' => 'El nombre debe tener al menos 2 caracteres.',
            'alumnoData.nombres.max' => 'El nombre no puede tener más de 255 caracteres.',
            'alumnoData.nombres.regex' => 'El nombre solo debe contener letras, espacios y acentos.',

            'alumnoData.apellidos.required' => 'Los apellidos son obligatorios.',
            'alumnoData.apellidos.min' => 'Los apellidos deben tener al menos 2 caracteres.',
            'alumnoData.apellidos.max' => 'Los apellidos no pueden tener más de 255 caracteres.',
            'alumnoData.apellidos.regex' => 'Los apellidos solo deben contener letras, espacios y acentos.',

            'alumnoData.estatus.required' => 'El estatus es obligatorio.',
            'alumnoData.estatus.in' => 'El estatus debe ser: vigente, egresado o baja.',

            'alumnoData.curp.required' => 'El CURP es obligatorio.',
            'alumnoData.curp.size' => 'El CURP debe tener exactamente 18 caracteres.',
            'alumnoData.curp.regex' => 'El CURP debe contener solo letras mayúsculas y números.',
            'alumnoData.curp.unique' => 'Este CURP ya está registrado.',

            'alumnoData.contacto.required' => 'El teléfono de contacto es obligatorio.',
            'alumnoData.contacto.min' => 'El teléfono debe tener exactamente 10 dígitos.',
            'alumnoData.contacto.max' => 'El teléfono debe tener exactamente 10 dígitos.',
            'alumnoData.contacto.regex' => 'El teléfono solo debe contener números (10 dígitos).',

            'alumnoData.tutor.required' => 'El nombre del tutor es obligatorio.',
            'alumnoData.tutor.min' => 'El nombre del tutor debe tener al menos 2 caracteres.',
            'alumnoData.tutor.max' => 'El nombre del tutor no puede tener más de 255 caracteres.',
            'alumnoData.tutor.regex' => 'El nombre del tutor solo debe contener letras, espacios y acentos.',
        ];
    }

    // Guardar cambios
    public function guardarCambios()
    {
        // Limpiar mensajes previos
        $this->mensaje = '';
        $this->tipoMensaje = '';

        // Validar antes de guardar
        $this->validate();

        try {
            DB::beginTransaction();

            // Actualizar alumno
            $this->alumno->update([
                'matricula' => strtoupper(trim($this->alumnoData['matricula'])),
                'nombres' => ucwords(strtolower(trim($this->alumnoData['nombres']))),
                'apellidos' => ucwords(strtolower(trim($this->alumnoData['apellidos']))),
                'curp' => strtoupper(trim($this->alumnoData['curp'])),
                'contacto' => trim($this->alumnoData['contacto']),
                'tutor' => ucwords(strtolower(trim($this->alumnoData['tutor']))),
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
            $this->mostrarModalEliminar = false;
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

    // Método updated CORREGIDO
    public function updated($property)
    {
        // Solo procesar si estamos en modo edición
        if (!$this->editando) {
            return;
        }

        // PRIMERO aplicar transformaciones de formato
        if ($property === 'alumnoData.curp') {
            $this->alumnoData['curp'] = strtoupper($this->alumnoData['curp']);
        }

        if ($property === 'alumnoData.matricula') {
            $this->alumnoData['matricula'] = strtoupper($this->alumnoData['matricula']);
        }

        // Limpiar números de campos de texto
        if (in_array($property, ['alumnoData.nombres', 'alumnoData.apellidos', 'alumnoData.tutor'])) {
            $field = explode('.', $property)[1];
            $this->alumnoData[$field] = preg_replace('/[0-9]/', '', $this->alumnoData[$field]);
        }

        // Limpiar todo excepto números del campo contacto
        if ($property === 'alumnoData.contacto') {
            $this->alumnoData['contacto'] = preg_replace('/[^0-9]/', '', $this->alumnoData['contacto']);
            // Limitar a 10 dígitos máximo
            if (strlen($this->alumnoData['contacto']) > 10) {
                $this->alumnoData['contacto'] = substr($this->alumnoData['contacto'], 0, 10);
            }
        }

        // DESPUÉS validar con el valor ya transformado
        $this->validateOnly($property);
    }

    public function render()
    {
        return view('livewire.info-alumno', [
            'promedioGeneral' => $this->calcularPromedioGeneral()
        ]);
    }
}
