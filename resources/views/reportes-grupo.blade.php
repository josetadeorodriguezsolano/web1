<div class="p-6 space-y-6">

    {{-- Estadísticas superiores --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Total Alumnos</h2>
            <p class="text-2xl font-bold">200</p> {{-- Reemplazar con variable Livewire --}}
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Total Maestros</h2>
            <p class="text-2xl font-bold">18</p> {{-- Reemplazar con variable Livewire --}}
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Materias sin Maestro</h2>
            <p class="text-2xl font-bold">3</p> {{-- Reemplazar con variable Livewire --}}
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Grupos sin Maestro</h2>
            <p class="text-2xl font-bold">2</p> {{-- Reemplazar con variable Livewire --}}
        </div>
    </div>

    {{-- Filtros de búsqueda --}}
    <div class="bg-white p-4 shadow rounded">
        <h3 class="text-lg font-semibold mb-4">Reportes de la Institución</h3>

        <div class="grid grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block mb-1">Grupo</label>
                <select wire:model="grupo_id" class="w-full border rounded p-2">
                    <option value="">Todos</option>
                    @foreach ($grupos as $grupo)
                        <option value="{{ $grupo->id }}">{{ $grupo->grado }} {{ $grupo->letra }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1">Generación</label>
                <select wire:model="generacion" class="w-full border rounded p-2">
                    <option value="">Todos</option>
                    <option value=""> 2020 </option>
                    {{-- @foreach ($generaciones as $gen) --}}
                    {{-- <option value="{{ $gen }}">{{ $gen }}</option> --}}
                    {{-- @endforeach --}}
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
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
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
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M4 4v6h6M20 20v-6h-6M4 20l6-6M20 4l-6 6"/>
                </svg>
                Actualizar
            </button>
            <button class="flex items-center bg-gray-100 px-3 py-1 rounded">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M12 20l9-5-9-5-9 5 9 5z"/>
                    <path d="M12 12V4l9 5-9 5-9-5 9-5z"/>
                </svg>
                Exportar PDF
            </button>
        </div>
        @if (count($alumnos) > 0)
        {{-- Tabla de resultados --}}
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">ID</th>
                        <th class="py-2">Nombre</th>
                        <th class="py-2">Materias</th>
                        <th class="py-2">Grupos</th>
                        </tr>
                </thead>
                <tbody>
                    @foreach($alumnos as $alumno)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $alumno->nombres }}</td>
                            <td class="px-4 py-2">{{ $alumno->apellidos }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
        <p class="text-gray-600 mt-4">No hay alumnos inscritos en este grupo.</p>
        @endif
        {{-- Paginación (si se usa) --}}
        <div class="flex justify-end mt-4 space-x-2">
            <button class="px-4 py-2 border rounded">Anterior</button>
            <button class="px-4 py-2 border rounded">Siguiente</button>
        </div>
    </div>

</div>
