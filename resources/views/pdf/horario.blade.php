<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Horario del Grupo {{ $grupo->letra }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
        }
        .materia {
            font-weight: bold;
        }
        .maestro {
            font-style: italic;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <h1>Horario del Grupo {{ $grupo->grado }} "{{ $grupo->letra }}" "{{ $grupo->generacion }}"</h1>
    
    <table>
        <thead>
            <tr>
                <th>Hora</th>
                @foreach ($diasSemana as $dia)
                    <th>{{ $dia }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($horasClase as $index => $hora)
                <tr>
                    <td>{{ $hora }}</td>
                    @foreach ($diasSemana as $dia)
                        <td>
                            @if (isset($horario[$index + 1][$dia]))
                                <div class="materia">{{ $horario[$index + 1][$dia]['materia'] ?? '' }}</div>
                                <div class="maestro">{{ $horario[$index + 1][$dia]['maestro'] ?? '' }}</div>
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="font-size: 10px; text-align: center;">
        Generado el: {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>