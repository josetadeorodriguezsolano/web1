<?php

namespace App\Livewire;

use Barryvdh\DomPDF\Facade\Pdf; //
use Livewire\Component;
use App\Models\Inasistencia;
use App\Models\Maestro;
use App\Models\Materia;
use App\Models\Grupo;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Ina extends Component
{
    public $inasistencias;
    public $inasistencia;
    public $mostrarFormulario = false;
    public $seleccionado = -1;

    public $maestros;
    public $materias;
    public $grupos;

    protected function rules()
    {
        return [
            'inasistencia.maestros_id' => 'required|exists:maestros,id',
            'inasistencia.materias_id' => 'required|exists:materias,id',
            'inasistencia.grupos_id' => 'required|exists:grupos,id',
            'inasistencia.horario_falta' => 'required|date',
            'inasistencia.horario_llegada' => 'nullable|date|after_or_equal:inasistencia.horario_falta',
            'inasistencia.justificacion' => 'nullable|string|max:1000',
        ];
    }

    protected function messages()
    {
        return [
            'inasistencia.maestros_id.required' => 'Seleccione un maestro',
            'inasistencia.materias_id.required' => 'Seleccione una materia',
            'inasistencia.grupos_id.required' => 'Seleccione un grupo',
            'inasistencia.horario_falta.required' => 'Debe indicar la fecha y hora de la falta',
            'inasistencia.horario_falta.date' => 'El formato de la falta no es válido',
            'inasistencia.horario_llegada.date' => 'El formato de la llegada no es válido',
            'inasistencia.horario_llegada.after_or_equal' => 'La llegada no puede ser antes de la falta',
            'inasistencia.justificacion.string' => 'La justificación debe ser un texto',
            'inasistencia.justificacion.max' => 'La justificación no puede superar los 1000 caracteres',
        ];
    }

    public function mount()
    {
        $this->inasistencia = [];
        $this->recargarDatos();
    }

    public function render()
    {
        return view('livewire.registrar-inasistencias');
    }

    public function agregar()
    {
        $this->mostrarFormulario = true;
        $this->inasistencia = [
            'maestros_id' => null,
            'materias_id' => null,
            'grupos_id' => null,
            'horario_falta' => null,
            'horario_llegada' => null,
            'justificacion' => '',
        ];
    }

    public function modificar()
    {
        if ($this->seleccionado != -1) {
            $this->mostrarFormulario = true;
            $this->inasistencia = $this->inasistencias[$this->seleccionado];
        }
    }

    public function GPDF()
    {
        header("Location: http://web1.test/inasistencia");
        exit(); // Siempre es buena práctica terminar el script después de una redirección
    }
    public function eliminar()
    {
        if (isset($this->inasistencias[$this->seleccionado])) {
            try {
                $inasistencia = Inasistencia::find($this->inasistencias[$this->seleccionado]['id']);
                $inasistencia->delete();
                $this->seleccionado = -1;
                $this->recargarDatos();
            } catch (\Exception $e) {
                Log::error('Error al eliminar una inasistencia: ' . $e->getMessage());
                Session::flash('error', 'Ocurrió un error al eliminar la inasistencia.');
            }
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
                'maestros_id' => $this->inasistencia['maestros_id'],
                'materias_id' => $this->inasistencia['materias_id'],
                'grupos_id' => $this->inasistencia['grupos_id'],
                'horario_falta' => $this->inasistencia['horario_falta'],
                'horario_llegada' => $this->inasistencia['horario_llegada'],
                'justificacion' => $this->inasistencia['justificacion'],
            ];

            if (isset($this->inasistencia['id'])) {
                Inasistencia::find($this->inasistencia['id'])->update($data);
            } else {
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

    public function cancelar()
    {
        $this->mostrarFormulario = false;
        $this->inasistencia = [];
    }

    public function seleccionar($key)
    {
        $this->seleccionado = $key;
    }

    private function recargarDatos()
    {
        $this->inasistencias = Inasistencia::with(['maestro', 'materia', 'grupo'])->get()->toArray();
        $this->maestros = Maestro::all();
        $this->materias = Materia::all();
        $this->grupos = Grupo::all();
    }
}
