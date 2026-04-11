@extends('layouts.app')
@section('title', 'Mon Profil')

@section('content')
<div class="pt-4 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Mon Profil</h1>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="card flex flex-col items-center text-center py-8">
            <div class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-2xl mb-4"
                 style="background: linear-gradient(135deg, #002444, #1a3a5c)">
                {{ strtoupper(substr($user->prenom, 0, 1) . substr($user->nom, 0, 1)) }}
            </div>
            <p class="text-base font-bold text-slate-800">{{ $user->nom_complet }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $user->email }}</p>
            <span class="mt-3 badge badge-green">Agent de Scolarité</span>
        </div>

        <div class="col-span-2 card">
            <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Informations professionnelles</h2>
            <dl class="space-y-3">
                @if($agent->matricule_agent)
                <div class="flex justify-between">
                    <dt class="text-xs text-slate-500 font-semibold uppercase">Matricule agent</dt>
                    <dd class="text-sm font-mono font-bold text-primary">{{ $agent->matricule_agent }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-xs text-slate-500 font-semibold uppercase">Service</dt>
                    <dd class="text-sm text-slate-800">{{ $agent->service }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-xs text-slate-500 font-semibold uppercase">Requêtes traitées</dt>
                    <dd class="text-sm font-semibold text-slate-800">
                        {{ $agent->requetes()->whereIn('statut', ['traitee_fondee', 'traitee_non_fondee'])->count() }}
                    </dd>
                </div>
                @if($user->derniere_connexion)
                <div class="flex justify-between">
                    <dt class="text-xs text-slate-500 font-semibold uppercase">Dernière connexion</dt>
                    <dd class="text-sm text-slate-800">{{ $user->derniere_connexion->format('d/m/Y à H:i') }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="card">
        <h2 class="text-sm font-bold text-slate-700 mb-5 uppercase tracking-wide">Changer le mot de passe</h2>
        <form method="POST" action="{{ route('agent.profil.update') }}" x-data="{ s1: false, s2: false }">
            @csrf @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nouveau mot de passe</label>
                    <div class="relative">
                        <input :type="s1 ? 'text' : 'password'" name="password"
                               class="form-input pr-10" placeholder="••••••••" minlength="8">
                        <button type="button" @click="s1=!s1"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="form-label">Confirmer</label>
                    <div class="relative">
                        <input :type="s2 ? 'text' : 'password'" name="password_confirmation"
                               class="form-input pr-10" placeholder="••••••••" minlength="8">
                        <button type="button" @click="s2=!s2"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex justify-end">
                <button type="submit" class="btn-primary">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>
@endsection
