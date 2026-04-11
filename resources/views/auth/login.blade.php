@extends('layouts.auth')
@section('title', 'Connexion')

@section('content')
{{-- Login Card --}}
<div class="bg-white p-8 sm:p-10 rounded-2xl" style="box-shadow:0 24px 48px -12px rgba(0,36,68,0.08)">

    <div class="mb-10">
        <h2 class="text-3xl font-bold mb-2" style="color:#002444">Connexion</h2>
        <p class="text-sm text-slate-500">Veuillez entrer vos paramètres d'accès pour continuer.</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="space-y-6" x-data="{ showPwd: false }">
        @csrf

        {{-- Identifiant --}}
        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">
                Matricule / Identifiant
            </label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 select-none"
                      style="font-size:20px">person</span>
                <input id="email" name="email" type="text" value="{{ old('email') }}" required autofocus
                       placeholder="Ex: 22G00123"
                       class="w-full pl-12 pr-4 py-4 bg-slate-50 border-0 rounded-lg text-slate-800 placeholder-slate-400
                              focus:ring-2 focus:outline-none transition-all
                              {{ $errors->has('email') ? 'ring-2 ring-red-400 bg-red-50' : 'focus:ring-primary/30' }}">
            </div>
            @error('email')
                <div class="flex items-center gap-1.5 text-red-600 mt-1.5 ml-1">
                    <span class="material-symbols-outlined text-red-600 select-none" style="font-size:16px">error</span>
                    <span class="text-xs font-medium">{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Mot de passe --}}
        <div class="space-y-1.5">
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">
                Mot de passe
            </label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 select-none"
                      style="font-size:20px">lock</span>
                <input id="password" name="password" :type="showPwd ? 'text' : 'password'" required
                       placeholder="••••••••"
                       class="w-full pl-12 pr-12 py-4 bg-slate-50 border-0 rounded-lg text-slate-800
                              focus:ring-2 focus:outline-none transition-all
                              {{ $errors->has('password') ? 'ring-2 ring-red-400 bg-red-50' : 'focus:ring-primary/30' }}">
                <button type="button" @click="showPwd = !showPwd"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined select-none" style="font-size:20px"
                          x-text="showPwd ? 'visibility_off' : 'visibility'">visibility</span>
                </button>
            </div>
            @error('password')
                <div class="flex items-center gap-1.5 text-red-600 mt-1.5 ml-1">
                    <span class="material-symbols-outlined select-none" style="font-size:16px">info</span>
                    <span class="text-xs font-medium">{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Se souvenir --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer group">
                <input type="checkbox" name="remember" value="1"
                       class="h-5 w-5 rounded border-slate-300 text-primary focus:ring-primary/30 transition-all">
                <span class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors">Se souvenir de moi</span>
            </label>
        </div>

        {{-- Bouton connexion --}}
        <button type="submit"
                class="w-full font-bold py-4 rounded-lg text-white transition-all duration-200 active:scale-[0.98]"
                style="background:#002444;box-shadow:0 4px 14px rgba(0,36,68,0.15)"
                onmouseover="this.style.background='#1a3a5c'" onmouseout="this.style.background='#002444'">
            Se connecter
        </button>
    </form>

    {{-- Lien support --}}
    <div class="mt-10 text-center">
        <a href="mailto:scolarite@iug.cm"
           class="inline-flex items-center gap-2 text-sm font-semibold transition-colors group"
           style="color:#002444"
           onmouseover="this.style.color='#1a3a5c'" onmouseout="this.style.color='#002444'">
            <span class="material-symbols-outlined text-lg select-none" style="font-size:18px">help_outline</span>
            <span>Problème de connexion ? Contactez la scolarité</span>
            <span class="material-symbols-outlined select-none opacity-0 group-hover:opacity-100 -translate-x-2 group-hover:translate-x-0 transition-all"
                  style="font-size:16px">chevron_right</span>
        </a>
    </div>
</div>

{{-- Footer --}}
<div class="mt-8 text-center text-slate-400 text-[10px] uppercase tracking-[0.2em]">
    © {{ date('Y') }} SYGRE — Système de Gestion Intégrée
</div>
@endsection
