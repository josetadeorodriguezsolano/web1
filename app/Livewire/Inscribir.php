<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Inscrito;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Inscribir extends Component
{
    public $alumno = [
        'matricula' => '',
        'nombres' => '',
        'apellidos' => '',
        'estatus' => 'vigente',  // Valor por defecto
        'curp' => '',
        'contacto' => '',
        'tutor' => ''
    ];

    public $grupoSeleccionado = '';
    public $grupos = [];
    public $alumnosRecientes = [];

    // Reglas de validación
    protected function rules()
    {
        return [
            'alumno.matricula' => [
                'required',
                'string',
                'max:10',
                'min:5',
                'unique:alumnos,matricula'
            ],
            'alumno.nombres' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo permite letras, espacios y caracteres acentuados
            ],
            'alumno.apellidos' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo permite letras, espacios y caracteres acentuados
            ],
            'alumno.estatus' => [
                'required',
                'in:vigente,egresado,baja'
            ],
            'alumno.curp' => [
                'required',
                'string',
                'size:18',
                'unique:alumnos,curp',
                'regex:/^[A-Z0-9]+$/' // Solo mayúsculas y números
            ],
            'alumno.contacto' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/' // Solo números
            ],
            'alumno.tutor' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/' // Solo permite letras, espacios y caracteres acentuados
            ],
            'grupoSeleccionado' => [
                'required',
                'exists:grupos,id'
            ],
        ];
    }

    // Mensajes de error personalizados
    protected function messages()
    {
        return [
            'alumno.matricula.required' => 'La matrícula es obligatoria.',
            'alumno.matricula.max' => 'La matrícula no debe exceder los 10 caracteres.',
            'alumno.matricula.min' => 'La matrícula debe tener al menos 5 caracteres.',
            'alumno.matricula.unique' => 'Esta matrícula ya está registrada.',

            'alumno.nombres.required' => 'El nombre es obligatorio.',
            'alumno.nombres.regex' => 'El nombre solo debe contener letras y espacios.',

            'alumno.apellidos.required' => 'Los apellidos son obligatorios.',
            'alumno.apellidos.regex' => 'Los apellidos solo deben contener letras y espacios.',

            'alumno.estatus.required' => 'El estatus es obligatorio.',
            'alumno.estatus.in' => 'El estatus debe ser vigente, egresado o baja.',

            'alumno.curp.required' => 'El CURP es obligatorio.',
            'alumno.curp.size' => 'El CURP debe tener exactamente 18 caracteres.',
            'alumno.curp.unique' => 'Este CURP ya está registrado.',
            'alumno.curp.regex' => 'El CURP solo debe contener letras mayúsculas y números.',

            'alumno.contacto.required' => 'El teléfono de contacto es obligatorio.',
            'alumno.contacto.regex' => 'El teléfono de contacto solo debe contener números.',

            'alumno.tutor.required' => 'El nombre del tutor es obligatorio.',
            'alumno.tutor.regex' => 'El nombre del tutor solo debe contener letras y espacios.',

            'grupoSeleccionado.required' => 'Debe seleccionar un grupo.',
            'grupoSeleccionado.exists' => 'El grupo seleccionado no es válido.',
        ];
    }

    // Método que se ejecuta al montar el componente
    public function mount()
    {
        $this->cargarGrupos();
        $this->cargarAlumnosRecientes();
    }

    // Método para validar los campos en tiempo real
    public function updated($propertyName)
    {
        // Validar solo el campo que acaba de ser actualizado
        $this->validateOnly($propertyName);

        // Si se actualizó el CURP, convertirlo a mayúsculas
        if ($propertyName === 'alumno.curp') {
            $this->alumno['curp'] = strtoupper($this->alumno['curp']);
        }

        // Si se actualizaron campos de texto que no deben tener números
        if (in_array($propertyName, ['alumno.nombres', 'alumno.apellidos', 'alumno.tutor'])) {
            // Eliminar cualquier número
            $fieldValue = $this->alumno[explode('.', $propertyName)[1]];
            $this->alumno[explode('.', $propertyName)[1]] = preg_replace('/[0-9]/', '', $fieldValue);
        }

        // Si se actualizó el campo de contacto (teléfono)
        if ($propertyName === 'alumno.contacto') {
            // Eliminar cualquier caracter que no sea número
            $this->alumno['contacto'] = preg_replace('/[^0-9]/', '', $this->alumno['contacto']);
        }
    }

    // Método para cargar los grupos disponibles
    public function cargarGrupos()
    {
        // Obtener solo las combinaciones específicas de grado y generación
        $criteriosFiltrado = [
            ['grado' => '1', 'generacion' => 2024], // Primer año - generación 2024
            ['grado' => '2', 'generacion' => 2023], // Segundo año - generación 2023
            ['grado' => '3', 'generacion' => 2022]  // Tercer año - generación 2022
        ];

        // Inicializar una colección vacía para los resultados
        $gruposColeccion = collect();

        // Obtener grupos para cada combinación de criterios
        foreach ($criteriosFiltrado as $criterio) {
            $grupos = Grupo::where('grado', $criterio['grado'])
                           ->where('generacion', $criterio['generacion'])
                           ->orderBy('letra')
                           ->get();

            $gruposColeccion = $gruposColeccion->concat($grupos);
        }

        // Transformar los resultados al formato necesario
        $this->grupos = $gruposColeccion->map(function ($grupo) {
            // Determinar el nombre del grado para la descripción
            $nombreGrado = '';
            switch ($grupo->grado) {
                case '1':
                    $nombreGrado = 'Primero';
                    break;
                case '2':
                    $nombreGrado = 'Segundo';
                    break;
                case '3':
                    $nombreGrado = 'Tercero';
                    break;
            }

            return [
                'id' => $grupo->id,
                'grado' => $grupo->grado,
                'letra' => $grupo->letra,
                'generacion' => $grupo->generacion
            ];
        })
        ->sortBy([
            ['grado', 'asc'],
            ['letra', 'asc']
        ])
        ->values()
        ->toArray();
    }

    // Método para cargar alumnos inscritos recientemente
    public function cargarAlumnosRecientes()
    {
        try {
            // Obtener los últimos 5 alumnos inscritos con información de su grupo
            $alumnosRecientes = Alumno::join('inscritos', 'alumnos.id', '=', 'inscritos.alumno_id')
                ->join('grupos', 'inscritos.grupo_id', '=', 'grupos.id')
                ->select('alumnos.*',
                    DB::raw("CONCAT(grupos.grado, '°', grupos.letra, ' (Gen. ', grupos.generacion, ')') as grupo"))
                ->orderBy('alumnos.created_at', 'desc')
                ->limit(5)
                ->get();

            $this->alumnosRecientes = $alumnosRecientes->map(function ($alumno) {
                return [
                    'matricula' => $alumno->matricula,
                    'nombres' => $alumno->nombres,
                    'apellidos' => $alumno->apellidos,
                    'tutor' => $alumno->tutor,
                    'grupo' => $alumno->grupo
                ];
            })->toArray();
        } catch (\Exception $e) {
            // Si hay un error al cargar los alumnos recientes, simplemente inicializar como array vacío
            $this->alumnosRecientes = [];

            // Registrar el error para depuración
            \Log::error('Error al cargar alumnos recientes: ' . $e->getMessage());
        }
    }

    // Método para registrar un nuevo alumno
    public function registrarAlumno()
    {
        // Validar todos los datos del formulario antes de proceder
        $this->validate();

        try {
            // Iniciar una transacción para garantizar integridad de datos
            DB::beginTransaction();

            // Crear el alumno
            $alumnoModel = Alumno::create([
                'matricula' => strtoupper($this->alumno['matricula']),
                'nombres' => ucwords(strtolower($this->alumno['nombres'])),
                'apellidos' => ucwords(strtolower($this->alumno['apellidos'])),
                'estatus' => $this->alumno['estatus'],
                'curp' => strtoupper($this->alumno['curp']),
                'contacto' => $this->alumno['contacto'],
                'tutor' => ucwords(strtolower($this->alumno['tutor']))
            ]);

            // Inscribir el alumno en el grupo seleccionado
            Inscrito::create([
                'alumno_id' => $alumnoModel->id,
                'grupo_id' => $this->grupoSeleccionado,
                'estatus' => 'vigente' // Estatus por defecto para la inscripción
            ]);

            // Confirmar la transacción
            DB::commit();

            // Mensaje de éxito
            session()->flash('message', '¡Alumno inscrito correctamente!');
            session()->flash('message-type', 'success');

            // Limpiar el formulario
            $this->limpiarFormulario();

            // Recargar la lista de alumnos recientes
            $this->cargarAlumnosRecientes();

        } catch (\Exception $e) {
            // Si hay un error, revertir la transacción
            DB::rollBack();

            // Mensaje de error detallado
            session()->flash('message', 'Error al inscribir al alumno: ' . $e->getMessage());
            session()->flash('message-type', 'error');

            // Log del error para depuración
            \Log::error('Error al inscribir alumno: ' . $e->getMessage());
        }
    }

    // Método para limpiar el formulario
    public function limpiarFormulario()
    {
        $this->alumno = [
            'matricula' => '',
            'nombres' => '',
            'apellidos' => '',
            'estatus' => 'vigente',  // Valor por defecto
            'curp' => '',
            'contacto' => '',
            'tutor' => ''
        ];
        $this->grupoSeleccionado = '';
        $this->resetValidation(); // Limpiar mensajes de error
    }

    // Método de renderizado para mostrar la vista
    public function render()
    {
        return view('livewire.inscribir');
    }
}
