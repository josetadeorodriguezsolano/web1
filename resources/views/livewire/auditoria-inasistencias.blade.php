<div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Auditoría de Inasistencias</h1>

    <form wire:submit.prevent="aplicarFiltros" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label>Matrícula</label>
            <input wire:model.defer="matricula" type="text" class="w-full border rounded px-2 py-1">
        </div>

        <div>
            <label>Materia</label>
            <select wire:model.defer="materiaId" class="w-full border rounded px-2 py-1">
                <option value="">-- Todas --</option>
                @foreach ($materias as $materia)
                    <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                @endforeach
            </select>
        </div>


        <div class="md:col-span-3">
            <button class="bg-blue-500 text-white px-4 py-2 rounded">Buscar</button>
        </div>
    </form>

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
                    <td class="border px-2 py-1">{{ $audit->created_at }}</td>
                    <td class="border px-2 py-1">{{ $audit->event }}</td>
                    <td class="border px-2 py-1">{{ $audit->user?->name ?? 'Sistema' }}</td>
                    <td class="border px-2 py-1 text-xs whitespace-pre-line">
                        @foreach($audit->getModified() as $key => $change)
                            <div><strong>{{ $key }}:</strong> {{ json_encode($change) }}</div>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-2">Sin registros</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $audits->links() }}
    </div>
</div>
