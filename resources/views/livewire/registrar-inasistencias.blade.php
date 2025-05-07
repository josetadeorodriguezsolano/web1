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
            max-width: 600px;
            margin: auto;
            margin-top: 20px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
    
    

    <div>
        <h1>CATÁLOGO DE INASISTENCIAS</h1>
        <div style="text-align: center;">
            <button wire:click='agregar'>Agregar</button>
            <button wire:click='modificar'>Modificar</button>
            <button wire:click='eliminar'>Eliminar</button>
            <button wire:click='GPDF'>Generar PDF</button>
        </div>
    </div>
    <hr>

    @if ($mostrarFormulario)
    <div class="formulario">
        <label>Maestro</label>
        <select wire:model='inasistencia.maestros_id'>
            <option value="">Seleccione un maestro</option>
            @foreach ($maestros as $maestro)
                <option value="{{ $maestro->id }}">{{ $maestro->name }} {{ $maestro->apellidos }}</option>
            @endforeach
        </select>

        <label>Materia</label>
        <select wire:model='inasistencia.materias_id'>
            <option value="">Seleccione una materia</option>
            @foreach ($materias as $materia)
                <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
            @endforeach
        </select>

        <label>Grupo</label>
        <select wire:model='inasistencia.grupos_id'>
            <option value="">Seleccione un grupo</option>
            @foreach ($grupos as $grupo)
                <option value="{{ $grupo->id }}">{{ $grupo->letra }}</option>
            @endforeach
        </select>

        <label>Horario de Falta</label>
        <input type="datetime-local" wire:model='inasistencia.horario_falta'>

        <label>Horario de Llegada</label>
        <input type="datetime-local" wire:model='inasistencia.horario_llegada'>

        <label>Justificación</label>
        <textarea wire:model='inasistencia.justificacion'></textarea>

        <div style="text-align: center;">
            <button wire:click='cancelar' style="background-color: #6c757d;">Cancelar</button>
            <button wire:click='guardar'>Guardar</button>
        </div>
    </div>
    @endif

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
            @foreach ($inasistencias as $key => $inasistencia)
            <tr wire:click='seleccionar({{ $key }})' class="{{ $seleccionado == $key ? 'seleccionado' : '' }}">
                <td>{{ $maestros->firstWhere('id', $inasistencia['maestros_id'])->name ?? 'N/A' }} {{ $maestros->firstWhere('id', $inasistencia['maestros_id'])->apellidos ?? '' }}</td>
                <td>{{ $materias->firstWhere('id', $inasistencia['materias_id'])->nombre ?? 'N/A' }}</td>
                <td>{{ $grupos->firstWhere('id', $inasistencia['grupos_id'])->letra ?? 'N/A' }}</td>
                <td>{{ $inasistencia['horario_falta'] }}</td>
                <td>{{ $inasistencia['horario_llegada'] }}</td>
                <td>{{ $inasistencia['justificacion'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @include('livewire.errores')
</div>
