@extends('layouts.app')
@section('title', 'Ajouter un élément')

@section('content')
<div class="pt-4 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.filieres.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Ajouter un élément de référentiel</h1>
    </div>

    <div class="card" x-data="{ type: '{{ old('type', 'session') }}' }">

        <div class="mb-5">
            <label class="form-label">Type d'élément</label>
            <div class="flex gap-3 mt-2">
                @foreach(['session' => 'Session d\'examen','filiere' => 'Filière','cours' => 'Cours'] as $val => $lbl)
                <label class="flex items-center gap-2 cursor-pointer px-4 py-2 rounded-lg border transition-colors"
                       :class="type==='{{ $val }}' ? 'border-primary bg-primary/5 text-primary font-semibold' : 'border-surface-container text-slate-600 hover:border-primary/40'">
                    <input type="radio" name="type" value="{{ $val }}" x-model="type" class="hidden">
                    {{ $lbl }}
                </label>
                @endforeach
            </div>
        </div>

        <form method="POST" action="{{ route('admin.filieres.store') }}">
            @csrf
            <input type="hidden" name="type" :value="type">

            {{-- Session fields --}}
            <div x-show="type==='session'" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" class="form-input" placeholder="Ex: SN2-2024">
                    </div>
                    <div>
                        <label class="form-label">Année académique <span class="text-danger">*</span></label>
                        <input type="text" name="annee_acad" value="{{ old('annee_acad') }}" class="form-input" placeholder="Ex: 2023-2024">
                    </div>
                </div>
                <div>
                    <label class="form-label">Libellé <span class="text-danger">*</span></label>
                    <input type="text" name="libelle" value="{{ old('libelle') }}" class="form-input" placeholder="Ex: Session normale 2e année">
                </div>
                <div>
                    <label class="form-label">Date de publication des résultats</label>
                    <input type="datetime-local" name="date_publication" value="{{ old('date_publication') }}" class="form-input">
                    <p class="text-xs text-slate-400 mt-1">La fenêtre de soumission des requêtes sera ouverte 72h après cette date.</p>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="est_active" id="est_active" value="1" {{ old('est_active') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary/20">
                    <label for="est_active" class="text-sm font-medium text-slate-700">Session active</label>
                </div>
            </div>

            {{-- Filiere fields --}}
            <div x-show="type==='filiere'" x-cloak class="space-y-4">
                <div>
                    <label class="form-label">Département <span class="text-danger">*</span></label>
                    <select name="departement_id" class="form-select">
                        <option value="">— Choisir —</option>
                        @foreach($departements as $d)
                            <option value="{{ $d->id }}" {{ old('departement_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->nom_dep }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Nom de la filière <span class="text-danger">*</span></label>
                    <input type="text" name="nom_filiere" value="{{ old('nom_filiere') }}" class="form-input" placeholder="Ex: Génie Logiciel">
                </div>
                <div>
                    <label class="form-label">Cycle <span class="text-danger">*</span></label>
                    <select name="cycle" class="form-select">
                        <option value="">— Choisir —</option>
                        @foreach(['BTS','HND','LP'] as $c)
                            <option value="{{ $c }}" {{ old('cycle') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Cours fields --}}
            <div x-show="type==='cours'" x-cloak class="space-y-4">
                <div>
                    <label class="form-label">Filière <span class="text-danger">*</span></label>
                    <select name="filiere_id" class="form-select">
                        <option value="">— Choisir —</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}" {{ old('filiere_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->nom_filiere }} ({{ $f->cycle }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Nom du cours <span class="text-danger">*</span></label>
                    <input type="text" name="nom_cours" value="{{ old('nom_cours') }}" class="form-input" placeholder="Ex: Algorithmique avancée">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Niveau <span class="text-danger">*</span></label>
                        <select name="niveau" class="form-select">
                            <option value="">—</option>
                            <option value="1" {{ old('niveau') == 1 ? 'selected' : '' }}>Niveau 1</option>
                            <option value="2" {{ old('niveau') == 2 ? 'selected' : '' }}>Niveau 2</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Crédits</label>
                        <input type="number" name="credits" value="{{ old('credits') }}" class="form-input" min="1" max="10">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.filieres.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
