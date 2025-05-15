<div class="p-6 text-[17px]">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Alumnos Inscritos</h2>

    @if (session('mensaje'))
        <div class="mb-4 p-4 rounded-md bg-green-100 text-green-800">
            {{ session('mensaje') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
            <label class="block text-xl font-medium text-gray-700 mb-2">Generación:</label>
            <select wire:model="generacionSeleccionada" class="mt-1 block w-full pl-3 pr-10 py-2 text-xl font-medium border-gray-300 rounded-md">
                <option value="">Todas las generaciones</option>
                @foreach ($generaciones as $gen)
                    <option value="{{ $gen->generacion }}">{{ $gen->generacion }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xl font-medium text-gray-700 mb-2">Grupo:</label>
            <select wire:model="grupoSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-xl font-medium border-gray-300 rounded-md">
                <option value="">Todos los grupos</option>
                @foreach ($grupos as $grupo)
                    <option value="{{ $grupo->id }}">{{ $grupo->grado }}°{{ $grupo->letra }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <button wire:click="$refresh" class="w-full px-4 py-2 bg-[#1E3A8A] text-white text-xl font-semibold rounded-md hover:bg-blue-900 transition">
                Aplicar Filtros
            </button>
        </div>
    </div>

    <!-- Tabla -->
    <div class="mt-8">
        <h3 class="text-2xl font-medium text-gray-900 mb-4">Lista de Alumnos Inscritos</h3>

        @if($inscritos instanceof \Illuminate\Pagination\LengthAwarePaginator && $inscritos->total() > 0)
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200 border-collapse text-lg">
                    <thead class="bg-[#1E3A8A] text-white">
                        <tr>
                            <th class="py-3 text-left pl-4">ID</th>
                            <th class="py-3 text-left">Nombre</th>
                            <th class="py-3 text-left">Matrícula</th>
                            <th class="py-3 text-left">Generación</th>
                            <th class="py-3 text-left">Grupo</th>
                            <th class="py-3 text-center">Estatus</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($inscritos as $inscrito)
                            <tr>
                                <td class="py-3 px-4">{{ $inscrito->id }}</td>
                                <td class="py-3 px-4">
                                    {{ $inscrito->alumno->apellidos }} {{ $inscrito->alumno->nombres }}
                                </td>
                                <td class="py-3 px-4">{{ $inscrito->alumno->matricula }}</td>
                                <td class="py-3 px-4">{{ $inscrito->grupo->generacion }}</td>
                                <td class="py-3 px-4">{{ $inscrito->grupo->grado }}°{{ $inscrito->grupo->letra }}</td>
                                <td class="py-3 text-center">
                                    <span class="px-4 py-1 inline-flex font-semibold rounded-full
                                        {{ $inscrito->estatus === 'activo' ? 'bg-green-100 text-green-800' :
                                           ($inscrito->estatus === 'inactivo' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($inscrito->estatus) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button wire:click="confirmarEliminacion({{ $inscrito->id }})" class="bg-red-100 text-red-800 hover:bg-red-200 px-3 py-1 rounded-md">Eliminar</button>
                                        <button wire:click="" class="bg-green-100 text-green-800 hover:bg-green-200 px-3 py-1 rounded-md">Ver alumno</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $inscritos->links() }}
            </div>
        @else
            <p class="text-gray-500 text-lg mt-4">No hay alumnos inscritos.</p>
        @endif
    </div>

    <!-- Modal de eliminación -->
    @if($mostrarModalEliminar)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar eliminación</h3>
                <p class="text-gray-700 mb-4">¿Seguro que deseas eliminar este alumno inscrito? Esta acción no se puede deshacer.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('mostrarModalEliminar', false)" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md">Cancelar</button>
                    <button wire:click="eliminarInscrito" class="bg-red-600 text-white px-4 py-2 rounded-md">Eliminar</button>
                </div>
            </div>
        </div>
    @endif

</div>
