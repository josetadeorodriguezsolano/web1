<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Inasistencias</title>

    {{-- Carga de estilos con Vite --}}
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    {{-- Estilos Livewire --}}
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="p-4 max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Auditoría de Inasistencias</h1>

        <livewire:auditoria-inasistencias />
    </div>

    {{-- Scripts Livewire --}}
    @livewireScripts
</body>
</html>
