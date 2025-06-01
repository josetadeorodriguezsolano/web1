<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
    <title>Auditoría de Inasistencias</title>
</head>
<body>
    <h2>Reporte de Auditoría de Inasistencias</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Evento</th>
                <th>Usuario</th>
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($audits as $audit)
                <tr>
                    <td>{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ ucfirst($audit->event) }}</td>
                    <td>{{ $audit->user?->name ?? 'Sistema' }}</td>
                    <td>
                        @foreach ($audit->getModified() as $key => $change)
                            <strong>{{ $key }}:</strong> {{ json_encode($change, JSON_UNESCAPED_UNICODE) }}<br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
