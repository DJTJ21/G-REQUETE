@extends('layouts.app')
@section('title', 'Traitement — ' . $requete->ref_requete)

@section('content')
<div class="pt-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('agent.requetes.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-slate-800">{{ $requete->ref_requete }}</h1>
                <x-badge-statut :statut="$requete->statut" />
            </div>
            <p class="text-sm text-slate-500 mt-0.5">Soumise le {{ $requete->date_soumission->format('d/m/Y à H:i') }}</p>
        </div>
        {{-- Take charge button --}}
        @if($requete->statut->value === 'en_attente')
        <form method="POST" action="{{ route('agent.requetes.prendre-en-charge', $requete->id) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/>
                </svg>
                Prendre en charge
            </button>
        </form>
        @endif
    </div>

    <div class="grid grid-cols-3 gap-4">

        {{-- LEFT: Details --}}
        <div class="col-span-2 space-y-4">
            {{-- Student info --}}
            <div class="card">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold"
                         style="background: linear-gradient(135deg, #002444, #1a3a5c)">
                        {{ strtoupper(substr($requete->etudiant->utilisateur->prenom, 0, 1) . substr($requete->etudiant->utilisateur->nom, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-base font-bold text-slate-800">{{ $requete->etudiant->utilisateur->nom_complet }}</p>
                        <p class="text-sm text-slate-500">Matricule : <strong>{{ $requete->etudiant->matricule }}</strong></p>
                    </div>
                    <div class="text-right text-sm">
                        <p class="font-semibold text-slate-700">{{ $requete->etudiant->filiere->nom_filiere }}</p>
                        <p class="text-xs text-slate-400">{{ $requete->etudiant->filiere->cycle->value }} — Niveau {{ $requete->etudiant->niveau }}</p>
                    </div>
                </div>
            </div>

            {{-- Requete details --}}
            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Détails de la requête</h2>
                <dl class="grid grid-cols-2 gap-y-3 gap-x-6">
                    <div>
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Cours</dt>
                        <dd class="text-sm text-slate-800 font-medium mt-0.5">{{ $requete->cours->nom_cours }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Session</dt>
                        <dd class="text-sm text-slate-800 mt-0.5">{{ $requete->session->libelle }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Type d'anomalie</dt>
                        <dd class="text-sm text-slate-800 mt-0.5">{{ $requete->type_anomalie->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Email</dt>
                        <dd class="text-sm text-slate-800 mt-0.5">{{ $requete->etudiant->utilisateur->email }}</dd>
                    </div>
                    @if($requete->description)
                    <div class="col-span-2">
                        <dt class="text-xs text-slate-500 font-semibold uppercase">Description de l'étudiant</dt>
                        <dd class="text-sm text-slate-700 mt-1 leading-relaxed bg-surface rounded-lg p-3">{{ $requete->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Pièces jointes --}}
            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">
                    Pièces justificatives ({{ $requete->piecesJointes->count() }})
                </h2>
                @if($requete->piecesJointes->isEmpty())
                    <p class="text-sm text-slate-400">Aucune pièce jointe.</p>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($requete->piecesJointes as $pj)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-surface border border-surface-container">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $pj->isPdf() ? 'bg-red-50' : 'bg-blue-50' }}">
                                <svg class="w-4 h-4 {{ $pj->isPdf() ? 'text-red-500' : 'text-blue-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-700 truncate">{{ $pj->nom_fichier }}</p>
                                <p class="text-xs text-slate-400">{{ $pj->taille_humaine }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Decision panel --}}
            @if($requete->statut->value === 'en_cours_verification')
            <div class="card" x-data="{ decision: '', showNote: false, showMotif: false }">
                <h2 class="text-sm font-bold text-slate-700 mb-5 uppercase tracking-wide">Rendre une décision</h2>

                <form method="POST" action="{{ route('agent.requetes.statut', $requete->id) }}">
                    @csrf @method('PATCH')

                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-surface-container hover:border-green-300 has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                            <input type="radio" name="decision" value="traitee_fondee" x-model="decision"
                                   @change="showNote = true; showMotif = false" class="sr-only">
                            <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0" :class="decision === 'traitee_fondee' ? 'border-green-500 bg-green-500' : ''"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">✅ Fondée</p>
                                <p class="text-xs text-slate-500">Anomalie confirmée — correction à effectuer</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all border-surface-container hover:border-red-300 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                            <input type="radio" name="decision" value="traitee_non_fondee" x-model="decision"
                                   @change="showMotif = true; showNote = false" class="sr-only">
                            <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0" :class="decision === 'traitee_non_fondee' ? 'border-red-500 bg-red-500' : ''"></div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">❌ Non fondée</p>
                                <p class="text-xs text-slate-500">Anomalie infirmée — pas de correction</p>
                            </div>
                        </label>
                    </div>

                    <div x-show="showNote" x-transition class="mb-4">
                        <label class="form-label">Nouvelle note corrigée <span class="text-red-500">*</span></label>
                        <input type="number" name="nouvelle_note" step="0.25" min="0" max="20"
                               class="form-input max-w-xs" placeholder="Ex: 14.75">
                    </div>

                    <div x-show="showMotif" x-transition class="mb-4">
                        <label class="form-label">Motif de rejet <span class="text-red-500">*</span></label>
                        <textarea name="motif_rejet" rows="3" maxlength="1000"
                                  class="form-input resize-none"
                                  placeholder="Expliquez pourquoi la requête est non fondée..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary"
                                :disabled="!decision" :class="{ 'opacity-40 cursor-not-allowed': !decision }">
                            Valider la décision
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Note si fondée --}}
            @if($requete->note)
            <div class="card border-l-4 border-green-500">
                <h2 class="text-sm font-bold text-green-700 mb-3 uppercase tracking-wide">Correction effectuée</h2>
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-xs text-slate-500 mb-1">Avant</p>
                        <span class="text-2xl font-bold text-red-600">{{ $requete->note->note_avant ?? '—' }}</span><span class="text-sm text-slate-400">/20</span>
                    </div>
                    <span class="text-2xl text-slate-300">→</span>
                    <div class="text-center">
                        <p class="text-xs text-slate-500 mb-1">Après</p>
                        <span class="text-2xl font-bold text-green-600">{{ $requete->note->note_apres }}</span><span class="text-sm text-slate-400">/20</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Timeline --}}
        <div class="col-span-1 space-y-4">
            <div class="card">
                <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Historique</h2>
                <ol class="relative border-l border-surface-container pl-4 space-y-4">
                    <li>
                        <div class="absolute -left-1.5 mt-0.5 w-3 h-3 rounded-full bg-primary"></div>
                        <p class="text-xs font-semibold text-slate-800">Soumission</p>
                        <p class="text-xs text-slate-400">{{ $requete->date_soumission->format('d/m/Y H:i') }}</p>
                    </li>
                    @if($requete->date_prise_en_charge)
                    <li>
                        <div class="absolute -left-1.5 mt-0.5 w-3 h-3 rounded-full bg-blue-400"></div>
                        <p class="text-xs font-semibold text-slate-800">Prise en charge</p>
                        <p class="text-xs text-slate-400">{{ $requete->date_prise_en_charge->format('d/m/Y H:i') }}</p>
                        @if($requete->agent)
                            <p class="text-xs text-slate-500">{{ $requete->agent->utilisateur->nom_complet }}</p>
                        @endif
                    </li>
                    @endif
                    @if($requete->date_traitement)
                    <li>
                        <div class="absolute -left-1.5 mt-0.5 w-3 h-3 rounded-full {{ $requete->statut->value === 'traitee_fondee' ? 'bg-green-500' : 'bg-red-400' }}"></div>
                        <p class="text-xs font-semibold text-slate-800">Décision</p>
                        <p class="text-xs text-slate-400">{{ $requete->date_traitement->format('d/m/Y H:i') }}</p>
                    </li>
                    @endif
                </ol>
            </div>

            @if($requete->motif_rejet)
            <div class="card border border-red-200 bg-red-50">
                <h2 class="text-xs font-bold text-red-700 mb-2 uppercase tracking-wide">Motif de rejet</h2>
                <p class="text-sm text-red-800">{{ $requete->motif_rejet }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
