<div class="p-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Registro de Calificaciones</h2>

    <!-- Mensajes de alerta -->
    @if ($mensaje)
        <div class="mb-4 p-4 rounded-md {{ $tipoMensaje === 'success' ? 'bg-green-100 text-green-800' : ($tipoMensaje === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
            {{ $mensaje }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Selección de grupo -->
        <div>
            <label for="grupo" class="block text-sm font-medium text-gray-700 mb-2">Selecciona un grupo:</label>
            <select id="grupo" wire:model.live="grupoSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">-- Seleccionar grupo --</option>
                @foreach ($gruposImpartidos as $grupo)
                    <option value="{{ $grupo['id'] }}">{{ $grupo['nombre'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Selección de materia -->
        <div>
            <label for="materia" class="block text-sm font-medium text-gray-700 mb-2">Selecciona una materia:</label>
            <select id="materia" wire:model.live="materiaSeleccionada" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" {{ count($materiasImpartidas) ? '' : 'disabled' }}>
                <option value="">-- Seleccionar materia --</option>
                @foreach ($materiasImpartidas as $materia)
                    <option value="{{ $materia['id'] }}">{{ $materia['nombre'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($cargando)
        <div class="flex justify-center items-center my-8">
            <svg class="animate-spin h-10 w-10 text-[#1E3A8A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    @elseif(count($alumnos) > 0 && $materiaSeleccionada)
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Lista de Alumnos y Calificaciones
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#1E3A8A]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Alumno</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Unidad 1</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Unidad 2</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Unidad 3</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Unidad 4</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Promedio</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($alumnos as $alumno)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $alumno['apellidos'] }} {{ $alumno['nombres'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Matrícula: {{ $alumno['matricula'] }}
                                    </div>
                                </td>

                                @for ($unidad = 1; $unidad <= 4; $unidad++)
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input
                                            type="number"
                                            step="0.1"
                                            min="0"
                                            max="10"
                                            wire:model="calificaciones.{{ $alumno['id'] }}.{{ $unidad }}.valor"
                                            wire:change="actualizarCalificacion({{ $alumno['id'] }}, {{ $unidad }}, $event.target.value)"
                                            class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm
                                                  @if(isset($errores['alumno_'.$alumno['id'].'_unidad_'.$unidad])) border-red-500 @endif"
                                        >
                                        @if(isset($errores['alumno_'.$alumno['id'].'_unidad_'.$unidad]))
                                            <div class="text-xs text-red-600 mt-1">
                                                {{ $errores['alumno_'.$alumno['id'].'_unidad_'.$unidad] }}
                                            </div>
                                        @endif
                                    </td>
                                @endfor

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $promedio = $this->calcularPromedioAlumno($alumno['id']);
                                    @endphp

                                    @if($promedio !== null)
                                        <span class="text-sm font-medium {{ $promedio < 6 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $promedio }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <button
                    wire:click="guardarTodasLasCalificaciones"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-[#1E3A8A] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-900 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                >
                    Guardar todas las calificaciones
                </button>
            </div>
        </div>
    @elseif($grupoSeleccionado && count($materiasImpartidas) === 0)
        <div class="mt-8 bg-yellow-50 p-4 rounded-md">
            <p class="text-yellow-700">No impartes ninguna materia en este grupo.</p>
        </div>
    @elseif($grupoSeleccionado)
        <div class="mt-8 bg-blue-50 p-4 rounded-md">
            <p class="text-blue-700">Selecciona una materia para ver la lista de alumnos.</p>
        </div>
    @else
        <div class="mt-8 bg-blue-50 p-4 rounded-md">
            <p class="text-blue-700">Selecciona un grupo y una materia para empezar a registrar calificaciones.</p>
        </div>
    @endif
</div>
