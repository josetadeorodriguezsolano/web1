<div class="p-6 text-[17px]">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Alumnos Inscritos</h2>

    @if (session('mensaje'))
        <div class="mb-4 p-4 rounded-md {{ session('tipo') === 'success' ? 'bg-green-100 text-green-800' : (session('tipo') === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
            {{ session('mensaje') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
            <label class="block text-xl font-medium text-gray-700 mb-2">Generación:</label>
            <select wire:model.live="generacionSeleccionada" class="mt-1 block w-full pl-3 pr-10 py-2 text-xl font-medium border-gray-300 rounded-md">
                <option value="">Todas las generaciones</option>
                @foreach ($generaciones as $gen)
                    <option value="{{ $gen->generacion }}">{{ $gen->generacion }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xl font-medium text-gray-700 mb-2">Grupo:</label>
            <select wire:model.live="grupoSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-xl font-medium border-gray-300 rounded-md">
                <option value="">Todos los grupos</option>
                @foreach ($grupos as $grupo)
                    <option value="{{ $grupo->id }}">{{ $grupo->grado }}°{{ $grupo->letra }}</option>
                @endforeach
            </select>
        </div>

        <div>
        <label class="block text-xl font-medium text-gray-700 mb-2">Estatus:</label>
            <select wire:model.live="estatusSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-xl font-medium border-gray-300 rounded-md">
                <option value="">Todos los estatus</option>
                <option value="vigente">Vigente</option>
                <option value="baja">Baja</option>
                <option value="egresado">Egresado</option>
            </select>
        </div>

    </div>

    <!-- Tabla con indicador de carga -->
    @if($cargando)
    <div class="flex justify-center items-center my-8">
        <svg class="animate-spin h-10 w-10 text-[#1E3A8A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    @else
    <div class="mt-8">
        <h3 class="text-2xl font-medium text-gray-900 mb-4">Lista de Alumnos Inscritos</h3>

        @if(isset($inscritos) && $inscritos->count() > 0)
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
                                        {{ $inscrito->estatus === 'vigente' ? 'bg-green-100 text-green-800' :
                                           ($inscrito->estatus === 'baja' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($inscrito->estatus) }}
                                    </span>
                                </td>
                                <td class="py-3 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button wire:click="confirmarEliminacion({{ $inscrito->id }})" class="bg-red-100 text-red-800 hover:bg-red-200 px-3 py-1 rounded-md">
                                            Eliminar
                                        </button>
                                        <button wire:click="verAlumno({{ $inscrito->alumno_id }})" class="bg-green-100 text-green-800 hover:bg-green-200 px-3 py-1 rounded-md">
                                            Ver alumno
                                        </button>
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
            <div class="mt-8 bg-yellow-50 p-4 rounded-md">
                <p class="text-yellow-700 text-lg">No hay alumnos inscritos que coincidan con los criterios de búsqueda.</p>
            </div>
        @endif
    </div>
    @endif

    <!-- Modal de eliminación -->
    @if($mostrarModalEliminar)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar eliminación</h3>
                <p class="text-gray-700 mb-4">¿Seguro que deseas eliminar este alumno inscrito? Esta acción no se puede deshacer.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('mostrarModalEliminar', false)" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md">
                        Cancelar
                    </button>
                    <button wire:click="eliminarInscrito" wire:loading.attr="disabled" class="bg-red-600 text-white px-4 py-2 rounded-md disabled:opacity-75">
                        <span wire:loading.remove wire:target="eliminarInscrito">Eliminar</span>
                        <span wire:loading wire:target="eliminarInscrito">Eliminando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Script para manejo de alertas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('mostrar-alerta', event => {
                const tipo = event.detail.tipo;
                const mensaje = event.detail.mensaje;
                
                // Crear y mostrar el mensaje de alerta
                const alertaDiv = document.createElement('div');
                alertaDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
                    tipo === 'success' ? 'bg-green-100 text-green-800' : 
                    tipo === 'error' ? 'bg-red-100 text-red-800' : 
                    'bg-yellow-100 text-yellow-800'
                } transition-opacity duration-500`;
                alertaDiv.style.maxWidth = '400px';
                alertaDiv.innerHTML = mensaje;
                
                document.body.appendChild(alertaDiv);
                
                // Eliminar la alerta después de 3 segundos
                setTimeout(() => {
                    alertaDiv.classList.add('opacity-0');
                    setTimeout(() => {
                        alertaDiv.remove();
                    }, 500);
                }, 3000);
            });
        });
    </script>
   
</div>