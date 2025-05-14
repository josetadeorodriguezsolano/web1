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
            'alumno.matricula' => ['required', 'string', 'max:10', Rule::unique('alumnos', 'matricula')],
            'alumno.nombres' => 'required|string|max:255',
            'alumno.apellidos' => 'required|string|max:255',
            'alumno.estatus' => 'required|in:vigente,egresado,baja',
            'alumno.curp' => ['required', 'string', 'size:18', Rule::unique('alumnos', 'curp')],
            'alumno.contacto' => 'required|string|max:20',
            'alumno.tutor' => 'required|string|max:255',
            'grupoSeleccionado' => 'required|exists:grupos,id',
        ];
    }

    // Mensajes de error personalizados
    protected function messages()
    {
        return [
            'alumno.matricula.required' => 'La matrícula es obligatoria.',
            'alumno.matricula.unique' => 'Esta matrícula ya está registrada.',
            'alumno.nombres.required' => 'El nombre es obligatorio.',
            'alumno.apellidos.required' => 'Los apellidos son obligatorios.',
            'alumno.curp.required' => 'El CURP es obligatorio.',
            'alumno.curp.size' => 'El CURP debe tener exactamente 18 caracteres.',
            'alumno.curp.unique' => 'Este CURP ya está registrado.',
            'alumno.contacto.required' => 'El teléfono de contacto es obligatorio.',
            'alumno.tutor.required' => 'El nombre del tutor es obligatorio.',
            'grupoSeleccionado.required' => 'Debe seleccionar un grupo.',
            'grupoSeleccionado.exists' => 'El grupo seleccionado no es válido.',
        ];
    }

    // Este método se ejecuta cuando el componente se monta
    public function mount()
    {
        $this->cargarGrupos();
        $this->cargarAlumnosRecientes();
    }

    // Método para cargar los grupos disponibles


// Método para cargar los grupos disponibles - versión dinámica
public function cargarGrupos()
{
    // Determinar el año actual para calcular las generaciones
    $añoActual = date('Y');
    $añoActual--;

    // Crear las combinaciones de grado y generación
    $criteriosFiltrado = [
        ['grado' => '1', 'generacion' => (int)$añoActual],          // 1° año - generación actual
        ['grado' => '2', 'generacion' => (int)$añoActual - 1],      // 2° año - generación del año pasado
        ['grado' => '3', 'generacion' => (int)$añoActual - 2]       // 3° año - generación de hace dos años
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
            'generacion' => $grupo->generacion,
            'descripcion' => "$nombreGrado $grupo->letra (Generación $grupo->generacion)"
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
    }

    // Método para registrar un nuevo alumno
    public function registrarAlumno()
    {
        // Validar los datos del formulario
        $this->validate();

        try {
            // Iniciar una transacción para garantizar que ambas operaciones se completen o fallen juntas
            DB::beginTransaction();

            // Crear el alumno
            $alumnoModel = Alumno::create([
                'matricula' => $this->alumno['matricula'],
                'nombres' => $this->alumno['nombres'],
                'apellidos' => $this->alumno['apellidos'],
                'estatus' => $this->alumno['estatus'],
                'curp' => strtoupper($this->alumno['curp']), // Asegurar que el CURP esté en mayúsculas
                'contacto' => $this->alumno['contacto'],
                'tutor' => $this->alumno['tutor']
            ]);

            // Inscribir el alumno en el grupo seleccionado
            Inscrito::create([
                'alumno_id' => $alumnoModel->id,
                'grupo_id' => $this->grupoSeleccionado,
                'estatus' => 'vigente' // Por defecto, la inscripción está vigente
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

            // Mensaje de error
            session()->flash('message', 'Error al inscribir al alumno: ' . $e->getMessage());
            session()->flash('message-type', 'error');
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
