<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes por Grupo</title>

    {{-- Carga de estilos de Vite --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    {{-- Estilos Livewire --}}
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900">
    <h1 class="text-2xl font-bold p-4">Reportes</h1>

    <livewire:reportes-grupo />

    {{-- Scripts Livewire --}}
    @livewireScripts
</body>
</html>
