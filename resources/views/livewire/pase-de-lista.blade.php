<div>
    Seleccione el grupo:
        <select wire:model.live="selectGrupo" style="color:black">
            @foreach ($gruposImpartidos as $imparte)
                <option value='{{$imparte['grupo']['id']}}'>
                    {{$imparte['materia']['grado']}}{{$imparte['grupo']['letra']}} {{$imparte['materia']['nombre']}}
                </option>
            @endforeach
        </select>
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
