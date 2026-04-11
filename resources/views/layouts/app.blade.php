<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'G-REQUÊTES') — G-REQUÊTES</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-surface font-sans" data-role="{{ auth()->user()?->role?->value }}" x-data="{ sidebarOpen: true }">
<div class="flex h-screen overflow-hidden">

    {{-- ─── SIDEBAR ──────────────────────────────────────────────────── --}}
    <aside class="w-52 flex-shrink-0 flex flex-col"
           style="background: linear-gradient(180deg, #002444 0%, #0a2d52 100%);">

        {{-- Logo --}}
        <div class="px-5 py-6 border-b border-white/10">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 14l9-5-9-5-9 5 9 5zm0 7v-7m0 7l-9-5m9 5l9-5"/>
                    </svg>
                </div>
                <div>
                    <div class="text-white font-bold text-sm leading-none">G-REQUÊTES</div>
                    <div class="text-white/50 text-xs mt-0.5">Système de Gestion</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php $user = auth()->user(); @endphp

            @if($user->isEtudiant())
                <a href="{{ route('etudiant.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('etudiant.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Tableau de bord
                </a>
                <a href="{{ route('etudiant.requetes.index') }}"
                   class="sidebar-link {{ request()->routeIs('etudiant.requetes.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Mes requêtes
                </a>
                <a href="{{ route('etudiant.profil') }}"
                   class="sidebar-link {{ request()->routeIs('etudiant.profil') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Mon profil
                </a>

            @elseif($user->isAgent())
                <a href="{{ route('agent.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Tableau de bord
                </a>
                <a href="{{ route('agent.requetes.index') }}"
                   class="sidebar-link {{ request()->routeIs('agent.requetes.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Mes requêtes
                </a>
                <a href="{{ route('agent.pv.create') }}"
                   class="sidebar-link {{ request()->routeIs('agent.pv.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Générer PV
                </a>
                <a href="{{ route('agent.historique') }}"
                   class="sidebar-link {{ request()->routeIs('agent.historique') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Historique
                </a>
                <a href="{{ route('agent.profil') }}"
                   class="sidebar-link {{ request()->routeIs('agent.profil') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Mon profil
                </a>

            @elseif($user->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Tableau de bord
                </a>
                <a href="{{ route('admin.utilisateurs.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Utilisateurs
                </a>
                <a href="{{ route('admin.journal.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.journal.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Journal d'audit
                </a>
                <a href="{{ route('admin.statistiques') }}"
                   class="sidebar-link {{ request()->routeIs('admin.statistiques') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Statistiques
                </a>
                <a href="{{ route('admin.filieres.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.filieres.*') || request()->routeIs('admin.cours.*') || request()->routeIs('admin.sessions.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Référentiels
                </a>
            @endif
        </nav>

        {{-- Bottom: user info + logout --}}
        <div class="px-3 py-4 border-t border-white/10">
            <div class="px-2 py-2 mb-2">
                <p class="text-white text-xs font-semibold truncate">{{ $user->nom_complet }}</p>
                <p class="text-white/50 text-xs truncate">{{ $user->role->label() }}</p>
            </div>
            <a href="#" class="sidebar-link text-red-300 hover:text-red-200 hover:bg-red-500/10"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Déconnexion
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    {{-- ─── MAIN CONTENT ─────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Header --}}
        <header class="bg-white shadow-card px-6 py-3 flex items-center justify-between flex-shrink-0 z-10">
            {{-- Search --}}
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" placeholder="Rechercher une requête..."
                           class="w-full pl-9 pr-4 py-2 bg-surface rounded-lg text-sm text-slate-700
                                  border-0 focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all">
                </div>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-4">
                {{-- Notification bell --}}
                <div class="relative" id="notification-bell-wrapper">
                    <a href="@if($user->isEtudiant()) {{ route('etudiant.notifications.index') }} @else # @endif"
                       class="relative w-9 h-9 flex items-center justify-center rounded-full bg-surface hover:bg-surface-container transition-colors">
                        <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if(isset($notificationsCount) && $notificationsCount > 0)
                            <span id="notif-badge" class="absolute -top-1 -right-1 w-4 h-4 bg-danger text-white text-xs flex items-center justify-center rounded-full font-bold">
                                {{ $notificationsCount > 9 ? '9+' : $notificationsCount }}
                            </span>
                        @else
                            <span id="notif-badge" class="hidden absolute -top-1 -right-1 w-4 h-4 bg-danger text-white text-xs flex items-center justify-center rounded-full font-bold"></span>
                        @endif
                    </a>
                </div>

                {{-- User --}}
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-800">{{ $user->nom_complet }}</p>
                        <p class="text-xs text-slate-500">{{ $user->role->label() }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm"
                         style="background: linear-gradient(135deg, #002444, #1a3a5c)">
                        {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
            @if(session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif
            @if($errors->any())
                <x-alert type="error" :message="$errors->first()" />
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto px-6 pb-6 pt-2">
            @yield('content')
        </main>
    </div>

</div>

{{-- Alpine.js CDN --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

@stack('scripts')
</body>
</html>
