<div>
    Seleccione el grupo:
        <select name="selectGrupo">
            @foreach ($gruposImpartidos as $gpo)
                <option value='{{$gpo->id}}'>{{$gpo->materia->grado}}{{$gpo->letra}} {{$gpo->materia->nombre}}</option>
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
            @foreach ($grupo->alumnos as $key => $alumno)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$alumno->apellidos}} {{$alumno->nombres}}</td>
                <td><input type="checkbox" name='alumno1' alumno_id='{{$alumno->id}}'
                @if($alumno->falto(date('Y-m-d'))!=null)
                    checked
                @endif
                ></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
