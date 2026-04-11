@extends('layouts.app')
@section('title', 'Détail Requête ' . $requete->ref_requete)

@section('content')
<div class="pt-4 max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('etudiant.requetes.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container text-slate-600 hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-slate-800">Requête {{ $requete->ref_requete }}</h1>
                <x-badge-statut :statut="$requete->statut" />
            </div>
            <p class="text-sm text-slate-500 mt-0.5">Soumise le {{ $requete->date_soumission->format('d/m/Y à H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        {{-- Main info --}}
        <div class="col-span-2 space-y-4">
            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Informations académiques</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Cours</dt>
                        <dd class="text-sm text-slate-800 font-medium">{{ $requete->cours->nom_cours }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Filière</dt>
                        <dd class="text-sm text-slate-800">{{ $requete->cours->filiere->nom_filiere }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Session</dt>
                        <dd class="text-sm text-slate-800">{{ $requete->session->libelle }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Type d'anomalie</dt>
                        <dd class="text-sm text-slate-800">{{ $requete->type_anomalie->label() }}</dd>
                    </div>
                </dl>
            </div>

            @if($requete->description)
            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">Description</h2>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $requete->description }}</p>
            </div>
            @endif

            {{-- Note result --}}
            @if($requete->note)
            <div class="card border-l-4 border-green-500">
                <h2 class="text-sm font-bold text-green-700 mb-4 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Résultat de correction
                </h2>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-xs text-slate-500 mb-1">Note Avant</p>
                        <span class="text-2xl font-bold text-red-600">{{ $requete->note->note_avant ?? 'N/A' }}</span>
                        <span class="text-sm text-slate-500">/20</span>
                    </div>
                    <div class="text-slate-300 text-2xl">→</div>
                    <div class="text-center">
                        <p class="text-xs text-slate-500 mb-1">Note Après</p>
                        <span class="text-2xl font-bold text-green-600">{{ $requete->note->note_apres }}</span>
                        <span class="text-sm text-slate-500">/20</span>
                    </div>
                    <div class="ml-auto text-xs text-slate-500">
                        Modifiée le {{ $requete->note->date_modification->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            @endif

            {{-- Motif rejet --}}
            @if($requete->motif_rejet)
            <div class="card border-l-4 border-red-400">
                <h2 class="text-sm font-bold text-red-700 mb-2 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Motif de la décision
                </h2>
                <p class="text-sm text-slate-700">{{ $requete->motif_rejet }}</p>
            </div>
            @endif

            {{-- Pièces jointes --}}
            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Pièces justificatives</h2>
                @if($requete->piecesJointes->isEmpty())
                    <p class="text-sm text-slate-400">Aucune pièce jointe.</p>
                @else
                    <div class="space-y-2">
                        @foreach($requete->piecesJointes as $pj)
                        <div class="flex items-center gap-3 p-3 bg-surface rounded-xl">
                            <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center {{ $pj->isPdf() ? 'bg-red-50' : 'bg-blue-50' }}">
                                <svg class="w-4 h-4 {{ $pj->isPdf() ? 'text-red-500' : 'text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate">{{ $pj->nom_fichier }}</p>
                                <p class="text-xs text-slate-400">{{ $pj->taille_humaine }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar: timeline --}}
        <div class="col-span-1 space-y-4">
            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Suivi</h2>
                <ol class="relative border-l border-surface-container pl-4 space-y-4">
                    <li>
                        <div class="absolute -left-1.5 mt-0.5 w-3 h-3 rounded-full bg-primary"></div>
                        <p class="text-xs font-semibold text-slate-800">Requête soumise</p>
                        <p class="text-xs text-slate-400">{{ $requete->date_soumission->format('d/m/Y H:i') }}</p>
                    </li>
                    @if($requete->date_prise_en_charge)
                    <li>
                        <div class="absolute -left-1.5 mt-0.5 w-3 h-3 rounded-full bg-blue-400"></div>
                        <p class="text-xs font-semibold text-slate-800">Prise en charge</p>
                        <p class="text-xs text-slate-400">{{ $requete->date_prise_en_charge->format('d/m/Y H:i') }}</p>
                        @if($requete->agent)
                        <p class="text-xs text-slate-500">par {{ $requete->agent->utilisateur->nom_complet }}</p>
                        @endif
                    </li>
                    @endif
                    @if($requete->date_traitement)
                    <li>
                        <div class="absolute -left-1.5 mt-0.5 w-3 h-3 rounded-full {{ $requete->statut->value === 'traitee_fondee' ? 'bg-green-500' : 'bg-red-400' }}"></div>
                        <p class="text-xs font-semibold text-slate-800">Décision rendue</p>
                        <p class="text-xs text-slate-400">{{ $requete->date_traitement->format('d/m/Y H:i') }}</p>
                    </li>
                    @endif
                </ol>
            </div>

            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-3 uppercase tracking-wide">Agent traitant</h2>
                @if($requete->agent)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-xs"
                             style="background: linear-gradient(135deg, #002444, #1a3a5c)">
                            {{ strtoupper(substr($requete->agent->utilisateur->prenom, 0, 1) . substr($requete->agent->utilisateur->nom, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $requete->agent->utilisateur->nom_complet }}</p>
                            <p class="text-xs text-slate-500">{{ $requete->agent->service }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400">Non assigné</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
