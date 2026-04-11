<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Connexion') — G-REQUÊTES</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-effect {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="font-sans antialiased bg-white overflow-hidden">
<main class="flex min-h-screen w-full">

    {{-- Left panel --}}
    <div class="hidden md:flex w-1/2 relative items-center justify-center overflow-hidden"
         style="background: linear-gradient(135deg, #1a3a5c 0%, #2563a8 100%)">

        {{-- Book icon top-left --}}
        <div class="absolute top-20 left-20" style="animation: pulse 2s cubic-bezier(0.4,0,0.6,1) infinite">
            <div class="glass-effect p-4 rounded-2xl flex items-center text-white">
                <span class="material-symbols-outlined" style="font-size:30px">book</span>
            </div>
        </div>

        {{-- School icon bottom-right --}}
        <div class="absolute bottom-24 right-20" style="animation: bounce 1s infinite">
            <div class="glass-effect p-5 rounded-2xl flex items-center text-white">
                <span class="material-symbols-outlined" style="font-size:36px">school</span>
            </div>
        </div>

        {{-- Main content --}}
        <div class="z-10 text-center px-12">
            <div class="inline-flex items-center px-4 py-1.5 rounded-full mb-8"
                 style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2)">
                <span class="text-white text-xs font-bold tracking-widest uppercase">Session Académique 2025-2026</span>
            </div>
            <h1 class="text-6xl font-extrabold text-white tracking-tight mb-4">G-REQUÊTES</h1>
            <p class="text-xl font-medium max-w-md mx-auto leading-relaxed"
               style="color:rgba(219,234,254,0.9)">
                Plateforme de gestion des requêtes académiques
            </p>
            <div class="mt-12 flex justify-center">
                <div class="w-24 h-1 rounded-full" style="background:rgba(255,255,255,0.2)"></div>
            </div>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="w-full md:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white relative">
        <div class="w-full max-w-md">
            @yield('content')
        </div>
        {{-- Decorative blurs --}}
        <div class="absolute pointer-events-none"
             style="top:-10%;right:-5%;width:40%;height:40%;background:rgba(0,36,68,0.04);border-radius:9999px;filter:blur(100px)"></div>
        <div class="absolute pointer-events-none"
             style="bottom:-10%;right:-5%;width:30%;height:30%;background:rgba(255,221,177,0.05);border-radius:9999px;filter:blur(80px)"></div>
    </div>

</main>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
