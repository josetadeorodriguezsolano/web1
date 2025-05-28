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

    @if (session()->has('warning'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Selección de Grupo -->
    <div class="mb-6">
        <label for="grupoSeleccionado" class="block text-sm font-medium text-gray-700">Seleccionar Grupo</label>
        <div class="mt-1 flex">
            <select wire:model="grupoSeleccionado" id="grupoSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">Seleccione un grupo</option>
                @foreach ($grupos as $grupo)
                    <option value="{{ $grupo->id }}">{{ $grupo->grado }} "{{ $grupo->letra }}" "{{ $grupo->generacion }}"</option>
                @endforeach
            </select>
            <button wire:click="cargarHorario" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cargar Horario
            </button>
        </div>
    </div>

    @if ($grupoSeleccionado)
        @php
            $grupo = \App\Models\Grupo::find($grupoSeleccionado);
        @endphp
        
        @if($grupo)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Grupo:</strong> {{ $grupo->grado }} "{{ $grupo->letra }}" - <strong>Generación:</strong> {{ $grupo->generacion }}
                    <br>
                    <span class="text-xs">Mostrando materias correspondientes al grado {{ $grupo->grado }}</span>
                </p>
            </div>
        @endif

        <!-- Panel de Asignación de Clases -->
        <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Asignar Clase</h3>
            <p class="text-sm text-blue-600 mb-4">
                💡 <strong>Tip:</strong> Haz clic en cualquier celda de la tabla para seleccionar automáticamente el día y hora correspondiente.
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Día -->
                <div>
                    <label for="diaSeleccionado" class="block text-sm font-medium text-gray-700">Día</label>
                    <select wire:model="diaSeleccionado" id="diaSeleccionado" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md {{ $diaSeleccionado ? 'bg-yellow-50 border-yellow-300' : '' }}">
                        <option value="">Seleccione día</option>
                        @foreach ($diasSemana as $dia)
                            <option value="{{ $dia }}">{{ $dia }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Hora -->
                <div>
                    <label for="horaSeleccionada" class="block text-sm font-medium text-gray-700">Hora</label>
                    <select wire:model="horaSeleccionada" id="horaSeleccionada" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md {{ $horaSeleccionada ? 'bg-yellow-50 border-yellow-300' : '' }}">
                        <option value="">Seleccione hora</option>
                        @foreach ($horasClase as $index => $hora)
                            <option value="{{ $index + 1 }}">{{ $hora }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Materia -->
                <div>
                    <label for="materiaSeleccionada" class="block text-sm font-medium text-gray-700">
                        Materia 
                        @if($materias->isNotEmpty())
                            <span class="text-xs text-blue-600">
                                ({{ $materias->count() }} disponibles)
                            </span>
                        @endif
                    </label>
                    <select wire:model="materiaSeleccionada" 
                            wire:change="cargarMaestros"
                            id="materiaSeleccionada" 
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md
                                   @if($materias->isEmpty()) opacity-50 cursor-not-allowed @endif"
                            @if($materias->isEmpty()) disabled @endif>
                        <option value="">
                            @if($materias->isEmpty())
                                Selecciona un grupo primero
                            @else
                                Seleccione materia
                            @endif
                        </option>
                        @foreach($materias as $materia)
                            <option value="{{ $materia->id }}">
                                {{ $materia->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if($materias->isEmpty() && $grupoSeleccionado)
                        <p class="text-xs text-red-600 mt-1">
                            ⚠️ No hay materias registradas para este grado
                        </p>
                    @endif
                </div>
                
                <!-- Maestro (Enhanced with Recommendations) -->
                <div>
                    <label for="maestroSeleccionado" class="block text-sm font-medium text-gray-700">
                        Maestro
                        @if($maestroSeleccionado)
                            <span class="text-xs text-blue-600">
                                @if(isset($maestroAutoSeleccionado) && $maestroAutoSeleccionado)
                                    (Auto-seleccionado)
                                @else
                                    (Seleccionado manualmente)
                                @endif
                            </span>
                        @endif
                    </label>
                    
                    @php
                        // Filtrar maestros disponibles (no ocupados en este horario)
                        $maestrosDisponibles = collect([]);
                        if($horaSeleccionada && $diaSeleccionado && $maestros) {
                            $maestrosDisponibles = $maestros->filter(function($maestro) {
                                // Verificar que el maestro no esté ocupado en este horario
                                $ocupado = \App\Models\Horario::join('imparte', 'horarios.imparte_id', '=', 'imparte.id')
                                    ->where('horarios.hora_numero', $this->horaSeleccionada)
                                    ->where('horarios.dia_semana', $this->diaSeleccionado)
                                    ->where('imparte.maestro_id', $maestro->id)
                                    ->where('imparte.grupo_id', '!=', $this->grupoSeleccionado)
                                    ->exists();
                                return !$ocupado;
                            });
                        }
                    @endphp
                    
                    <select wire:model="maestroSeleccionado" 
                            id="maestroSelect"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md
                                   @if($maestrosDisponibles->isEmpty() || !$materiaSeleccionada || !$horaSeleccionada || !$diaSeleccionado) opacity-50 cursor-not-allowed @endif
                                   @if($maestroSeleccionado) bg-green-50 border-green-300 @endif"
                            @if($maestrosDisponibles->isEmpty() || !$materiaSeleccionada || !$horaSeleccionada || !$diaSeleccionado) disabled @endif>
                        
                        <option value="">
                            @if(!$materiaSeleccionada)
                                Selecciona una materia primero
                            @elseif(!$horaSeleccionada || !$diaSeleccionado)
                                Selecciona hora y día primero
                            @elseif($maestrosDisponibles->isEmpty())
                                No hay maestros disponibles
                            @else
                                Seleccione maestro
                            @endif
                        </option>
                        
                        @if($maestrosDisponibles->isNotEmpty() && $materiaSeleccionada && $horaSeleccionada && $diaSeleccionado)
                            @php
                                // Separar maestros con experiencia en esta materia
                                $maestrosConExperiencia = $maestrosDisponibles->filter(function($maestro) {
                                    return \App\Models\Imparte::where('maestro_id', $maestro->id)
                                        ->where('materia_id', $this->materiaSeleccionada)
                                        ->exists();
                                });
                                
                                $maestrosSinExperiencia = $maestrosDisponibles->diff($maestrosConExperiencia);
                            @endphp
                            
                            @if($maestrosConExperiencia->isNotEmpty())
                                <optgroup label="✅ Con experiencia en esta materia">
                                    @foreach($maestrosConExperiencia->sortBy('name') as $maestro)
                                        <option value="{{ $maestro->id }}" 
                                                @if($maestroSeleccionado == $maestro->id) selected @endif>
                                            {{ $maestro->name }} {{ $maestro->apellidos }}
                                            @php
                                                $horasAsignadas = \App\Models\Imparte::where('maestro_id', $maestro->id)->count();
                                            @endphp
                                            ({{ $horasAsignadas }} materias asignadas)
                                            @if(isset($maestroAutoSeleccionado) && $maestroAutoSeleccionado && isset($maestroOptimo) && $maestroOptimo && $maestroOptimo->id == $maestro->id)
                                                - ⭐ Recomendado automáticamente
                                            @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            
                            @if($maestrosSinExperiencia->isNotEmpty())
                                <optgroup label="📚 Otros maestros disponibles">
                                    @foreach($maestrosSinExperiencia->sortBy('name') as $maestro)
                                        <option value="{{ $maestro->id }}" 
                                                @if($maestroSeleccionado == $maestro->id) selected @endif>
                                            {{ $maestro->name }} {{ $maestro->apellidos }}
                                            @php
                                                $horasAsignadas = \App\Models\Imparte::where('maestro_id', $maestro->id)->count();
                                            @endphp
                                            ({{ $horasAsignadas }} materias asignadas)
                                            @if(isset($maestroAutoSeleccionado) && $maestroAutoSeleccionado && isset($maestroOptimo) && $maestroOptimo && $maestroOptimo->id == $maestro->id)
                                                - ⭐ Recomendado automáticamente
                                            @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endif
                    </select>
                    
                    <!-- Información adicional -->
                    <div class="mt-1 text-xs">
                        @if($materiaSeleccionada && ($horaSeleccionada && $diaSeleccionado) && $maestrosDisponibles->isEmpty())
                            <p class="text-red-600">
                                ⚠️ No hay maestros disponibles para este horario
                            </p>
                        @elseif($maestroSeleccionado && $maestrosDisponibles->isNotEmpty())
                            @php
                                $maestroSeleccionadoObj = $maestrosDisponibles->firstWhere('id', $maestroSeleccionado);
                                if($maestroSeleccionadoObj) {
                                    $tieneExperiencia = \App\Models\Imparte::where('maestro_id', $maestroSeleccionado)
                                        ->where('materia_id', $this->materiaSeleccionada)
                                        ->exists();
                                    $horasAsignadas = \App\Models\Imparte::where('maestro_id', $maestroSeleccionado)->count();
                                }
                            @endphp
                            @if(isset($tieneExperiencia))
                                <p class="text-green-600">
                                    ✅ {{ $tieneExperiencia ? 'Tiene experiencia' : 'Nuevo en esta materia' }} - {{ $horasAsignadas }} materias asignadas
                                    @if(isset($maestroAutoSeleccionado) && $maestroAutoSeleccionado && isset($maestroOptimo) && $maestroOptimo && $maestroOptimo->id == $maestroSeleccionado)
                                        <span class="font-semibold text-indigo-600">- ⭐ Selección automática óptima</span>
                                    @endif
                                </p>
                            @endif
                        @elseif($maestrosDisponibles->isNotEmpty() && $materiaSeleccionada && $horaSeleccionada && $diaSeleccionado)
                            <p class="text-blue-600">
                                💡 {{ $maestrosDisponibles->count() }} maestros disponibles para este horario
                            </p>
                        @endif
                    </div>
                    
                    <!-- Botón de auto-selección -->
                    @if($materiaSeleccionada && $horaSeleccionada && $diaSeleccionado && $maestrosDisponibles->isNotEmpty())
                        <button type="button"
                                wire:click="cargarMaestros" 
                                class="mt-2 text-xs text-indigo-600 hover:text-indigo-800 hover:underline focus:outline-none transition-colors duration-200">
                            🎯 Auto-seleccionar maestro óptimo
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Botones de Acción -->
            <div class="mt-4 flex gap-2">
                <button wire:click="asignarClase" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                               @if(!$materiaSeleccionada || !$maestroSeleccionado || !$horaSeleccionada || !$diaSeleccionado) opacity-50 cursor-not-allowed @endif"
                        @if(!$materiaSeleccionada || !$maestroSeleccionado || !$horaSeleccionada || !$diaSeleccionado) disabled @endif>
                    ✅ Asignar Clase
                </button>
                <button wire:click="limpiarSeleccion" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    🗑️ Limpiar Selección
                </button>
            </div>
            
            @if($celdaSeleccionada)
                <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm">
                    📍 <strong>Celda seleccionada:</strong> 
                    {{ $horasClase[$horaSeleccionada - 1] ?? 'N/A' }} - {{ $diaSeleccionado }}
                </div>
            @endif
        </div>
        
        <!-- Tabla de Horarios -->
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Horario del Grupo</h3>
                @if($grupo)
                    <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                        {{ $grupo->grado }} "{{ $grupo->letra }}" "{{ $grupo->generacion }}"
                    </span>
                @endif
            </div>
            <p class="text-sm text-gray-600 mb-4">Haz clic en cualquier celda para seleccionarla automáticamente en los campos de arriba.</p>
            
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">
                                Hora
                            </th>
                            @foreach ($diasSemana as $dia)
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">
                                    {{ $dia }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($horasClase as $index => $hora)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 sticky left-0 bg-white z-10 border-r border-gray-200">
                                    <div class="text-center">
                                        <div class="font-semibold">{{ $hora }}</div>
                                        <div class="text-xs text-gray-500">Módulo {{ $index + 1 }}</div>
                                    </div>
                                </td>
                                @foreach ($diasSemana as $dia)
                                    @php
                                        $numeroHora = $index + 1;
                                        $esSeleccionada = $celdaSeleccionada === $numeroHora . '_' . $dia;
                                        $tieneAsignacion = isset($horario[$numeroHora][$dia]) && $horario[$numeroHora][$dia] !== null;
                                    @endphp
                                    <td class="px-4 py-3 text-sm text-gray-500 cursor-pointer transition-all duration-200 relative border-r border-gray-100
                                        {{ $esSeleccionada ? 'bg-indigo-100 border-2 border-indigo-300 shadow-sm' : 'hover:bg-gray-50' }}
                                        {{ $tieneAsignacion ? 'bg-green-50 hover:bg-green-100' : '' }}"
                                        wire:click="seleccionarCelda({{ $numeroHora }}, '{{ $dia }}')"
                                        title="Clic para seleccionar {{ $hora }} - {{ $dia }}">
                                        
                                        @if ($tieneAsignacion)
                                            <div class="relative min-h-[60px] p-2">
                                                <!-- Materia -->
                                                <div class="font-semibold text-gray-900 text-sm leading-tight mb-1">
                                                    {{ $horario[$numeroHora][$dia]['materia'] ?? 'Sin materia' }}
                                                </div>
                                                
                                                <!-- Maestro -->
                                                <div class="text-gray-600 text-xs leading-tight">
                                                    👨‍🏫 {{ $horario[$numeroHora][$dia]['maestro'] ?? 'Sin maestro' }}
                                                </div>
                                                
                                                <!-- Botón eliminar -->
                                                <button wire:click.stop="eliminarAsignacion({{ $numeroHora }}, '{{ $dia }}')" 
                                                        class="absolute -top-1 -right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-full flex items-center justify-center opacity-80 hover:opacity-100 transition-all duration-200 shadow-sm"
                                                        title="Eliminar asignación">
                                                    ×
                                                </button>
                                                
                                                <!-- Indicador de selección -->
                                                @if($esSeleccionada)
                                                    <div class="absolute -top-1 -left-1 w-4 h-4 bg-indigo-500 rounded-full flex items-center justify-center">
                                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center h-16 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                                <div class="text-center">
                                                    <div class="text-2xl mb-1">+</div>
                                                    <div class="text-xs">Asignar</div>
                                                </div>
                                                
                                                <!-- Indicador de selección para celdas vacías -->
                                                @if($esSeleccionada)
                                                    <div class="absolute -top-1 -left-1 w-4 h-4 bg-indigo-500 rounded-full flex items-center justify-center">
                                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Leyenda -->
            <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-600 bg-gray-50 p-3 rounded-md">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-50 border border-green-200 rounded mr-2"></div>
                    <span>Clase asignada</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-indigo-100 border-2 border-indigo-300 rounded mr-2"></div>
                    <span>Celda seleccionada</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-gray-50 border border-gray-200 rounded mr-2"></div>
                    <span>Disponible para asignar</span>
                </div>
                <div class="flex items-center">
                    <span class="text-indigo-600 mr-2">⭐</span>
                    <span>Recomendación automática</span>
                </div>
            </div>
        </div>
        
        <!-- Botón de Exportar -->
        <div class="mt-8 flex justify-end">
            <button wire:click="exportarPDF" 
                    class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                📄 Exportar a PDF
            </button>
        </div>
        
        <!-- Estadísticas del Horario -->
        @php
            $totalClases = 0;
            $clasesAsignadas = 0;
            if(is_array($horario) || is_object($horario)) {
                foreach($horario as $horas) {
                    if(is_array($horas) || is_object($horas)) {
                        foreach($horas as $celda) {
                            $totalClases++;
                            if($celda !== null) {
                                $clasesAsignadas++;
                            }
                        }
                    }
                }
            }
            $porcentajeCompleto = $totalClases > 0 ? round(($clasesAsignadas / $totalClases) * 100, 1) : 0;
        @endphp
        
        <div class="mt-6 bg-gray-50 p-4 rounded-lg">
            <h4 class="text-sm font-medium text-gray-900 mb-2">Estadísticas del Horario</h4>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $clasesAsignadas }}</div>
                    <div class="text-gray-600">Clases Asignadas</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $totalClases - $clasesAsignadas }}</div>
                    <div class="text-gray-600">Espacios Libres</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $porcentajeCompleto }}%</div>
                    <div class="text-gray-600">Completado</div>
                </div>
            </div>
            
            <!-- Barra de progreso -->
            <div class="mt-3">
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $porcentajeCompleto }}%"></div>
                </div>
            </div>
        </div>
    @endif
</div>