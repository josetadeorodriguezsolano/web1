<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inasistencias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-general {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007bff;
        }
        
        .table-container {
            width: 100%;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        thead {
            background-color: #343a40;
            color: white;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            font-weight: bold;
            font-size: 11px;
        }
        
        td {
            font-size: 10px;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        tbody tr:hover {
            background-color: #e8f4f8;
        }
        
        .fecha-col {
            width: 80px;
        }
        
        .maestro-col {
            width: 120px;
        }
        
        .materia-col {
            width: 100px;
        }
        
        .grupo-col {
            width: 60px;
        }
        
        .horario-col {
            width: 110px;
        }
        
        .justificacion-col {
            width: 150px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        
        .resumen {
            background-color: #e9ecef;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .resumen h3 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Inasistencias</h1>
        <p>Fecha de generación: {{ $fechaGeneracion ?? date('d/m/Y H:i:s') }}</p>
    </div>

    @if(isset($inasistencias) && count($inasistencias) > 0)
        <div class="resumen">
            <h3>Resumen</h3>
            <p><strong>Total de inasistencias:</strong> {{ count($inasistencias) }}</p>
            <p><strong>Período:</strong> 
                @php
                    $fechas = collect($inasistencias)->pluck('fecha')->sort();
                    $fechaMin = $fechas->first();
                    $fechaMax = $fechas->last();
                @endphp
                {{ \Carbon\Carbon::parse($fechaMin)->format('d/m/Y') }} - 
                {{ \Carbon\Carbon::parse($fechaMax)->format('d/m/Y') }}
            </p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="fecha-col">Fecha</th>
                        <th class="maestro-col">Maestro</th>
                        <th class="materia-col">Materia</th>
                        <th class="grupo-col">Grupo</th>
                        <th class="horario-col">Horario</th>
                        <th class="justificacion-col">Justificación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inasistencias as $inasistencia)
                        <tr>
                            <td class="fecha-col">
                                {{ \Carbon\Carbon::parse($inasistencia->fecha ?? $inasistencia['fecha'])->format('d/m/Y') }}
                            </td>
                            <td class="maestro-col">
                                @if(is_object($inasistencia) && $inasistencia->imparte && $inasistencia->imparte->maestro)
                                    {{ $inasistencia->imparte->maestro->name }} {{ $inasistencia->imparte->maestro->apellidos }}
                                @elseif(is_array($inasistencia))
                                    {{ $inasistencia['maestro'] ?? 'Sin maestro' }} {{ $inasistencia['maestro_apellidos'] ?? '' }}
                                @else
                                    Sin maestro
                                @endif
                            </td>
                            <td class="materia-col">
                                @if(is_object($inasistencia) && $inasistencia->imparte && $inasistencia->imparte->materia)
                                    {{ $inasistencia->imparte->materia->nombre }}
                                @elseif(is_array($inasistencia))
                                    {{ $inasistencia['materia'] ?? 'Sin materia' }}
                                @else
                                    Sin materia
                                @endif
                            </td>
                            <td class="grupo-col">
                                @if(is_object($inasistencia) && $inasistencia->imparte && $inasistencia->imparte->grupo)
                                    {{ $inasistencia->imparte->grupo->grado }}{{ $inasistencia->imparte->grupo->letra }}
                                @elseif(is_array($inasistencia))
                                    {{ $inasistencia['grupo'] ?? 'Sin grupo' }}
                                @else
                                    Sin grupo
                                @endif
                            </td>
                            <td class="horario-col">
                                @if(is_object($inasistencia) && $inasistencia->horario)
                                    {{ $inasistencia->horario->dia_semana ?? 'Sin día' }} - 
                                    @php
                                        $horasClase = [
                                            1 => '07:00 - 07:50',
                                            2 => '07:50 - 08:40',
                                            3 => '08:40 - 09:30',
                                            4 => '09:30 - 10:20',
                                            5 => '10:20 - 11:10',
                                            6 => '11:10 - 12:00',
                                            7 => '12:00 - 12:50'
                                        ];
                                    @endphp
                                    {{ $horasClase[$inasistencia->horario->hora_numero] ?? 'Hora desconocida' }}
                                @elseif(is_array($inasistencia))
                                    {{ $inasistencia['horario'] ?? 'Sin horario' }}
                                @else
                                    Sin horario
                                @endif
                            </td>
                            <td class="justificacion-col">
                                @if(is_object($inasistencia))
                                    {{ $inasistencia->justificacion ?? 'Sin justificación' }}
                                @elseif(is_array($inasistencia))
                                    {{ $inasistencia['justificacion'] ?? 'Sin justificación' }}
                                @else
                                    Sin justificación
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="no-data">
            <p>No hay inasistencias registradas para mostrar en este reporte.</p>
        </div>
    @endif

    <div class="footer">
        <p>Reporte generado automáticamente el {{ $fechaGeneracion ?? date('d/m/Y H:i:s') }}</p>
        <p>Sistema de Gestión Académica</p>
    </div>
</body>
</html>