<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inasistencias</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Reporte de Inasistencias</h2>
    <table>
        <thead>
            <tr>
                <th>Maestro</th>
                <th>Materia</th>
                <th>Grupo</th>
                <th>Horario de Falta</th>
                <th>Horario de Llegada</th>
                <th>Justificación</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inasistencias as $inasistencia)
            <tr>
                <td>{{ $inasistencia->maestro->name ?? 'N/A' }} {{ $inasistencia->maestro->apellidos ?? '' }}</td>
                <td>{{ $inasistencia->materia->nombre ?? 'N/A' }}</td>
                <td>{{ $inasistencia->grupo->letra ?? 'N/A' }}</td>
                <td>{{ $inasistencia->horario_falta }}</td>
                <td>{{ $inasistencia->horario_llegada }}</td>
                <td>{{ $inasistencia->justificacion }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>