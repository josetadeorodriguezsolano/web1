<div class="overflow-x-auto">
    <h2 class="text-xl font-bold mb-4">Horario del Grupo A</h2>

    <table class="table-auto border w-full text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-2 py-1">Hora</th>

                {{-- Imprime los días de la semana en encabezados --}}
                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia)
                    <th class="border px-2 py-1">{{ $dia }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            {{-- Recorre las horas 1 a 7 para las filas --}}
            @for($hora = 1; $hora <= 7; $hora++)
                <tr>
                    {{-- Muestra el rango horario en la primera columna --}}
                    <td class="border px-2 py-1">{{ $this->obtenerRangoHora($hora) }}</td>

                    {{-- Por cada día, muestra la materia y maestro si existe para esa hora --}}
                    @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $dia)
                        <td class="border px-2 py-1">
                            @if(isset($horarios[$hora][$dia]))
                                {{ $horarios[$hora][$dia]->materia }}<br>
                                <small>({{ $horarios[$hora][$dia]->maestro }})</small>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>
</div>
