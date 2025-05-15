@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Cabecera con título y botones -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Información del Alumno</h1>
            <div class="flex space-x-3">
                <button class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                    Editar
                </button>
                <button class="flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    Eliminar
                </button>
            </div>
        </div>

        <!-- Ficha del alumno -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <!-- Encabezado de la ficha -->
            <div class="p-6 bg-[#1E3A8A] text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-20 h-20 bg-white rounded-full flex items-center justify-center text-[#1E3A8A] border-4 border-white mr-4">
                        <span class="text-2xl font-bold">JC</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Juan Carlos García Pérez</h2>
                        <p class="text-lg opacity-90">Matrícula: A12345678</p>
                        <div class="mt-1 inline-block px-2 py-1 bg-green-500 text-xs font-semibold rounded-full">
                            Vigente
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido de la ficha -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Datos Personales -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Datos Personales</h3>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Nombres</p>
                            <p class="text-base font-medium">Juan Carlos</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Apellidos</p>
                            <p class="text-base font-medium">García Pérez</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">CURP</p>
                            <p class="text-base font-medium">GAPE050823HDFRCR01</p>
                        </div>
                    </div>

                    <!-- Información Académica y Contacto -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información Académica y Contacto</h3>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Teléfono de Contacto</p>
                            <p class="text-base font-medium">6121234567</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Nombre del Tutor</p>
                            <p class="text-base font-medium">María López Gómez</p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Grupo Actual</p>
                            <p class="text-base font-medium">2°B (Gen. 2024)</p>
                        </div>
                    </div>
                </div>
            </div>
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
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">Matemáticas</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">8.5</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">9.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">7.5</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">8.0</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-800">8.3</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">Español</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">7.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">8.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">8.5</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">9.0</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-800">8.1</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">Historia</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">9.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">8.5</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">9.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">-</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-800">8.8</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">Biología</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">8.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">7.5</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">8.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">-</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-800">7.8</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">Educación Física</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">10.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">9.5</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">10.0</td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">-</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-800">9.8</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
            <button class="px-5 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                Volver a la lista
            </button>
            <button class="px-5 py-2 bg-[#1E3A8A] text-white rounded-md hover:bg-blue-900 transition">
                Generar Reporte
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endsection