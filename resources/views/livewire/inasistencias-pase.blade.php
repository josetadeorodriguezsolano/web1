<div class="p-4">
    <div class="flex space-x-4 mb-4">
        <div>
            <label>Generación</label>
            <select wire:model="generacionSeleccionada" wire:change="actualizarTabla">
                <option value="">Selecciona una generación</option>
                @foreach($generaciones as $gen)
                    <option value="{{ $gen }}">{{ $gen }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Grupo</label>
            <select wire:model.defer="grupoSeleccionado" wire:change="actualizarTabla">
                <option value="">Selecciona un grupo</option>
                @foreach($gruposFiltrados as $grupo)
                    <option value="{{ $grupo->id }}">{{ $grupo->grado }}°{{ $grupo->letra }} ({{ $grupo->generacion }})</option>
                @endforeach
            </select>
        </div>


        <div>
            <label>Materia</label>
            <select wire:model.defer="materiaSeleccionada" wire:change="actualizarTabla">
                <option value="">Selecciona una materia</option>
                @foreach($materias as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label>Mes</label>
            <input type="month" wire:model.defer="fechaSeleccionada" wire:change="actualizarTabla">
        </div>
    </div>
    

    @if($alumnos && count($diasDelMes))
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-2 py-1">Alumno</th>
                    @foreach($diasDelMes as $dia)
                        <th class="border px-2 py-1 text-center text-xs">{{ $dia['nombre'] }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="border px-2 py-1"></th>
                    @foreach($diasDelMes as $dia)
                        <th class="border px-2 py-1 text-center">{{ $dia['numero'] }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($alumnos as $alumno)
                    @php
                        $key = $alumno->id . '-' . $refrescar;
                    @endphp
                    <tr @key($key)>
                        <td class="border px-2 py-1">{{ $alumno->apellidos }} {{ $alumno->nombres }}</td>
                        @foreach($diasDelMes as $dia)
                            <td class="border px-2 py-1 text-center">
                                <input type="checkbox"
                                    wire:key="checkbox-{{ $alumno->id }}-{{ $dia['numero'] }}-{{ $refrescar }}"
                                    wire:click="toggleInasistencia({{ $alumno->id }}, {{ $dia['numero'] }})"
                                    {{ $inasistenciasPorDia[$alumno->id][$dia['numero']] ?? false ? 'checked' : '' }}>
                            </td>
                        @endforeach

                    </tr>
                @endforeach

            </tbody>
        </table>
    @endif
</div>
