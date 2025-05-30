<div>
    <style>
        body {
            background-color: #f9fbfd; /* Fondo claro y agradable */
            font-family: Arial, sans-serif;
        }
    
        h1 {
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }
    
        button {
            margin: 5px;
            padding: 8px 14px;
            border: none;
            background-color: #a3d5ff;
            color: #1a1a1a;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
    
        button:hover {
            background-color: #72bdf2;
        }
    
        button.cancelar {
            background-color: #f8c291;
        }
    
        button.cancelar:hover {
            background-color: #f5b07d;
        }
    
        hr {
            margin: 20px 0;
            border-color: #dcdcdc;
        }
    
        label {
            font-weight: bold;
            color: #34495e;
        }
    
        select, input, textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            background-color: #ffffff;
            color: #2c3e50;
        }
    
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
            color: #2c3e50;
            background-color: #ffffff;
        }
    
        th, td {
            border: 1px solid #dfe6e9;
            padding: 10px;
            text-align: center;
        }
    
        th {
            background-color: #dfefff;
        }
    
        tr.seleccionado {
            background-color: #d6dfeb;
        }
    
        tr:hover {
            background-color: #f1f2f6;
        }
    
     .formulario {
    width: 100%;
    margin-top: 20px;
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    box-sizing: border-box;
    
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between; /* distribuye los elementos de manera equitativa */
    gap: 15px; /* espacio entre los elementos */
}

.formulario div {
    flex: 1 1 calc(15% - 15px); /* 33% del ancho total menos el espacio entre elementos */
    min-width: 250px; /* para evitar que los campos sean demasiado pequeños */
}

    </style>
    
    <div>
@php
    function obtenerRangoHorario($numero) {
        if ($numero < 1 || $numero > 7) {
            return 'Error: número de horario inválido';
        }

        $inicio = \Carbon\Carbon::createFromTime(7, 0)->addMinutes(50 * ($numero - 1));
        $fin = $inicio->copy()->addMinutes(50);
        return $inicio->format('H:i') . ' - ' . $fin->format('H:i');
    }
@endphp

<h1>CATÁLOGO DE HORARIOS</h1>

<div class="formulario" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
    <div>
        <label>Maestro</label><br>
        <select id="maestroSelect" wire:model="maestro_id">
            <option value="">Seleccione un maestro</option>
            @foreach ($maestros as $maestro)
                <option value="{{ $maestro->id }}">{{ $maestro->name }} {{ $maestro->apellidos }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Fecha</label><br>
        <input type="date" id="fechaInput" wire:model="inasistencia.fecha">
    </div>

    <div>
        <label>Justificación</label><br>
        <input type="text" wire:model="inasistencia.justificacion">
    </div>
</div>

@include('livewire.errores')

@if($horarios && $horarios->isNotEmpty())
<table>
    <thead>
        <tr>
            <th>Día</th>
            <th>Hora</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($horarios as $key => $horario)
        <tr wire:click="seleccionar({{ $key }})"
            class="{{ $seleccionado == $key ? 'seleccionado' : '' }} {{ $horario->ocupado ? 'bg-red-300' : '' }}">
            <td>{{ $horario->dia_semana }}</td>
            <td>{{ obtenerRangoHorario($horario->hora_numero) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
    <p>No hay horarios disponibles para este maestro.</p>
@endif

<hr>

<div style="text-align: center;">
    <button wire:click="obtenerHorariosPorMaestro" style="background-color: #28a745;">
        Actualizar
    </button>

    <button wire:click="guardar" style="background-color: #ffc107;">
        Guardar
    </button>

    <button wire:click="eliminar" style="background-color: #dc3545;">
        Eliminar
    </button>
</div>
