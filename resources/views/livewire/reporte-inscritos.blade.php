<div class="p-6 text-[17px]">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Reporte de Movimientos de Inscritos</h2>

    @if($mensaje)
        <div class="mb-4 p-4 rounded-md {{ $tipoMensaje === 'success' ? 'bg-green-100 text-green-800' : ($tipoMensaje === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
            {{ $mensaje }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="mb-6 p-4 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-4">Filtros de Búsqueda</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Generación -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-2">Generación:</label>
                <select wire:model.live="generacionSeleccionada" class="w-full pl-3 pr-10 py-2 text-lg border-gray-300 rounded-md">
                    <option value="">Todas las generaciones</option>
                    @foreach($generaciones as $gen)
                        <option value="{{ $gen['generacion'] }}">{{ $gen['generacion'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Grupo -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-2">Grupo:</label>
                <select wire:model.live="grupoSeleccionado" class="w-full pl-3 pr-10 py-2 text-lg border-gray-300 rounded-md">
                    <option value="">Todos los grupos</option>
                    @foreach($grupos as $grupo)
                        <option value="{{ $grupo['id'] }}">{{ $grupo['grado'] }}°{{ $grupo['letra'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tipo de Acción -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-2">Tipo de Cambio:</label>
                <select wire:model="accionFiltro" class="w-full pl-3 pr-10 py-2 text-lg border-gray-300 rounded-md">
                    <option value="">Todos los cambios</option>
                    <option value="created">Inscripciones</option>
                    <option value="updated">Cambios de estatus</option>
                    <option value="deleted">Eliminaciones</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Fecha Inicio -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-2">Fecha Inicio:</label>
                <input 
                    type="date" 
                    wire:model="fechaInicio"
                    class="w-full pl-3 pr-3 py-2 text-lg border-gray-300 rounded-md"
                >
            </div>

            <!-- Fecha Fin -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-2">Fecha Fin:</label>
                <input 
                    type="date" 
                    wire:model="fechaFin"
                    class="w-full pl-3 pr-3 py-2 text-lg border-gray-300 rounded-md"
                >
            </div>
        </div>

        <div class="flex space-x-3">
            <button 
                wire:click="aplicarFiltros" 
                class="px-4 py-2 bg-[#1E3A8A] text-white text-lg font-semibold rounded-md hover:bg-blue-900 transition"
            >
                Aplicar Filtros
            </button>
            <button 
                wire:click="limpiarFiltros" 
                class="px-4 py-2 bg-gray-600 text-white text-lg font-semibold rounded-md hover:bg-gray-700 transition"
            >
                Limpiar Filtros
            </button>
        </div>
    </div>

    <!-- Tabla de Historial -->
    @if($cargando)
        <div class="flex justify-center items-center my-8">
            <svg class="animate-spin h-10 w-10 text-[#1E3A8A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md">
            <h3 class="text-2xl font-medium text-gray-900 mb-4 p-4 border-b">Historial de Movimientos de Inscripciones</h3>

            @if($historialInscripciones && $historialInscripciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 text-lg">
                        <thead class="bg-[#1E3A8A] text-white">
                            <tr>
                                <th class="py-3 px-4 text-left">Fecha/Hora</th>
                                <th class="py-3 px-4 text-left">Alumno</th>
                                <th class="py-3 px-4 text-left">Matrícula</th>
                                <th class="py-3 px-4 text-left">Grupo</th>
                                <th class="py-3 px-4 text-left">Acción</th>
                                <th class="py-3 px-4 text-left">Estatus Anterior</th>
                                <th class="py-3 px-4 text-left">Estatus Nuevo</th>
                                <th class="py-3 px-4 text-left">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($historialInscripciones as $historial)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="text-sm">
                                            {{ \Carbon\Carbon::parse($historial->created_at)->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($historial->created_at)->format('H:i:s') }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium">{{ $historial->alumno_apellidos }} {{ $historial->alumno_nombres }}</div>
                                    </td>
                                    <td class="py-3 px-4">{{ $historial->alumno_matricula }}</td>
                                    <td class="py-3 px-4">
                                        {{ $historial->grupo_grado }}°{{ $historial->grupo_letra }} (Gen. {{ $historial->grupo_generacion }})
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-sm rounded-full 
                                            {{ $historial->accion === 'created' ? 'bg-green-100 text-green-800' : 
                                               ($historial->accion === 'updated' ? 'bg-blue-100 text-blue-800' : 
                                                'bg-red-100 text-red-800') }}">
                                            {{ $historial->accion === 'created' ? 'Inscripción' : 
                                               ($historial->accion === 'updated' ? 'Cambio de Estatus' : 'Eliminación') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($historial->estatus_anterior)
                                            @php
                                                $estatusAnteriorDisplay = '';
                                                $estatusAnteriorClass = '';
                                                switch($historial->estatus_anterior) {
                                                    case 'vigente':
                                                        $estatusAnteriorDisplay = 'Activo';
                                                        $estatusAnteriorClass = 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'baja':
                                                        $estatusAnteriorDisplay = 'Inactivo';
                                                        $estatusAnteriorClass = 'bg-red-100 text-red-800';
                                                        break;
                                                    case 'egresado':
                                                        $estatusAnteriorDisplay = 'Egresado';
                                                        $estatusAnteriorClass = 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    default:
                                                        $estatusAnteriorDisplay = ucfirst($historial->estatus_anterior);
                                                        $estatusAnteriorClass = 'bg-gray-100 text-gray-800';
                                                }
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $estatusAnteriorClass }}">
                                                {{ $estatusAnteriorDisplay }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($historial->estatus_nuevo)
                                            @php
                                                $estatusNuevoDisplay = '';
                                                $estatusNuevoClass = '';
                                                switch($historial->estatus_nuevo) {
                                                    case 'vigente':
                                                        $estatusNuevoDisplay = 'Activo';
                                                        $estatusNuevoClass = 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'baja':
                                                        $estatusNuevoDisplay = 'Inactivo';
                                                        $estatusNuevoClass = 'bg-red-100 text-red-800';
                                                        break;
                                                    case 'egresado':
                                                        $estatusNuevoDisplay = 'Egresado';
                                                        $estatusNuevoClass = 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    default:
                                                        $estatusNuevoDisplay = ucfirst($historial->estatus_nuevo);
                                                        $estatusNuevoClass = 'bg-gray-100 text-gray-800';
                                                }
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $estatusNuevoClass }}">
                                                {{ $estatusNuevoDisplay }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">{{ $historial->usuario_nombre }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="p-4">
                    {{ $historialInscripciones->links() }}
                </div>
            @else
                <div class="p-4">
                    <div class="bg-yellow-50 p-4 rounded-md">
                        <p class="text-yellow-700 text-lg">No se encontraron movimientos de inscripciones en el período y filtros seleccionados.</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>