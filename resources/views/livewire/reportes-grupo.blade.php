<div class="p-6 space-y-6">
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

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
            <p class="text-2xl font-bold">{{ $materiasSinMaestro }}</p>
        </div>
        <div class="bg-white p-4 shadow rounded text-center">
            <h2 class="text-gray-600 text-sm">Grupos sin Maestro</h2>
            <p class="text-2xl font-bold">{{ $gruposSinMaestro }}</p>
        </div>
    </div>

    {{-- Filtros de búsqueda --}}
    <div class="bg-white p-4 shadow rounded">
        <h3 class="text-lg font-semibold mb-4">Reportes de la Institución</h3>
        <div class="grid grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block mb-1">Grado</label>
                <select wire:model="grado" class="w-full border rounded p-2 @error('grado') border-red-500 @enderror">
                    <option value="">Todos</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
                @error('grado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-1">Grupo</label>
                <select wire:model="letra" class="w-full border rounded p-2 @error('letra') border-red-500 @enderror">
                    <option value="">Todos</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                </select>
                @error('letra') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-1">Generación</label>
                <select wire:model="generacion" class="w-full border rounded p-2 @error('generacion') border-red-500 @enderror">
                    <option value="">Todos</option>
                    @foreach ($generaciones as $gen)
                        <option value="{{ $gen }}">{{ $gen }}</option>
                    @endforeach
                </select>
                @error('generacion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-1">Materia</label>
                <select wire:model.defer="materia_id" wire:change="actualizarMateriaId"
                    class="w-full border rounded p-2 @error('materia_id') border-red-500 @enderror">
                    <option value="">Todos</option>
                    @foreach ($materias as $materia)
                        <option value="{{ $materia['id'] }}">{{ $materia['nombre'] }}</option>
                    @endforeach
                </select>
                @error('materia_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block mb-1">Maestro</label>
                <select wire:model="maestro_id" class="w-full border rounded p-2 @error('maestro_id') border-red-500 @enderror">
                    <option value="">Todos</option>
                    @foreach($maestros_basico as $maestro)
                        <option value="{{ $maestro['id'] }}">{{ $maestro['nombre_completo'] }}</option>
                    @endforeach
                </select>
                @error('maestro_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="text-right">
            <button wire:click="buscarReporte" class="bg-black text-white px-4 py-2 rounded inline-flex items-center">
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
                            <button wire:click="cambiarVista('maestros')" class="{{ $vista === 'maestros' ? 'font-semibold border-b-2 border-black' : '' }}">
                                Maestros
                            </button>
                            <button wire:click="cambiarVista('maestro_por_materias')" class="{{ $vista === 'maestro_por_materias' ? 'font-semibold border-b-2 border-black' : '' }}">
                            Maestro por Materias
                            </button>
                            <button wire:click="cambiarVista('materias_sin_maestro')" class="{{ $vista === 'materias_sin_maestro' ? 'font-semibold border-b-2 border-black' : '' }}">
                                Materias sin Maestro
                            </button>
                            <button wire:click="cambiarVista('grupos_sin_maestro')" class="{{ $vista === 'grupos_sin_maestro' ? 'font-semibold border-b-2 border-black' : '' }}">
                                Grupos sin Maestro
                            </button>
                            {{-- Tabs de visualización solo para modo Alumno --}}
                        <button wire:click="cambiarVista('alumnos')" class="{{ $vista === 'alumnos' ? 'font-semibold border-b-2 border-black' : '' }}">
                            Alumnos
                        </button>
                        </div>

                        <div class="flex justify-between items-center mb-2">
                            <!-- Botón Actualizar -->
                            <button wire:click="actualizar" class="flex items-center bg-gray-100 px-3 py-1 rounded hover:bg-gray-200 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M4 4v6h6M20 20v-6h-6M4 20l6-6M20 4l-6 6"/>
                                </svg>
                                Actualizar
                            </button>

                            <!-- Botón Exportar PDF -->
                            <button wire:click="exportarPDF" class="flex items-center bg-gray-100 px-3 py-1 rounded hover:bg-gray-200 transition">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 20l9-5-9-5-9 5 9 5z"/>
                                    <path d="M12 12V4l9 5-9 5-9-5 9-5z"/>
                                </svg>
                                Exportar PDF
                            </button>
                        </div>


                        {{-- Mostrar contenido basado en la vista seleccionada --}}
            @if ($vista === 'maestros')
                {{-- Tabla de Maestros --}}
                @if ($resultados && $resultados->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">CURP</th>
                                <th class="border px-4 py-2">Apellidos</th>
                                <th class="border px-4 py-2">Nombres</th>
                                <th class="border px-4 py-2">Teléfono</th>
                                <th class="border px-4 py-2">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultados as $index => $maestro)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-4 py-2">{{ $maestro->curp }}</td>
                                <td class="border px-4 py-2">{{ $maestro->apellidos }}</td>
                                <td class="border px-4 py-2">{{ $maestro->name }}</td>
                                <td class="border px-4 py-2">{{ $maestro->telefono }}</td>
                                <td class="border px-4 py-2">{{ $maestro->email }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-gray-500">
                    No se encontraron maestros con los criterios seleccionados.
                </div>
                @endif

                @elseif ($vista === 'maestro_por_materias')
                {{-- Tab de Materias por Maestro --}}
                @if ($resultados && count($resultados) > 0)
                    <div class="overflow-x-auto mt-4">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="border px-4 py-2">#</th>
                                    <th class="border px-4 py-2">Nombre Completo</th>
                                    <th class="border px-4 py-2">Materia</th>
                                    <th class="border px-4 py-2">Grupo</th>
                                    <th class="border px-4 py-2">Día</th>
                                    <th class="border px-4 py-2">Hora</th>
                                    <th class="border px-4 py-2">Hora Inicio</th>
                                    <th class="border px-4 py-2 text-red-600">Cruce</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($resultados as $index => $materia)
                            <tr class="{{ $materia['cruce'] ? 'bg-red-100' : 'hover:bg-gray-50' }}">
                                <td class="border px-4 py-2">{{ $index + 1 }}</td>
                                <td class="border px-4 py-2">{{ $materia['maestro'] }}</td>
                                <td class="border px-4 py-2">{{ $materia['materia'] }}</td>
                                <td class="border px-4 py-2">{{ $materia['grupo'] }}</td>
                                <td class="border px-4 py-2">{{ $materia['dia'] }}</td>
                                <td class="border px-4 py-2">{{ $materia['hora'] }}</td>
                                <td class="border px-4 py-2">{{ $materia['hora_inicio'] }}</td>
                                <td class="border px-4 py-2 text-center font-bold">
                                    @if($materia['cruce'])
                                                            Hay cruce
                                                        @else
                                                            Bien
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        No hay datos disponibles con los filtros seleccionados.
                    </div>
                @endif

            @elseif ($vista === 'materias_sin_maestro')
                                {{-- Tab de Materias Sin Maestro --}}
                                @if ($resultados && count($resultados) > 0)
                    <div class="overflow-x-auto mt-4">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="border px-4 py-2">#</th>
                                    <th class="px-4 py-2 text-left">Materia</th>
                                    <th class="px-4 py-2 text-left">Grupo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resultados as $index => $materia)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2">{{ $materia['materia'] }}</td>
                                        <td class="px-4 py-2">{{ $materia['grupo'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        No hay materias sin maestro asignado.
                    </div>
                @endif
            @elseif ($vista === 'grupos_sin_maestro')
            {{-- Tab de Grupos sin Maestro --}}
            @if ($resultados && count($resultados) > 0)
                <div class="overflow-x-auto mt-4">
                    <table class="table-auto w-full border-collapse border">
                    <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Grupo</th>
                                <th class="border px-4 py-2">Materia sin Maestro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $contador = 1; @endphp

                            @foreach ($resultados->groupBy('grupo_nombre') as $grupo => $materias)
                                @foreach ($materias as $index => $materia)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                        <td class="border px-4 py-2">{{ $contador++ }}</td>
                                        <td class="border px-4 py-2">{{ $grupo }}</td>
                                        <td class="border px-4 py-2">{{ $materia->materia_nombre }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    Todos los grupos tienen al menos un maestro asignado.
                </div>
            @endif
            @elseif ($vista === 'alumnos')
            {{-- Tab de Alumnos --}}
            @if ($resultados && $resultados->count() > 0)
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Matrícula</th>
                                <th class="border px-4 py-2">Apellidos</th>
                                <th class="border px-4 py-2">Nombre</th>
                                <th class="border px-4 py-2">Grado Y Grupo</th>
                                <th class="border px-4 py-2">Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultados as $index => $alumno)
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
                    No se encontraron alumnos con los criterios seleccionados.
                </div>
            @endif
            @endif
            @if($modo && $reporteInasistencia == false && $resultados && $resultados->count()>0)
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
                                    {{ $alumno->inscritos->where('grupo.grado', $grado)->first()->grupo->grado ?? $alumno->inscritos->first()->grupo->grado}}
                                    {{ $alumno->inscritos->first()->grupo->letra ?? '' }}
                                </td>
                                <td class="border px-4 py-2">{{ $alumno->estatus}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif ($modo && $reporteInasistencia && $resultados && count($resultados) > 0)
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Matrícula</th>
                                <th class="border px-4 py-2">Apellidos</th>
                                <th class="border px-4 py-2">Nombres</th>
                                <th class="border px-4 py-2">Grado/Grupo</th>
                                <th class="border px-4 py-2">Faltas Totales</th>
                                <th class="border px-4 py-2">Clases Totales</th>
                                <th class="border px-4 py-2">Porcentaje faltas</th>
                            </tr>
                        </thead>
                        <tbody>
        <tr class="hover:bg-gray-50">
            <td class="border px-4 py-2">{{ $resultados['matricula'] }}</td>
            <td class="border px-4 py-2">{{ $resultados['apellidos'] }}</td>
            <td class="border px-4 py-2">{{ $resultados['nombre'] }}</td>
            <td class="border px-4 py-2">{{ $resultados['grado'] }} {{ $resultados['grupo'] }}</td>
            <td class="border px-4 py-2">{{ $resultados['faltas_totales'] }}</td>
            <td class="border px-4 py-2">{{ $resultados['clases_totales'] }}</td>
            <td class="border px-4 py-2">{{ $resultados['porcentaje_faltas'] }}%</td>
        </tr>
    </tbody>
                    </table>
                </div>
            @elseif ($modo)
                <div class="p-8 text-center text-gray-500">
                    No se encontraron resultados.
                </div>
            @endif
        </div>
                    </div>


</div>
