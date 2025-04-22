@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Registro de Calificaciones</h2>

    <div class="mb-6">
        <label for="materia" class="block text-sm font-medium text-gray-700 mb-2">Selecciona una materia:</label>
        <select id="materia" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            <option value="">-- Seleccionar materia --</option>
            <option value="1">Matemáticas (3° "A")</option>
            <option value="2">Español (2° "B")</option>
            <option value="3">Ciencias (1° "C")</option>
        </select>
    </div>

    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            Calificaciones 3° "A" - Ciclo 2024-2025
        </h3>

        <form>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#1E3A8A]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Alumno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Unidad 1</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Unidad 2</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Unidad 3</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Unidad 4</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Promedio</th>
                        </tr>
                    </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <!-- Alumno 1 -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                Pérez García, Juan Carlos
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Matrícula: 2023A0001
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="8.5"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="9.0"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="7.8"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="8.2"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">
                                                8.4
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Alumno 2 -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                Rodríguez López, María Fernanda
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Matrícula: 2023A0002
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="9.5"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="10.0"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="9.8"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="9.5"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">
                                                9.7
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Alumno 3 -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                González Martínez, José Luis
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Matrícula: 2023A0003
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="5.5"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="6.0"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="5.8"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="5.9"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-red-600">
                                                5.8
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Alumno 4 -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                Sánchez Torres, Ana Paola
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Matrícula: 2023A0004
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="8.0"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="7.5"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="8.2"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="7.8"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">
                                                7.9
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Alumno 5 -->
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                Hernández Vargas, Carlos Eduardo
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Matrícula: 2023A0005
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="7.0"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10" value="6.5"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" step="0.1" min="0" max="10"
                                                class="w-20 text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="text-sm font-medium text-gray-900">
                                                6.8
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#1E3A8A] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-900 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                Guardar calificaciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endsection