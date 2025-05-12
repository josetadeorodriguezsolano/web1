<?php

namespace App\Livewire;

use App\Models\Alumno;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use PgSql\Lob;

class CatalogoAlumnos extends Component
{
    public $alumno;
    public $alumnos;
    public $seleccionado;
    public $mostrarFormulario;

    protected function rules(){
        return [
            'alumno.curp'=>'required|min:18|max:18',
            'alumno.nombres' => 'required',
            'alumno.matricula' => 'required',
            'alumno.contacto' => 'required',
        ];
    }

    protected function messages(){
        return[
            'alumno.curp.required'=>'El campo CURP es requerido',
            'alumno.curp.min' => 'El campo CURP debe tener 18 caracteres',
            'alumno.curp.max' => 'El campo CURP no puede tener mas de 18 caracteres',
            'alumno.nombres.required' => 'El campo nombre es requerido',
            'alumno.matricula.required' => 'El campo matricula es requerido',
            'alumno.contacto.required' => 'El campo contacto es requerido',
        ];
    }

    public function mount(){
        $this->seleccionado=-1;
        $this->alumno=[];
        $this->alumnos=Alumno::all()->toArray();
        $this->mostrarFormulario=false;

    }

    public function render()
    {
        return view('livewire.catalogo-alumnos');
    }

    public function agregar(){
        $this->mostrarFormulario=true;
        $this->alumno=[];
    }

    public function modificar(){
        if ($this->seleccionado!=-1) {
            $this->mostrarFormulario=true;
            $this->alumno=$this->alumnos[$this->seleccionado];
        }
    }

    public function eliminar(){
        if (isset($this->alumnos[$this->seleccionado])) {
            $alumno=Alumno::find($this->alumnos[$this->seleccionado]['id']);
            $this->seleccionado=1;
            try{
                $alumno->delete();
                $this->alumnos=Alumno::all()->toArray();
            }
            catch (Exception $e){
                Log::error('Error al intentar eliminar a un alumno'.$e->getMessage());
            }
        }
    }

    public function insertar(){
        if (isset($this->alumno['id'])) {
            try{
                $this->validate();
            }
            catch(ValidationException $e){
                Session::flash('error',$e->validator->errors()->first());
                return;
            }
            catch(Exception $e){
                Session::flash('error','Error al actualizar un alumno'.$e->getMessage());
                return;
            }
            $alumno=Alumno::find($this->alumno['id']);
            $alumno->nombres=$this->alumno['nombres'];
            $alumno->apellidos=$this->alumno['apellidos'];
            $alumno->curp=$this->alumno['curp'];
            $alumno->matricula=$this->alumno['matricula'];
            $alumno->contacto=$this->alumno['contacto'];
            $alumno->tutor=$this->alumno['tutor'];
            $alumno->estatus=$this->alumno['estatus'];
            try{
                Gate::authorize('update',$alumno);
            }
            catch(Exception $e){
                Session::flash('error', 'No tiene autorizacion');
                return;
            }
            $alumno->save();
        }
        else{
            try{
                Alumno::create($this->alumno);
            }
            catch(Exception $e){
                Session::flash('error', 'Error al insertar un alumno '.$e->getMessage());
                return;
            }
        }
        $this->alumnos=Alumno::all()->toArray();
        $this->alumno=[];
        $this->mostrarFormulario=false;
    }

    public function cancelar(){
        $this->mostrarFormulario = false;
    }

    public function seleccionar($key){
        $this->seleccionado = $key;
    }

}
