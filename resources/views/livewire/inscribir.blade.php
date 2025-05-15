<div>
    <br>
    <div class="p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Inscripción de Nuevo Alumno</h2>

        <!-- Mensajes de alerta -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 {{ session('message-type') === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-md">
                <span class="text-lg">{{ session('message') }}</span>
            </div>
        @endif

        <!-- Formulario de inscripción -->
        <form wire:submit.prevent="registrarAlumno">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Datos personales -->
                <div class="space-y-4">
                    <h3 class="text-xl font-medium text-gray-700 border-b pb-2">Datos Personales</h3>

                    <!-- Matrícula -->
                    <div>
                        <label for="matricula" class="block text-lg font-medium text-gray-700">Matrícula</label>
                        <input type="text" id="matricula" wire:model="alumno.matricula"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                            maxlength="10" placeholder="Ej: A12345678">
                        @error('alumno.matricula')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Nombres -->
                    <div>
                        <label for="nombres" class="block text-lg font-medium text-gray-700">Nombres</label>
                        <input type="text" id="nombres" wire:model="alumno.nombres"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                            placeholder="Ej: Juan Carlos">
                        @error('alumno.nombres')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Apellidos -->
                    <div>
                        <label for="apellidos" class="block text-lg font-medium text-gray-700">Apellidos</label>
                        <input type="text" id="apellidos" wire:model="alumno.apellidos"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                            placeholder="Ej: García Pérez">
                        @error('alumno.apellidos')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- CURP -->
                    <div>
                        <label for="curp" class="block text-lg font-medium text-gray-700">CURP</label>
                        <input type="text" id="curp" wire:model="alumno.curp"
                            class="uppercase mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                            maxlength="18" placeholder="Ej: GAPE050823HDFRCR01">
                        @error('alumno.curp')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                        <span class="text-base text-gray-500">Formato: 18 caracteres alfanuméricos (obligatorio)</span>
                    </div>
                </div>

                <!-- Información académica y de contacto -->
                <div class="space-y-4">
                    <h3 class="text-xl font-medium text-gray-700 border-b pb-2">Información Académica y Contacto</h3>

                    <!-- Estatus -->
                    <div>
                        <label for="estatus" class="block text-lg font-medium text-gray-700">Estatus</label>
                        <select id="estatus" wire:model="alumno.estatus"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                            <option value="vigente">Vigente</option>
                            <option value="egresado">Egresado</option>
                            <option value="baja">Baja</option>
                        </select>
                        @error('alumno.estatus')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Contacto (teléfono) -->
                    <div>
                        <label for="contacto" class="block text-lg font-medium text-gray-700">Teléfono de Contacto</label>
                        <input type="text" id="contacto" wire:model="alumno.contacto"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                            maxlength="10" placeholder="Ej: 6121234567">
                        @error('alumno.contacto')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tutor -->
                    <div>
                        <label for="tutor" class="block text-lg font-medium text-gray-700">Nombre del Tutor</label>
                        <input type="text" id="tutor" wire:model="alumno.tutor"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg"
                            placeholder="Ej: María López Gómez">
                        @error('alumno.tutor')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Selección de Grupo -->
                    <div>
                        <label for="grupo" class="block text-lg font-medium text-gray-700">Inscribir en Grupo</label>
                        <select id="grupo" wire:model="grupoSeleccionado"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg">
                            <option value="">-- Seleccionar grupo --</option>
                            @foreach ($grupos as $grupo)
                                <option value="{{ $grupo['id'] }}">{{ $grupo['grado'] }}°{{ $grupo['letra'] }} (Generación {{ $grupo['generacion'] }})</option>
                            @endforeach
                        </select>
                        @error('grupoSeleccionado')
                            <span class="text-red-600 text-lg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-8 flex justify-end space-x-3">
                <button type="button" wire:click="limpiarFormulario"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-lg font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancelar
                </button>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-[#1E3A8A] hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Inscribir Alumno
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de alumnos recientes (opcional) -->
    @if(count($alumnosRecientes) > 0)
    <div class="mt-6 p-6 bg-white rounded-lg shadow-md">
        <h3 class="text-lg font-medium text-gray-800 mb-4">Alumnos Inscritos Recientemente</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Matrícula</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                        <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Tutor</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($alumnosRecientes as $alumno)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-lg font-medium text-gray-900">{{ $alumno['matricula'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-lg text-gray-500">{{ $alumno['apellidos'] }} {{ $alumno['nombres'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-lg text-gray-500">{{ $alumno['grupo'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-lg text-gray-500">{{ $alumno['tutor'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <br>
    @endif

    <!-- Incluir componente de mensajes de error -->
    @include('livewire.errores')
</div>
