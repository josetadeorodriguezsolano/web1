<div style="padding: 20px;">
    <style>
        body {
            background-color: #f9fbfd;
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
            justify-content: space-between;
            gap: 15px;
        }

        .formulario div {
            flex: 1 1 calc(20% - 15px);
            min-width: 250px;
        }
    </style>
<div>
    <h1>SELECCIÓN DE CONSULTA</h1>

    <div class="formulario mb-4">
        <div>
            <label for="tipoConsulta">Tipo de consulta</label>
            <select id="tipoConsulta" wire:model="tipoConsulta" class="border px-2 py-1 rounded">
                <option value="">Seleccione una opción</option>
                <option value="inasistencia">Inasistencia</option>
                <option value="horario">Horario</option>
            </select>
        </div>
    </div>

    @if (!empty($auditorias))
        <div>
            <h2 class="text-lg font-bold mb-4">Auditoría de {{ ucfirst($tipoConsulta) }}</h2>
            <table class="table-auto w-full border mt-4">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Evento</th>
                        <th class="px-4 py-2 border">Nombre Maestro</th>
                        <th class="px-4 py-2 border">Apellidos</th>
                        <th class="px-4 py-2 border">Fecha</th>
                        <th class="px-4 py-2 border">Old Values</th>
                        <th class="px-4 py-2 border">New Values</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($auditorias as $audit)
                        <tr class="border">
                            <td class="px-4 py-2 border">{{ $audit->id }}</td>
                            <td class="px-4 py-2 border">{{ $audit->event }}</td>
                            <td class="px-4 py-2 border">{{ $audit->nombre_maestro }}</td>
                            <td class="px-4 py-2 border">{{ $audit->apellidos_maestro }}</td>
                            <td class="px-4 py-2 border">{{ $audit->created_at }}</td>
                            <td class="px-4 py-2 border text-sm">
                                @foreach ($audit->old_values ?? [] as $key => $value)
                                    <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                @endforeach
                            </td>
                            <td class="px-4 py-2 border text-sm">
                                @foreach ($audit->new_values ?? [] as $key => $value)
                                    <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="text-center mt-6">
        <button wire:click="cargarAuditorias"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
            Mostrar auditoría
        </button>
    </div>
</div>
