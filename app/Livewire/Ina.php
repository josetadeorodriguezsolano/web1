<?php

namespace App\Livewire;
use App\Models\Imparte;
use Barryvdh\DomPDF\Facade\Pdf; //
use Livewire\Component;
use App\Models\Inasistencia;
use App\Models\Maestro;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Horario;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Ina extends Component
{
   public $inasistencias;

public $mostrarFormulario = false;
public $seleccionado = -1;

public $maestro_id;
  public $horario;
    public $horarios = [];
    public $impartes;      // Para la lista de impartes (maestro, materia y grupo)
   public $imparte; 
public $maestros;
public $materias;
public $grupos;
public $fecha;
public $fechaStr;
public $diaNumero;

public $inasistencia = ['fecha' => 1];

public function obtenerDiaSemanaAjustado()
{
    $fechaStr = $this->inasistencia['fecha'] ?? null;

    if (!$fechaStr) {
        $this->diaNumero = 'Lunes'; // Valor por defecto
        return;
    }

    $fecha = \DateTime::createFromFormat('Y-m-d', $fechaStr);

    if (!$fecha) {
        $this->diaNumero = 'Lunes'; // Valor por defecto si la fecha es inválida
        return;
    }

    $dias = [
        'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'
    ];

    $diaIndex = (int)$fecha->format('w'); // 0 (domingo) a 6 (sábado)

    // Ajustar sábado a viernes y domingo a lunes, como tú pediste
    if ($diaIndex === 6) {
        $this->diaNumero = 'Viernes'; // Sábado → Viernes
    } elseif ($diaIndex === 0) {
        $this->diaNumero = 'Lunes'; // Domingo → Lunes
    } else {
        $this->diaNumero = $dias[$diaIndex];
    }
}



protected function rules()
{
    return [
        'inasistencia.imparte_id'    => 'required|exists:imparte,id',
        'inasistencia.horario_id'    => 'required|exists:horarios,id',
        'inasistencia.justificacion' => 'nullable|string|max:1000',
        'inasistencia.fecha'         => 'required|date',
    ];
}



protected function messages()
{
    return [
        'inasistencia.imparte_id.required'    => 'Seleccione una asignación de maestro, materia y grupo.',
        'inasistencia.imparte_id.exists'      => 'La asignación seleccionada no es válida.',
        'inasistencia.horario_id.required'    => 'Seleccione un horario.',
        'inasistencia.horario_id.exists'      => 'El horario seleccionado no es válido.',
        'inasistencia.justificacion.string'   => 'La justificación debe ser un texto.',
        'inasistencia.justificacion.max'      => 'La justificación no puede superar los 1000 caracteres.',
        'inasistencia.fecha.required'         => 'Seleccione una fecha.',
        'inasistencia.fecha.date'             => 'La fecha no es válida.',
    ];
}


    public function mount()
    {



 // Cargar los impartes con sus relaciones
        $this->impartes = Imparte::with(['maestro', 'materia', 'grupo'])->get();
       // $this->horarios = Horario::all();
        $this->inasistencias = Inasistencia::with('imparte.maestro', 'imparte.materia', 'imparte.grupo', 'horario')->get();

        $this->inasistencia = [];


        
        $this->recargarDatos();
    }

    public function render()
    {
        return view('livewire.registrar-inasistencias');
    }


public function obtenerHorariosPorMaestro()
{
    $this->obtenerDiaSemanaAjustado();

    if (!$this->maestro_id) {
        $this->horarios = [];
        return;
    }

    $horarios = Horario::join('imparte as i', 'i.id', '=', 'horarios.imparte_id')
        ->where('i.maestro_id', $this->maestro_id)
        ->where('horarios.dia_semana', $this->diaNumero)
        ->select('horarios.*', 'i.maestro_id', 'i.id as imparte_id')
        ->get();

    $fechaSeleccionada = $this->inasistencia['fecha'] ?? null;

    // Marcar horarios ocupados
    foreach ($horarios as $horario) {
        $horario->ocupado = false;

        if ($fechaSeleccionada) {
            $ocupado = Inasistencia::where('fecha', $fechaSeleccionada)
                ->where('horario_id', $horario->id)
                ->where('imparte_id', $horario->imparte_id)
                ->exists();

            $horario->ocupado = $ocupado;
        }
    }

    $this->horarios = $horarios;
}




public function agregar()
{
    $fechaActual = $this->inasistencia['fecha'] ?? now()->toDateString();
    $this->mostrarFormulario = true;
    $this->inasistencia = [
        'imparte_id'      => null,  // ahora representa la relación maestro-materia-grupo
        'horario_id'      => null,
        'justificacion'   => '',
        'fecha'           => null,  // recomendable incluir también 'fecha'
    ];

     $this->maestro_id ;
}



    public function modificar()
    {
        if ($this->seleccionado != -1) {
            $this->mostrarFormulario = true;
            $this->horarios = $this->inasistencias[$this->seleccionado];
        }
    }

    public function GPDF()
    {
        header("Location: /inasistencia");
        exit(); // Siempre es buena práctica terminar el script después de una redirección
    }
  public function eliminar()
{
    try {
        $fecha = $this->inasistencia['fecha'] ?? null;
        $horarioId = $this->inasistencia['horario_id'] ?? null;

        if (!$fecha || !$horarioId) {
            Session::flash('error', 'Fecha u horario no especificado para eliminar la inasistencia.');
            return;
        }

        // Buscar inasistencia con la misma fecha y horario
        $inasistencia = Inasistencia::where('fecha', $fecha)
                                    ->where('horario_id', $horarioId)
                                    ->first();

        if ($inasistencia) {
            $inasistencia->delete();
            $this->seleccionado = -1;
            $this->recargarDatos();
            Session::flash('success', 'Inasistencia eliminada correctamente.');
        } else {
            Session::flash('error', 'No se encontró una inasistencia con esa fecha y horario.');
        }
    } catch (\Exception $e) {
        Log::error('Error al eliminar una inasistencia: ' . $e->getMessage());
        Session::flash('error', 'Ocurrió un error al eliminar la inasistencia.');
    }
}

public function guardar()
{
    try {
        $this->validate();
    } catch (ValidationException $e) {
        Session::flash('error', $e->validator->errors()->first());
        return;
    }

    try {
        $data = [
            'imparte_id'    => $this->inasistencia['imparte_id'],
            'horario_id'    => $this->inasistencia['horario_id'],
            'justificacion' => $this->inasistencia['justificacion'],
            'fecha'         => $this->inasistencia['fecha'],
        ];

        // Buscar si ya existe una inasistencia con la misma fecha y horario
        $inasistenciaExistente = Inasistencia::where('fecha', $data['fecha'])
            ->where('horario_id', $data['horario_id'])
            ->first();

        if ($inasistenciaExistente) {
            // Si existe, se actualiza
            $inasistenciaExistente->update($data);
        } elseif (isset($this->inasistencia['id'])) {
            // Si es una modificación manual por id, se actualiza
            Inasistencia::find($this->inasistencia['id'])->update($data);
        } else {
            // Si no existe nada, se crea uno nuevo
            Inasistencia::create($data);
        }

        $this->mostrarFormulario = false;
        $this->inasistencia = [];
        $this->recargarDatos();
    } catch (\Exception $e) {
        Log::error('Error al guardar la inasistencia: ' . $e->getMessage());
        Session::flash('error', 'Error al guardar la inasistencia: ' . $e->getMessage());
    }
}


  

public function seleccionar($key)
{
    $this->seleccionado = $key;

    if ($this->seleccionado != -1) {
        $this->mostrarFormulario = true;
        $this->horario = $this->horarios[$this->seleccionado];

        // Asignar valores a inasistencia automáticamente
        $this->inasistencia = [
            'imparte_id'    => $this->horario->imparte_id,
            'horario_id'    => $this->horario->id, // o 'horario_id' si ese es el nombre del campo
            'justificacion' => '', // Inicialmente vacío para que el usuario la escriba
            'fecha'         =>  $this->inasistencia['fecha']??null, // O puedes poner null o la fecha que quieras
        ];
    }
}

  public function cancelar()
    {
        $this->mostrarFormulario = false;
        $this->inasistencia = [];
    }
private function recargarDatos()
{
    // Solo se necesita esta línea para obtener las inasistencias con sus relaciones.
    $this->inasistencias = Inasistencia::with('imparte.maestro', 'imparte.materia', 'imparte.grupo', 'horario')->get();

    // Eliminamos la segunda línea redundante.
    // $this->inasistencias = Inasistencia::with(['maestro', 'materia', 'grupo', 'horario'])->get()->toArray();

    $this->maestros = Maestro::all();
    $this->materias = Materia::all();
    $this->grupos = Grupo::all();
    $this->impartes = Imparte::all();

//  $this->horarios = $this->obtenerHorariosPorMaestro($this->maestro_id);
  //  $this->horarios = Horario::all(); // Se asegura que horarios se asignen correctamente
}

}
