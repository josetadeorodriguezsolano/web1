<div class="p-6 space-y-6">

    {{-- Estadísticas superiores --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Total Alumnos</h2>
            <p class="text-2xl font-bold">{{ $totalAlumnos }}</p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Total Maestros</h2>
            <p class="text-2xl font-bold">{{ $totalMaestros }}</p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Materias sin Maestro</h2>
            <p class="text-2xl font-bold">{{ $materiasSinMaestroCount }}</p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Grupos sin Maestro</h2>
            <p class="text-2xl font-bold">{{ $gruposSinMaestroCount }}</p>
        </div>
    </div>

    {{-- Filtros de búsqueda --}}
    <div class="bg-white p-4 shadow rounded">
        <h3 class="text-lg font-semibold mb-4">Reportes de la Institución</h3>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block mb-1">Grado</label>
                <select wire:model="grado" class="w-full border rounded p-2">
                    <option value="">Todos</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div>
                <label class="block mb-1">Grupo</label>
                <select wire:model="letra" class="w-full border rounded p-2">
                    <option value="">Todos</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                </select>
            </div>
            <div>
                <label class="block mb-1">Generación</label>
                <select wire:model="generacion" class="w-full border rounded p-2">
                    <option value="">Todos</option>
                    @foreach ($generaciones as $gen)
                        <option value="{{ $gen }}">{{ $gen }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1">Materia</label>
                <select wire:model="materia_id" class="w-full border rounded p-2">
                    <option value="">Todos</option>
                    {{-- Aquí iría la lista de materias --}}
                </select>
            </div>
            <div>
                <label class="block mb-1">Maestro</label>
                <select wire:model="maestro_id" class="w-full border rounded p-2">
                    <option value="">Todos</option>
                    {{-- Aquí iría la lista de maestros --}}
                </select>
            </div>
        </div>

        <div class="text-right">
            <button wire:click="controlEscolar" class="bg-black text-white px-4 py-2 rounded inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Buscar Reporte
            </button>
        </div>
    </div>

    {{-- Tabs de visualización --}}
    <div class="bg-white p-4 shadow rounded">
        <div class="flex space-x-4 border-b pb-2 mb-4">
            <button class="font-semibold border-b-2 border-black">Maestros</button>
            <button>Materias por Maestro</button>
            <button>Materias sin Maestro</button>
            <button>Grupos sin Maestro</button>
        </div>

        <div class="flex justify-between items-center mb-2">
            <button wire:click="actualizar" class="flex items-center bg-gray-100 px-3 py-1 rounded">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 4v6h6M20 20v-6h-6M4 20l6-6M20 4l-6 6"/>
                </svg>
                Actualizar
            </button>
            <button class="flex items-center bg-gray-100 px-3 py-1 rounded">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 20l9-5-9-5-9 5 9 5z"/>
                    <path d="M12 12V4l9 5-9 5-9-5 9-5z"/>
                </svg>
                Exportar PDF
            </button>
        </div>

        {{-- Tabla de alumnos corregida --}}
        @if($resultados && $resultados->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-2">#</th>
                <th class="border px-4 py-2">Matrícula</th>
                <th class="border px-4 py-2">Apellidos</th>
                <th class="border px-4 py-2">Nombres</th>
                <th class="border px-4 py-2">Grado/Grupo</th>
                <th class="border px-4 py-2">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resultados as $index => $alumno)
            <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                <td class="border px-4 py-2">{{ $alumno->matricula }}</td>
                <td class="border px-4 py-2">{{ $alumno->apellidos }}</td>
                <td class="border px-4 py-2">{{ $alumno->nombres }}</td>
                <td class="border px-4 py-2">
                    {{ $alumno->inscritos->first()->grupo->grado ?? '' }}{{ $alumno->inscritos->first()->grupo->letra ?? '' }}
                </td>
                <td class="border px-4 py-2">{{ $alumno->estatus }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="p-8 text-center text-gray-500">
    No se encontraron alumnos con los criterios seleccionados
</div>
@endif
    </div>
</div>
