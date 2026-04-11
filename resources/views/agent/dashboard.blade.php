@extends('layouts.app')
@section('title', 'Tableau de bord Agent')

@section('content')
<div class="pt-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Bonjour, {{ $agent->utilisateur->prenom }} 👋</h1>
        <p class="text-sm text-slate-500 mt-1">Voici la situation en temps réel de la file de requêtes.</p>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <x-card-stat :value="$stats['total']" label="Total requêtes" color="blue"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>'/>
        <x-card-stat :value="$stats['en_attente']" label="En attente" color="amber"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'/>
        <x-card-stat :value="$stats['en_cours']" label="En cours" color="purple"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'/>
        <x-card-stat :value="$stats['traitees']" label="Traitées" color="green"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'/>
    </div>

    <div class="grid grid-cols-3 gap-4">
        {{-- File prioritaire --}}
        <div class="col-span-2 card">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-slate-800">File de requêtes</h2>
                <a href="{{ route('agent.requetes.index') }}"
                   class="text-sm text-primary font-semibold flex items-center gap-1 hover:underline">
                    Tout voir <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @if($requetesPrioritaires->isEmpty())
                <x-empty-state title="File vide" description="Toutes les requêtes ont été prises en charge." />
            @else
                <div class="space-y-2">
                    @foreach($requetesPrioritaires as $req)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-surface transition-colors">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-mono font-bold text-primary">{{ $req->ref_requete }}</p>
                                <x-badge-statut :statut="$req->statut" />
                            </div>
                            <p class="text-sm font-medium text-slate-800 mt-0.5">{{ $req->etudiant->utilisateur->nom_complet }}</p>
                            <p class="text-xs text-slate-400">{{ $req->cours->nom_cours }} — {{ $req->date_soumission->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('agent.requetes.show', $req->id) }}"
                           class="btn-secondary text-xs px-3 py-1.5">Traiter</a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Mes requêtes en cours --}}
        <div class="col-span-1 card">
            <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Mes en-cours</h2>
            @if($mesRequetes->isEmpty())
                <p class="text-sm text-slate-400">Aucune requête en cours.</p>
            @else
                <div class="space-y-3">
                    @foreach($mesRequetes as $req)
                    <div class="p-3 rounded-xl bg-surface">
                        <p class="text-xs font-mono font-bold text-primary mb-0.5">{{ $req->ref_requete }}</p>
                        <p class="text-sm text-slate-800">{{ $req->etudiant->utilisateur->nom_complet }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ Str::limit($req->cours->nom_cours, 25) }}</p>
                        <a href="{{ route('agent.requetes.show', $req->id) }}"
                           class="mt-2 text-xs text-primary font-semibold hover:underline block">Continuer →</a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
