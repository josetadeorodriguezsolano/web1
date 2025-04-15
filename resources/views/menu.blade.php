<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menú con Sidebar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100">

<div class="flex">
    <!-- Sidebar -->
   <div class="relative w-64 h-screen bg-[#F3F4F6] shadow-lg">
    <!-- Logo en posición absoluta -->
    <div class="absolute top-4 left-1/2 transform -translate-x-1/2">
        <img src="{{ asset('img/LogoSecundaria.png') }}"
             alt="Logo"
             class="w-32 h-28">
    </div>


    <!-- Menú -->
    <nav class="mt-36 space-y-1">
        <a href="/dashboard" class="flex items-center py-3 px-6 hover:bg-gray-300">
            <i data-lucide="home" class="w-5 h-5 mr-3"></i> Dashboard
        </a>
        <a href="/pase_de_lista" class="flex items-center py-3 px-6 hover:bg-gray-300">
            <i data-lucide="list" class="w-5 h-5 mr-3"></i> Pase de Lista
        </a>
        <a href="/catalogo/maestros" class="flex items-center py-3 px-6 hover:bg-gray-300">
            <i data-lucide="users" class="w-5 h-5 mr-3"></i> Catálogo Maestros
        </a>
        <a href="/admin/index" class="flex items-center py-3 px-6 hover:bg-gray-300">
            <i data-lucide="file-text" class="w-5 h-5 mr-3"></i> Inscripciones
        </a>
        <a href="/calificaciones" class="flex items-center py-3 px-6 hover:bg-gray-300">
            <i data-lucide="award" class="w-5 h-5 mr-3"></i> Calificaciones
        </a>
    </nav>
</div>


    <!-- Contenido -->
    <div class="flex-1 p-8">
        <h1 class="text-4xl font-bold mb-4">Bienvenido a Mi Secundaria🎉</h1> </h1>
        <p class="text-lg text-gray-700">El sistema que facilitará la gestión de tu secundaria</p>
    </div>
</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>
