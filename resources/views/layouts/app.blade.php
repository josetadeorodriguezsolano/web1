<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sistema</title>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>


<body class="bg-gray-100">

    <!-- Barra Superior -->
    <header class="flex items-center justify-between px-6 py-4 bg-[#1E3A8A] text-white fixed top-0 w-full z-50">
        <!-- Botón menú -->
        <button id="toggleSidebar" class="mr-4 focus:outline-none">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

        <!-- Bienvenida -->
        <div class="text-lg font-semibold">Hola, Bienvenido!</div>

        <!-- Fecha -->
        <div class="flex-1 text-center text-sm font-light">
            {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}
        </div>

        <!-- Iconos y Foto -->
        <div class="flex items-center space-x-4">
            <i data-lucide="bell" class="w-6 h-6 cursor-pointer"></i>
            <i data-lucide="settings" class="w-6 h-6 cursor-pointer"></i>
            <div class="w-10 h-10 flex items-center justify-center bg-white text-[#1E3A8A] rounded-full border-2 border-white">
                <i data-lucide="user" class="w-6 h-6"></i>
            </div>
                    </div>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-16 left-0 w-64 h-full bg-[#F3F4F6] shadow-lg transform -translate-x-full transition-transform duration-300 z-40">
        @include('menu')
    </aside>


    <!-- Contenido Principal -->
    <main class="pt-20 pb-24 px-6 transition-all duration-300" id="mainContent">
        @yield('content')
    </main>



    <script>
        lucide.createIcons()

        const sidebar = document.getElementById('sidebar')
        const toggleBtn = document.getElementById('toggleSidebar')
        const closeBtn = document.getElementById('closeSidebar')
        const mainContent = document.getElementById('mainContent')

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full')
        })

        closeBtn.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full')
        })
    </script>

    <!-- Footer -->
    <footer class="flex items-center px-6 py-2 bg-[#1E3A8A] text-white fixed bottom-0 w-full z-50">
        <!-- Imagen grande -->
        <div class="flex-shrink-0 mr-4">
            <img src="{{ asset('img/SecundariaBlancos.png') }}" alt="Logo" class="h-60 object-contain">
        </div>

        <!-- Textos a la derecha de la imagen -->
        <div class="flex flex-col">
            <span class="text-xl font-semibold">Mi oficina</span>
            <span class="text-sm font-light">by Equipo 1</span>
            <span class="text-xs font-light mt-1">Copyright © ISC 2022-2026 Todos los derechos reservados.
            Español</span>
        </div>
    </footer>



</body>
</html>
