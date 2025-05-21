<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de alerta -->
            @if(isset($mensaje) && $mensaje)
            <div class="mb-4 p-4 rounded-md {{ $tipoMensaje === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $mensaje }}
            </div>
            @endif

            @if(isset($alumno) && $alumno)
                <!-- Cabecera con título y botones -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Información del Alumno</h1>
                    <div class="flex space-x-3">
                        @if(!$editando)
                            <button wire:click="activarEdicion" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                                Editar
                            </button>
                            <button wire:click="confirmarEliminacion" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                                Eliminar
                            </button>
                        @else
                            <button wire:click="guardarCambios" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Guardar
                            </button>
                            <button wire:click="cancelarEdicion" class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i>
                                Cancelar
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ficha del alumno -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <!-- Encabezado de la ficha -->
                    <div class="p-6 bg-[#1E3A8A] text-white">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-20 h-20 bg-white rounded-full flex items-center justify-center text-[#1E3A8A] border-4 border-white mr-4">
                                <span class="text-2xl font-bold">{{ substr($alumno->nombres, 0, 1) }}{{ substr($alumno->apellidos, 0, 1) }}</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold">{{ $alumno->nombres }} {{ $alumno->apellidos }}</h2>
                                <p class="text-lg opacity-90">Matrícula: {{ $alumno->matricula }}</p>
                                <div class="mt-1 inline-block px-2 py-1
                                    {{ $alumno->estatus === 'vigente' ? 'bg-green-500' :
                                      ($alumno->estatus === 'egresado' ? 'bg-blue-500' : 'bg-red-500') }}
                                    text-xs font-semibold rounded-full">
                                    {{ ucfirst($alumno->estatus) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido de la ficha -->
                    @if(!$editando)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Datos Personales -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Datos Personales</h3>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">Nombres</p>
                                    <p class="text-base font-medium">{{ $alumno->nombres }}</p>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">Apellidos</p>
                                    <p class="text-base font-medium">{{ $alumno->apellidos }}</p>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">CURP</p>
                                    <p class="text-base font-medium">{{ $alumno->curp }}</p>
                                </div>
                            </div>

                            <!-- Información Académica y Contacto -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información Académica y Contacto</h3>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">Teléfono de Contacto</p>
                                    <p class="text-base font-medium">{{ $alumno->contacto }}</p>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">Nombre del Tutor</p>
                                    <p class="text-base font-medium">{{ $alumno->tutor }}</p>
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">Grupo Actual</p>
                                    <p class="text-base font-medium">
                                        @if(isset($grupo) && $grupo)
                                            {{ $grupo->grado }}°{{ $grupo->letra }} (Gen. {{ $grupo->generacion }})
                                        @else
                                            No asignado
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Formulario de edición -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Datos Personales -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Datos Personales</h3>

                                <div class="mb-4">
                                    <label for="nombres" class="block text-sm text-gray-500">Nombres</label>
                                    <input type="text" id="nombres" wire:model.live="alumnoData.nombres" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('alumnoData.nombres') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="apellidos" class="block text-sm text-gray-500">Apellidos</label>
                                    <input type="text" id="apellidos" wire:model.live="alumnoData.apellidos" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('alumnoData.apellidos') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="matricula" class="block text-sm text-gray-500">Matrícula</label>
                                    <input type="text" id="matricula" wire:model.live="alumnoData.matricula" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('alumnoData.matricula') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="curp" class="block text-sm text-gray-500">CURP</label>
                                    <input type="text" id="curp" wire:model.live="alumnoData.curp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('alumnoData.curp') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Información Académica y Contacto -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información Académica y Contacto</h3>

                                <div class="mb-4">
                                    <label for="contacto" class="block text-sm text-gray-500">Teléfono de Contacto</label>
                                    <input type="text" id="contacto" wire:model.live="alumnoData.contacto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('alumnoData.contacto') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="tutor" class="block text-sm text-gray-500">Nombre del Tutor</label>
                                    <input type="text" id="tutor" wire:model.live="alumnoData.tutor" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('alumnoData.tutor') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="estatus" class="block text-sm text-gray-500">Estatus</label>
                                    <select id="estatus" wire:model.live="alumnoData.estatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="vigente">Vigente</option>
                                        <option value="egresado">Egresado</option>
                                        <option value="baja">Baja</option>
                                    </select>
                                    @error('alumnoData.estatus') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">Grupo Actual</p>
                                    <p class="text-base font-medium">
                                        @if(isset($grupo) && $grupo)
                                            {{ $grupo->grado }}°{{ $grupo->letra }} (Gen. {{ $grupo->generacion }})
                                        @else
                                            No asignado
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">El cambio de grupo debe realizarse desde la sección de inscripciones.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Sección de Calificaciones -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <div class="p-4 bg-[#1E3A8A] text-white">
                        <h3 class="text-lg font-semibold">Calificaciones</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Materia</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Unidad 1</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Unidad 2</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Unidad 3</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Unidad 4</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Promedio</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($calificaciones as $materia)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $materia['nombre'] }}</td>

                                        @for($unidad = 1; $unidad <= 4; $unidad++)
                                            <td class="px-4 py-3 text-center text-sm {{ isset($materia['unidades'][$unidad]) && $materia['unidades'][$unidad] && $materia['unidades'][$unidad] < 6 ? 'text-red-600' : 'text-gray-600' }}">
                                                {{ isset($materia['unidades'][$unidad]) && $materia['unidades'][$unidad] ? $materia['unidades'][$unidad] : '-' }}
                                            </td>
                                        @endfor

                                        <td class="px-4 py-3 text-center text-sm {{ isset($materia['promedio']) && $materia['promedio'] && $materia['promedio'] < 6 ? 'text-red-600 font-semibold' : 'text-gray-800 font-semibold' }}">
                                            {{ isset($materia['promedio']) && $materia['promedio'] ? $materia['promedio'] : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-sm text-gray-500 text-center">
                                            No hay calificaciones registradas para este alumno.
                                        </td>
                                    </tr>
                                @endforelse

                                <!-- Promedio general -->
                                @if(count($calificaciones) > 0 && isset($promedioGeneral) && $promedioGeneral)
                                    <tr class="bg-gray-50">
                                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-700">Promedio General:</td>
                                        <td colspan="4"></td>
                                        <td class="px-4 py-3 text-center text-sm font-bold {{ $promedioGeneral < 6 ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $promedioGeneral }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('inscritos') }}" class="px-5 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                        Volver a la lista
                    </a>
                </div>

                <!-- Modal de confirmación de eliminación -->
                @if($mostrarModalEliminar)
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                        <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar eliminación</h3>
                            <p class="text-gray-700 mb-4">¿Estás seguro que deseas dar de baja a este alumno? El alumno aparecerá como "Baja" en el sistema.</p>
                            <div class="flex justify-end space-x-3">
                                <button wire:click="cancelarEliminacion" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md">Cancelar</button>
                                <button wire:click="eliminarAlumno" class="bg-red-600 text-white px-4 py-2 rounded-md">Dar de baja</button>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Mensaje si no se encuentra el alumno -->
                <div class="bg-yellow-50 p-4 rounded-md mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Información no disponible</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>No se encontró información del alumno solicitado o no se ha especificado un ID de alumno.</p>
                            </div>
                            <div class="mt-4">
                                <div class="-mx-2 -my-1.5 flex">
                                    <a href="{{ route('inscritos') }}" class="px-3 py-2 rounded-md text-sm font-medium text-yellow-800 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        Volver a la lista de alumnos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</div>