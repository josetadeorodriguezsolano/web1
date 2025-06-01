<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Auditoría de Inasistencias</title>


    @vite(['resources/scss/app.scss', 'resources/js/app.js'])


    @livewireStyles
</head>

<body class="bg-gray-300 text-gray-900">
    <div style="height: 10vh;" class="w3-indigo">
        <h1 class=" w3-padding-16 text-2xl text-center font-bold mb-4">Auditoría de Inasistencias</h1>
    </div>
    <div style="background-color: #98b8e2; min-height: 90vh">
        <div class="w3-light-grey max-w-7xl mx-auto">
            <livewire:auditoria-inasistencias />
        </div>
    </div>

    @livewireScripts
</body>

</html>
