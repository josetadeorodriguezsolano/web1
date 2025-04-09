<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Maestro;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;



class CatalogoMaestros extends Component
{
    public $maestros;
    public $maestro;
    public $mostrarFormulario;
    public $seleccionado;

    protected function rules(){
        return ['maestro.curp'=>'required|min:18|max:18',
                'maestro.name' => 'required'];
    }

    protected function messages(){
        return ['maestro.curp.required' => 'El campo CURP es requerido',
                'maestro.curp.min' => 'El campo CURP debe tener 18 caracteres',
                'maestro.curp.max' => 'El campo CURP no puede tener mas de 18 caracteres',
                'maestro.name.required' => 'El campo nombre es requerido'];
    }

    public function mount(){
        $this->seleccionado = -1;
        $this->mostrarFormulario = false;
        $this->maestro = [];
        $this->maestros = Maestro::all()->toArray();
    }

    public function render()
    {
        return view('livewire.catalogo-maestros');
    }

    public function agregar(){
        $this->mostrarFormulario = true;
        $this->maestro = [];
    }

    public function modificar(){
        if ($this->seleccionado!=-1){
            $this->mostrarFormulario = true;
            $this->maestro = $this->maestros[$this->seleccionado];
        }
    }

    public function eliminar(){
        if (isset($this->maestros[$this->seleccionado]))
        {
            $maestro = Maestro::find($this->maestros[$this->seleccionado]['id']);
            $this->seleccionado = 1;
            try{
            $maestro->delete();
            $this->maestros = Maestro::all()->toArray();
            }catch (Exception $e){
                Log::error('Error al eliminar un maestro '.$e->getMessage());
            }
        }
    }

    public function guardar(){
        if (isset($this->maestro['id'])){
            try{
                $this->validate();
            }
            catch (ValidationException  $e) {
                Session::flash('error',$e->validator->errors()->first());
                return;
            }
            catch (Exception $e){
                Session::flash('error', 'Error al actualizar un maestro '.$e->getMessage());
                return;
            }
            $maestro = Maestro::find($this->maestro['id']);
            $maestro->name = $this->maestro['name'];
            $maestro->apellidos = $this->maestro['apellidos'];
            $maestro->email = $this->maestro['email'];
            $maestro->direccion = $this->maestro['direccion'];
            $maestro->telefono = $this->maestro['telefono'];
            $maestro->curp = $this->maestro['curp'];
            try{
                Gate::authorize('update',$maestro);
            }catch (AuthorizationException $e) {
                Session::flash('error', 'No tiene autorizacion');
                return;
            }
            $maestro->save();
        }
        else{
            $this->maestro['password'] = Hash::make('123');
            try{
                Maestro::create($this->maestro);
            }catch (Exception $e){
                Session::flash('error', 'Error al insertar un maestro '.$e->getMessage());
                return;
            }
        }
        $this->maestros = Maestro::all()->toArray();
        $this->maestro = [];
        $this->mostrarFormulario = false;
    }

    public function cancelar(){
        $this->mostrarFormulario = false;
    }

    public function seleccionar($key){
        $this->seleccionado = $key;
    }
}
