<body>
    <div class="header">
        <h2>{{ $titulo }}</h2>

        @if ($vista === 'alumnos')
            <p><strong>Materia:</strong> {{ $materia ?? 'Todas' }}</p>
            <p><strong>Profesor:</strong> {{ $maestro ?? '*' }}</p>
            <p><strong>Generación:</strong> {{ $generacion ?? 'Todas' }}</p>
            <p><strong>Grado:</strong> {{ $grado ?? 'Todos' }}</p>
            <p><strong>Grupo:</strong> {{ $letra ?? 'Todos' }}</p>
        @endif
    </div>

    @if ($vista === 'maestros')
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>CURP</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th>Telefono</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultados as $maestro)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $maestro->curp }}</td>
                        <td>{{ $maestro->apellidos }}</td>
                        <td>{{ $maestro->name }}</td>
                        <td>{{ $maestro->telefono }}</td>
                        <td>{{ $maestro->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($vista === 'maestro_por_materias')
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre Completo</th>
                    <th>Materia</th>
                    <th>Grupo</th>
                    <th>Día</th>
                    <th>Hora</th>
                    <th>Hora Inicio</th>
                    <th>Cruce</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resultados as $index => $materia)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $materia['maestro'] }}</td>
                        <td>{{ $materia['materia'] }}</td>
                        <td>{{ $materia['grupo'] }}</td>
                        <td>{{ $materia['dia'] }}</td>
                        <td>{{ $materia['hora'] }}</td>
                        <td>{{ $materia['hora_inicio'] }}</td>
                        <td>
                            @if($materia['cruce'])
                                Hay cruce
                            @else
                                Bien
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($vista === 'materias_sin_maestro')
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Materia</th>
                    <th>Grupo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resultados as $index => $materia)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $materia['materia'] }}</td>
                        <td>{{ $materia['grupo'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($vista === 'grupos_sin_maestro')
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Grupo</th>
                    <th>Materia sin Maestro</th>
                </tr>
            </thead>
            <tbody>
                @php $contador = 1; @endphp
                @foreach ($resultados->groupBy('grupo_nombre') as $grupo => $materias)
                    @foreach ($materias as $materia)
                        <tr>
                            <td>{{ $contador++ }}</td>
                            <td>{{ $grupo }}</td>
                            <td>{{ $materia->materia_nombre }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

    @elseif ($vista === 'alumnos')
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Matrícula</th>
                    <th>Apellidos</th>
                    <th>Nombre</th>
                    <th>Grado y Grupo</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resultados as $alumno)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $alumno->matricula }}</td>
                    <td>{{ $alumno->apellidos }}</td>
                    <td>{{ $alumno->nombres }}</td>
                    <td>
                        {{
                            ($grado && trim($grado) !== '')
                            ? $grado
                            : (optional(optional($alumno->inscritos->first())->grupo)->grado ?? 'Sin grado')
                        }}
                        {{
                            optional(optional($alumno->inscritos->first())->grupo)->letra ?? 'Sin letra'
                        }}
                    </td>
                    <td>{{ $alumno->estatus }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</body>
