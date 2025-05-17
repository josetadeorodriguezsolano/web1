<div>
    <h2 class="text-2xl font-bold mb-4">Generador de Horarios</h2>
@if (session()->has('message'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('message') }}</span>
    </div>
@endif

@if (session()->has('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

<div class="mb-6">
    <label for="grupoSeleccionado" class="block text-sm font-medium text-gray-700">Seleccionar Grupo</label>
    <div class="mt-1 flex">
        <select wire:model="grupoSeleccionado" id="grupoSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">Seleccione un grupo</option>
            @foreach ($grupos as $grupo)
                <option value="{{ $grupo->id }}">{{ $grupo->grado }} "{{ $grupo->letra }}"</option>
            @endforeach
        </select>
        <button wire:click="cargarHorario" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cargar Horario
        </button>
    </div>
</div>

@if ($grupoSeleccionado)
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Asignar Clase</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="diaSeleccionado" class="block text-sm font-medium text-gray-700">Día</label>
                <select wire:model="diaSeleccionado" id="diaSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Seleccione día</option>
                    @foreach ($diasSemana as $dia)
                        <option value="{{ $dia }}">{{ $dia }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="horaSeleccionada" class="block text-sm font-medium text-gray-700">Hora</label>
                <select wire:model="horaSeleccionada" id="horaSeleccionada" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Seleccione hora</option>
                    @foreach ($horasClase as $index => $hora)
                        <option value="{{ $index + 1 }}">{{ $hora }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="materiaSeleccionada" class="block text-sm font-medium text-gray-700">Materia</label>
                <select wire:model="materiaSeleccionada" id="materiaSeleccionada" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Seleccione materia</option>
                    @foreach ($materias as $materia)
                        <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="maestroSeleccionado" class="block text-sm font-medium text-gray-700">Maestro</label>
                <select wire:model="maestroSeleccionado" id="maestroSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Seleccione maestro</option>
                    @foreach ($maestros as $maestro)
                        <option value="{{ $maestro->id }}">{{ $maestro->name }} {{ $maestro->apellidos }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button wire:click="asignarClase" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Asignar Clase
            </button>
        </div>
    </div>
    
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-2">Horario del Grupo</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hora
                        </th>
                        @foreach ($diasSemana as $dia)
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ $dia }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($horasClase as $index => $hora)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $hora }}
                            </td>
                            @foreach ($diasSemana as $dia)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if (isset($horario[$index + 1][$dia]))
                                        <div>
                                            <strong>{{ $horario[$index + 1][$dia]['materia'] ?? '' }}</strong>
                                            <p>{{ $horario[$index + 1][$dia]['maestro'] ?? '' }}</p>
                                            <button wire:click="eliminarAsignacion({{ $index + 1 }}, '{{ $dia }}')" class="text-red-600 hover:text-red-900 text-xs mt-1">
                                                Eliminar
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-gray-400">Sin asignar</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- <div class="mt-8">
        <button wire:click="exportarPDF" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Exportar a PDF
        </button>
    </div> -->
@endif
</div>