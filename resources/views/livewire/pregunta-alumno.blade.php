<div>
    <button wire:click='cambiarVista()'>Lista Completa</button><br>
    Alumno:
    <!-- ($grupo['alumnos'] as $key => $alumno)-->


    @if (isset($grupo['alumnos'][$indiceAlumno]))
        @php
            $alumno=$grupo['alumnos'][$indiceAlumno];
        @endphp
        <p>{{$indiceAlumno+1}}</p>
        <p>{{$alumno['apellidos']}} {{$alumno['nombres']}}</p>

    @endif
    <button wire:click='paseLista({{$indiceAlumno}},true)'>Faltó</button>
    <button wire:click='paseLista({{$indiceAlumno}})'>Presente</button>
    <!--<table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Asistio</th>
            </tr>
        </thead>
        <tbody>
            <tr>



            </tr>
        </tbody>
    </table>-->

</div>
