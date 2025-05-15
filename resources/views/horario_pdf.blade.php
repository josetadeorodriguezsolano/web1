<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Horario del Grupo</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: #eaeaea;
        }
    </style>
</head>
<body>
    <h2>Horario del Grupo {{ $grupo ?? 'Desconocido' }}</h2>

    <table>
        <thead>
            <tr>
                <th>Hora</th>
                <th>Lunes</th>
                <th>Martes</th>
                <th>Miércoles</th>
                <th>Jueves</th>
                <th>Viernes</th>
            </tr>
        </thead>
        <tbody>
            @php
                $inicio = \Carbon\Carbon::createFromTime(7, 0); // 7:00 AM
            @endphp

            @for ($hora = 1; $hora <= 7; $hora++)
                @php
                    $inicioBloque = $inicio->copy();
                    $finBloque = $inicio->copy()->addMinutes(50);
                    $rango = $inicioBloque->format('H:i') . ' - ' . $finBloque->format('H:i');
                    $inicio->addMinutes(50); // actualizar para la siguiente fila
                @endphp
                <tr>
                    <td>{{ $rango }}</td>
                    @for ($dia = 1; $dia <= 5; $dia++)
                        <td>
                            {{ $horas[$hora][$dia] ?? '' }}
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>
</body>
</html>
