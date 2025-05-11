<div class="p-6 space-y-6">

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
            <div>
            <!-- Aqui debe de haber un selector para los resportes entre maestros y alumnos -->
            <button wire:click="cambioModo" class="px-4 py-2 bg-orange-700 text-black rounded">
                Cambiar reporte
                </button>
            <label class="block mb-1">Modo de reporte:
                @if ($modo)
                    Alumnos
                @else
                    Maestros

                @endif
                </label>
            </div>

        <!-- Select básico -->


        <!-- Búsqueda de maestros sin materias -->

        <div class="bg-white p-4 shadow rounded">
            <h3 class="text-lg font-semibold mb-2">Maestros sin materias</h3>
            <ul>
                @foreach($maestros_sin_materias as $maestro)
                    <li>{{ $maestro['nombre_completo'] }} - {{ $maestro['curp'] }}</li>
                @endforeach
            </ul>
        </div>
        {{-- Filtros de búsqueda --}}
            <div class="bg-white p-4 shadow rounded">
                <h3 class="text-lg font-semibold mb-4">Reportes de la Institución</h3>

                <div class="grid grid-cols-4 gap-4 mb-4">
                    @if ($modo)
                    <div>

                        <label class="block mb-1">Grado</label>
                        <select wire:model="grado" class="w-full border rounded p-2">
                            <option value="">Todos</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">Grupo</label>
                        <select wire:model="letra" class="w-full border rounded p-2">
                            <option value="">Todos</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="F">F</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">Generación</label>
                        <select wire:model="generacion" class="w-full border rounded p-2">
                            <option value="">Todos</option>
                            @foreach ($generaciones as $gen)
                                <option value="{{ $gen }}">{{ $gen }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div>
                        <label class="block mb-1">Materia</label>
                        <select wire:model.defer="materia_id" wire:change="actualizarMateriaId" class="w-full border rounded p-2">
                            <option value="">Todos</option>
                            @foreach ($materias as $materia)
                                <option value="{{ $materia['id'] }}">{{ $materia['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">Maestro</label>
                        <select wire:model="maestro_id">
                            @foreach($maestros_basico as $maestro)
                                <option value="{{ $maestro['id'] }}">{{ $maestro['nombre_completo'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="text-right">
                <button
                @if ($modo)
                    wire:click="controlEscolar"
                @else
                    wire:click="buscarReporte"
                @endif
                    class="bg-black text-white px-4 py-2 rounded inline-flex items-center">
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
                <button wire:click="cambiarVista('reporte_de_inasistencia')" class="{{ $vista === 'reporte_de_inasistencia' ? 'font-semibold border-b-2 border-black' : '' }}">
                    Reporte de inasistencia
                </button>
            </div>

            <div class="flex justify-between items-center mb-2">
                <button wire:click="actualizar" class="flex items-center bg-gray-100 px-3 py-1 rounded">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4v6h6M20 20v-6h-6M4 20l6-6M20 4l-6 6"/>
                    </svg>
                    Actualizar
                </button>
                <button class="flex items-center bg-gray-100 px-3 py-1 rounded">
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
                @if (!$modo && $resultados && $resultados->count() > 0)
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
                    @if (!$modo && $resultados && $resultados->count() > 0)
                    <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Nombre del Maestro</th>
                                <th class="border px-4 py-2">Materia</th>
                                <th class="border px-4 py-2">Grupo</th>
                                <th class="border px-4 py-2">Día</th>
                                <th class="border px-4 py-2">Hora Inicio</th>
                                <th class="border px-4 py-2">Hora Fin</th>
                                <th class="border px-4 py-2 text-red-600">Cruce</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($maestros_con_materias as $index => $materia)
                                <tr class="{{ $materia['cruce'] ? 'bg-red-100' : 'hover:bg-gray-50' }}">
                                    <td class="border px-4 py-2">{{ $index + 1 }}</td>
                                    <td class="border px-4 py-2">{{ $materia['maestro'] }}</td>
                                    <td class="border px-4 py-2">{{ $materia['materia'] }}</td>
                                    <td class="border px-4 py-2">{{ $materia['grupo'] }}</td>
                                    <td class="border px-4 py-2">{{ $materia['dia'] }}</td>
                                    <td class="border px-4 py-2">{{ $materia['hora_inicio'] }}</td>
                                    <td class="border px-4 py-2">{{ $materia['hora_fin'] }}</td>
                                    <td class="border px-4 py-2 text-center font-bold">
                                        @if($materia['cruce'])
                                            ❌
                                        @else
                                            ✅
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
            @if (!$modo && $resultados && $resultados->count() > 0)
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Nombre de la Materia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultados as $index => $materia)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="border px-4 py-2">{{ $materia->nombre }}</td>
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

            @if (!$modo && $resultados && $resultados->count() > 0)
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">#</th>
                                <th class="border px-4 py-2">Grupo</th>
                                <th class="border px-4 py-2">Materia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultados as $index => $grupo)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="border px-4 py-2">{{ $grupo->grupo_nombre }}</td>
                                    <td class="border px-4 py-2">{{ $grupo->materia_nombre }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    Todos los grupos tienen al menos un maestro asignado.
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
            @else
                <div class="p-8 text-center text-gray-500">
                    No se encontraron resultados.
                </div>
            @endif
        </div>
</div>
