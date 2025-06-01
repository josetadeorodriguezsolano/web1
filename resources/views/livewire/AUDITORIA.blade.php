<div class="container mx-auto px-4 py-6">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

    <script>
        function toggleDetails(elementId) {
            const element = document.getElementById(elementId);
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
                element.classList.add('animate-fadeIn');
            } else {
                element.classList.add('hidden');
                element.classList.remove('animate-fadeIn');
            }
        }
    </script>

    <div class="bg-white rounded-lg shadow-lg">
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-4">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Auditoría del Sistema
            </h1>
            <p class="text-gray-600 mt-1">Consulta y monitoreo de cambios en el sistema</p>
        </div>

        <!-- Filtros -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <label for="tipoConsulta" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Consulta
                    </label>
                    <select wire:model.live="tipoConsulta" id="tipoConsulta" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                        <option value="">Selecciona un tipo de consulta</option>
                        <option value="horario">Auditoría de Horarios</option>
                        <option value="inasistencia">Auditoría de Inasistencias</option>
                    </select>
                </div>
                
                @if($tipoConsulta)
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ count($auditorias) }} registro(s) encontrado(s)</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="px-6 py-4">
            @if(!$tipoConsulta)
                <!-- Estado inicial -->
                <div class="text-center py-12">
                    <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona un tipo de consulta</h3>
                    <p class="text-gray-500">Elige entre auditoría de horarios o inasistencias para ver los registros.</p>
                </div>
            @elseif(count($auditorias) === 0)
                <!-- Sin resultados -->
                <div class="text-center py-12">
                    <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay registros de auditoría</h3>
                    <p class="text-gray-500">No se encontraron registros para el tipo de consulta seleccionado.</p>
                </div>
            @else
                <!-- Tabla de auditorías -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Maestro
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acción
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cambios
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($auditorias as $auditoria)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-800">
                                                    {{ substr($auditoria->nombre_maestro, 0, 1) }}{{ substr($auditoria->apellidos_maestro, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $auditoria->nombre_maestro }} {{ $auditoria->apellidos_maestro }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: {{ $auditoria->user_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $eventColors = [
                                            'created' => 'bg-green-100 text-green-800',
                                            'updated' => 'bg-yellow-100 text-yellow-800',
                                            'deleted' => 'bg-red-100 text-red-800'
                                        ];
                                        $eventLabels = [
                                            'created' => 'Creado',
                                            'updated' => 'Actualizado',
                                            'deleted' => 'Eliminado'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $eventColors[$auditoria->event] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $eventLabels[$auditoria->event] ?? ucfirst($auditoria->event) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ \Carbon\Carbon::parse($auditoria->created_at)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($auditoria->created_at)->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($auditoria->event === 'updated' && (count($auditoria->old_values) > 0 || count($auditoria->new_values) > 0))
                                        <button type="button" 
                                                onclick="toggleDetails('audit-{{ $auditoria->id }}')"
                                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver cambios
                                        </button>
                                        
                                        <!-- Panel de detalles (inicialmente oculto) -->
                                        <div id="audit-{{ $auditoria->id }}" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @if(count($auditoria->old_values) > 0)
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Valores Anteriores:</h4>
                                                    <div class="bg-red-50 p-3 rounded border">
                                                        @foreach($auditoria->old_values as $key => $value)
                                                        <div class="text-sm mb-1">
                                                            <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                            <span class="text-red-700">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @if(count($auditoria->new_values) > 0)
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Valores Nuevos:</h4>
                                                    <div class="bg-green-50 p-3 rounded border">
                                                        @foreach($auditoria->new_values as $key => $value)
                                                        <div class="text-sm mb-1">
                                                            <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                            <span class="text-green-700">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($auditoria->event === 'created')
                                        <span class="text-sm text-gray-500">Registro creado</span>
                                    @elseif($auditoria->event === 'deleted')
                                        <span class="text-sm text-gray-500">Registro eliminado</span>
                                    @else
                                        <span class="text-sm text-gray-500">Sin cambios detallados</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>