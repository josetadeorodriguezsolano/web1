<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Maestro;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CatalogoMaestros extends Component
{
    public $maestros;
    public $maestro;
    public $mostrarFormulario;
    public $seleccionado;

    protected function rules(){
        return ['maestro.curp'=>'requited|min:18|max:18',
                'maestro.name' => 'required'];
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
            if (!$this->validate())
                return;
            $maestro = Maestro::find($this->maestro['id']);
            $maestro->name = $this->maestro['name'];
            $maestro->apellidos = $this->maestro['apellidos'];
            $maestro->email = $this->maestro['email'];
            $maestro->direccion = $this->maestro['direccion'];
            $maestro->telefono = $this->maestro['telefono'];
            $maestro->curp = $this->maestro['curp'];
            $maestro->save();
        }
        else{
            $this->maestro['password'] = Hash::make('123');
            try{
                Maestro::create($this->maestro);
            }catch (Exception $e){
                Log::error('Error al insertar un maestro '.$e->getMessage());
            }
        }
        $this->maestros = Maestro::all()->toArray();
        $this->maestro = [];
    }

    public function cancelar(){
        $this->mostrarFormulario = false;
    }

    public function seleccionar($key){
        $this->seleccionado = $key;
    }
}
