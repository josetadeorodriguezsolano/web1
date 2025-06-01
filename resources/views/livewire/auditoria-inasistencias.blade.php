<div class="p-4">

    <form wire:submit.prevent="aplicarFiltros" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">

        <div style="background-color: #bebebe;" class="w3-padding rounded-lg">
            <div class="flex justify-center">
                <div class="w3-margin">
                    <label>Acciones</label>
                    <select wire:model.defer="eventoSeleccionado" class="w-full border rounded px-2 py-1">
                        <option value="">Todas las acciones</option>
                        <option value="Created">Creado</option>
                        <option value="Updated">Actualizado</option>
                        <option value="Deleted">Eliminado</option>
                    </select>
                </div>
                <div class="w3-margin">
                    <label>Matrícula</label>
                    <input wire:model.defer="matricula" type="text" class="w-full border rounded px-2 py-1">
                </div>
            </div>

            <div class="flex justify-center w3-padding">
                <div class="w3-margin">
                    <label>Maestros</label>
                    <select wire:model.defer="maestroId" class="w-full border rounded px-2 py-1">
                        <option value="">Todos los maestros</option>
                        <option value="Sistema">Sistema</option>
                        @foreach ($maestros as $maestro)
                            <option value="{{ $maestro->id }}">{{ $maestro->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w3-margin">
                    <label>Materia</label>
                    <select wire:model.defer="materiaId" class="w-full border rounded px-2 py-1">
                        <option value="">-- Todas --</option>
                        @foreach ($materias as $materia)
                            <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w3-margin">
                    <label>Mes</label>
                    <select wire:model.defer="mesSeleccionado" class="w-full border rounded px-2 py-1">
                        @foreach ($meses as $valor => $nombre)
                            <option value="{{ $valor }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>




            <div class="flex items-end">
                <button class="bg-blue-500 text-white px-4 py-2 rounded w-full">Buscar</button>
            </div>
        </div>

    </form>
    <div style="background-color: #bebebe;" class="w3-padding rounded-lg">
       @if (count($audits) > 0)
    <div class="flex items-end w3-margin">
        <button wire:click="exportarPDF" type="button" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Descargar PDF</button>
    </div>
@endif

        <table class="w-full border-collapse border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Fecha</th>
                    <th class="border px-2 py-1">Evento</th>
                    <th class="border px-2 py-1">Usuario</th>
                    <th class="border px-2 py-1">Detalles</th>
                </tr>
            </thead>
            <tbody>
                @forelse($audits as $audit)
                    <tr>
                        <td class="border px-2 py-1">{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                        <td class="border px-2 py-1">{{ ucfirst($audit->event) }}</td>
                        <td class="border px-2 py-1">{{ $audit->user?->name ?? 'Sistema' }}</td>
                        <td class="border px-2 py-1 text-xs whitespace-pre-line">
                            <div
                                style="display: flex; flex-wrap: wrap; background-color: #f1f1f1; border-radius: 5px; padding: 10px; margin: 10px 0;">
                                @foreach ($audit->getModified() as $key => $change)
                                    <div style="flex: 0 0 25%; box-sizing: border-box; padding: 5px;">
                                        <div class="w3-card w3-round text-center">
                                            <strong>{{ $key }}:</strong>
                                            <p>{{ json_encode($change, JSON_UNESCAPED_UNICODE) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-2">Sin registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>



    <div class="mt-4">
        {{ $audits->links() }}
    </div>
    @include('livewire.errores')
</div>
