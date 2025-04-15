@extends('layouts.app') {{-- Asegúrate que tengas un layout base llamado app.blade.php --}}

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">📌 Panel de Inscripciones – Ciclo {{ $datos['ciclo_actual'] }}</h1>

    {{-- Tarjetas resumen --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white shadow p-4 rounded-xl">
            <p class="text-gray-500">Estado de inscripción</p>
            <p class="text-xl font-semibold text-green-600">{{ $datos['estado_inscripcion'] }}</p>
        </div>
        <div class="bg-white shadow p-4 rounded-xl">
            <p class="text-gray-500">Materias ofertadas</p>
            <p class="text-xl font-semibold">{{ $datos['total_materias'] }}</p>
        </div>
        <div class="bg-white shadow p-4 rounded-xl">
            <p class="text-gray-500">Estudiantes inscritos</p>
            <p class="text-xl font-semibold">{{ $datos['total_inscritos'] }}</p>
        </div>
        <div class="bg-white shadow p-4 rounded-xl col-span-1 md:col-span-3">
            <p class="text-gray-500">Materias con cupo lleno</p>
            <p class="text-xl font-semibold text-red-500">{{ $datos['materias_llenas'] }}</p>
        </div>
    </div>

    {{-- Acciones rápidas --}}
    <div class="mb-6">
        <h2 class="text-lg font-bold mb-2">Acciones rápidas</h2>
        <div class="flex flex-wrap gap-2">
            <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg">➕ Crear ciclo</a>
            <a href="#" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">📚 Administrar materias</a>
            <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded-lg">👨‍🏫 Asignar docentes</a>
        </div>
    </div>

    {{-- Tabla resumen --}}
    <div class="bg-white shadow rounded-xl p-4">
        <h2 class="text-lg font-bold mb-3">Resumen de materias inscritas</h2>
        <table class="table-auto w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Materia</th>
                    <th>Grupo</th>
                    <th>Docente</th>
                    <th>Cupo</th>
                    <th>Inscritos</th>
                    <th>% Ocupado</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td>Matemáticas I</td>
                    <td>A</td>
                    <td>Juan Pérez</td>
                    <td>30</td>
                    <td>30</td>
                    <td>100%</td>
                    <td class="text-red-500">Lleno</td>
                </tr>
                <tr class="border-b">
                    <td>Biología I</td>
                    <td>B</td>
                    <td>Laura Gómez</td>
                    <td>35</td>
                    <td>20</td>
                    <td>57%</td>
                    <td>Abierta</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
