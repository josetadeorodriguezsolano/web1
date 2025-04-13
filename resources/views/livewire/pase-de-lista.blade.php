<div>
    <input style="color:black" type="text" wire:model.live.debounce.1000ms="buscar" placeholder="Buscar Alumno">
    <br>
    @foreach ($palabras as $palabra)
        {{$palabra}}<br>
    @endforeach
    Seleccione el grupo:
    <select wire:model.live="selectGrupo" style="color:black">
        @foreach ($gruposImpartidos as $imparte)
            <option value='{{$imparte['grupo']['id']}}'>
                {{$imparte['materia']['grado']}}{{$imparte['grupo']['letra']}} {{$imparte['materia']['nombre']}}
            </option>
        @endforeach
    </select>
    <hr>
    Lista de Asistencia:
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Asistio</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grupo['alumnos'] as $key => $alumno)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$alumno['apellidos']}} {{$alumno['nombres']}}</td>
                <td><input wire:click='faltas({{$key}})'
                        wire:model="grupo.alumnos.{{$key}}.falto"
                        type="checkbox">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
