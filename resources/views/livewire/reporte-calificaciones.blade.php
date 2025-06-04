<div class="p-6 text-[17px]">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Reporte de Modificaciones de Calificaciones</h2>

    @if($mensaje)
        <div class="mb-4 p-4 rounded-md {{ $tipoMensaje === 'success' ? 'bg-green-100 text-green-800' : ($tipoMensaje === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
            {{ $mensaje }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="mb-6 p-4 bg-white rounded-lg shadow-md">
        <h3 class="text-xl font-medium text-gray-700 mb-4">Filtros de Búsqueda</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Materia -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-2">Materia:</label>
                <select wire:model="materiaFiltro" class="w-full pl-3 pr-10 py-2 text-lg border-gray-300 rounded-md">
                    <option value="">Todas las materias</option>
                    @foreach($materiasDisponibles as $materia)
                        <option value="{{ $materia['id'] }}">{{ $materia['nombre'] }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Unidad -->
            <div>
                <label class="block text-lg font-medium text-gray-700 mb-2">Unidad:</label>
                <select wire:model="unidadFiltro" class="w-full pl-3 pr-10 py-2 text-lg border-gray-300 rounded-md">
                    <option value="">Todas las unidades</option>
                    <option value="1">Unidad 1</option>
                    <option value="2">Unidad 2</option>
                    <option value="3">Unidad 3</option>
                    <option value="4">Unidad 4</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Búsqueda por Matrícula/Alumno con Autocompletado -->
            <div class="relative">
                <label class="block text-lg font-medium text-gray-700 mb-2">Buscar Alumno:</label>
                <input 
                    type="text" 
                    wire:model.live="matriculaBusqueda"
                    placeholder="Matrícula, nombre o apellido..."
                    class="w-full pl-3 pr-3 py-2 text-lg border-gray-300 rounded-md"
                    autocomplete="off"
                >
                
                <!-- Sugerencias de autocompletado -->
                @if($mostrarSugerencias && count($sugerenciasAlumnos) > 0)
                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                        @foreach($sugerenciasAlumnos as $alumno)
                            <div 
                                wire:click="seleccionarAlumno('{{ $alumno['matricula'] }}', '{{ $alumno['nombres'] }} {{ $alumno['apellidos'] }}')"
                                class="px-4 py-3 cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-b-0"
                            >
                                <div class="font-medium text-gray-900">{{ $alumno['matricula'] }}</div>
                                <div class="text-sm text-gray-600">{{ $alumno['nombres'] }} {{ $alumno['apellidos'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Botón para limpiar búsqueda -->
                @if($matriculaBusqueda)
                    <button 
                        wire:click="limpiarBusqueda"
                        class="absolute right-2 top-10 text-gray-400 hover:text-gray-600"
                        type="button"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>

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
            <h3 class="text-2xl font-medium text-gray-900 mb-4 p-4 border-b">Historial de Modificaciones de Calificaciones</h3>

            @if($historialesCalificaciones && $historialesCalificaciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 text-lg">
                        <thead class="bg-[#1E3A8A] text-white">
                            <tr>
                                <th class="py-3 px-4 text-left">Fecha/Hora</th>
                                <th class="py-3 px-4 text-left">Alumno</th>
                                <th class="py-3 px-4 text-left">Matrícula</th>
                                <th class="py-3 px-4 text-left">Materia</th>
                                <th class="py-3 px-4 text-left">Unidad</th>
                                <th class="py-3 px-4 text-left">Calificación Anterior</th>
                                <th class="py-3 px-4 text-left">Calificación Nueva</th>
                                <th class="py-3 px-4 text-left">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($historialesCalificaciones as $historial)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="text-sm">
                                            {{ \Carbon\Carbon::parse($historial->created_at)->utc()->format('d/m/Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($historial->created_at)->utc()->format('H:i:s') }} UTC
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium">
                                            @if(isset($historial->alumno_info))
                                                {{ $historial->alumno_info['apellidos'] ?? '' }} {{ $historial->alumno_info['nombres'] ?? '' }}
                                            @else
                                                <span class="text-gray-400">Sin datos</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if(isset($historial->alumno_info))
                                            {{ $historial->alumno_info['matricula'] ?? 'N/A' }}
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if(isset($historial->materia_info))
                                            {{ $historial->materia_info['nombre'] ?? 'Sin datos' }}
                                        @else
                                            <span class="text-gray-400">Sin datos</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if(isset($historial->unidad_info))
                                            Unidad {{ $historial->unidad_info }}
                                        @else
                                            <span class="text-gray-400">Sin datos</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $calificacionAnterior = $historial->old_values['calificacion'] ?? null;
                                        @endphp
                                        @if($calificacionAnterior !== null)
                                            <span class="font-bold text-lg">{{ number_format($calificacionAnterior, 1) }}</span>
                                        @else
                                            <span class="text-gray-400">Sin calificación</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $calificacionNueva = $historial->new_values['calificacion'] ?? null;
                                        @endphp
                                        @if($calificacionNueva !== null)
                                            <span class="font-bold text-lg">{{ number_format($calificacionNueva, 1) }}</span>
                                        @else
                                            <span class="text-gray-400">Sin calificación</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        {{ $historial->user->name ?? 'Sistema' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="p-4">
                    {{ $historialesCalificaciones->links() }}
                </div>
            @else
                <div class="p-4">
                    <div class="bg-yellow-50 p-4 rounded-md">
                        <p class="text-yellow-700 text-lg">No se encontraron modificaciones de calificaciones en el período y filtros seleccionados.</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>