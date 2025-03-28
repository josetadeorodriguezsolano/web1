<div>
    <div>
        <h1>CATALOGO DE MAESTROS</h1>
        <button wire:click='agregar' >Agregar</button>
        <button wire:click='modificar'>Modificar</button>
        <button wire:click='eliminar'>Eliminar</button>
    </div>
    <hr>
    @if ($mostrarFormulario)
    <div>
        <form  >
            <label>Nombres</label><br>
            <input type="text" wire:model='maestro.name'><br>
            <label>Apellidos</label><br>
            <input type="text" wire:model='maestro.apellidos'><br>
            <label>Correo</label><br>
            <input type="email" wire:model='maestro.email'><br>
            <label>CURP</label><br>
            <input type="text" wire:model='maestro.curp'><br>
            <label>Direccion</label><br>
            <input type="text" wire:model='maestro.direccion'><br>
            <label>Telefono</label><br>
            <input type="number" wire:model='maestro.telefono'><br>
            <button wire:click='cancelar'>Cancelar</button>
            <button wire:click='guardar'>Guardar</button>
        </form>
    </div>
    @endif
    <table>
        <thead>
            <tr>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Correo</th>
                <th>CURP</th>
                <th>Direccion</th>
                <th>Telefono</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($maestros as $key => $maestro)
            <tr wire:click='seleccionar({{$key}})' class="{{$seleccionado==$key?'seleccionado':''}}">
                <td>{{$maestro['name']}}</td>
                <td>{{$maestro['apellidos']}}</td>
                <td>{{$maestro['email']}}</td>
                <td>{{$maestro['curp']}}</td>
                <td>{{$maestro['direccion']}}</td>
                <td>{{$maestro['telefono']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
