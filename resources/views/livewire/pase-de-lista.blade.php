<div>
    Seleccione el grupo:
        <select name="selectGrupo">
            @foreach ($gruposImpartidos as $gpo)
                <option value='{{$gpo->id}}'>{{$gpo->materia->grado}}{{$gpo->letra}} {{$gpo->materia->nombre}}</option>
            @endforeach
        </select>
    <br><button wire:click='cambiarVista(false)'>Pasar Lista</button><br>
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
                <td>{{$grupo['alumnos'][$key]['falto']}}
                    <input wire:click='faltas({{$key}})' wire:model="grupo->alumnos->{{$key}}->falto" type="checkbox"></td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
