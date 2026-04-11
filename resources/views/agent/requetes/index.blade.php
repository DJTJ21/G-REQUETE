@extends('layouts.app')
@section('title', 'File des Requêtes')

@section('content')
<div class="pt-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">File des Requêtes</h1>
            <p class="text-sm text-slate-500 mt-1">Gestion complète de toutes les requêtes académiques.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-5">
        <form method="GET" class="flex items-end gap-3 flex-wrap">
            <div>
                <label class="form-label">Statut</label>
                <select name="statut" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="en_cours_verification" {{ request('statut') === 'en_cours_verification' ? 'selected' : '' }}>En cours</option>
                    <option value="traitee_fondee" {{ request('statut') === 'traitee_fondee' ? 'selected' : '' }}>Fondée</option>
                    <option value="traitee_non_fondee" {{ request('statut') === 'traitee_non_fondee' ? 'selected' : '' }}>Non fondée</option>
                </select>
            </div>
            <div>
                <label class="form-label">Filière</label>
                <select name="filiere_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes</option>
                    @foreach($filieres as $f)
                        <option value="{{ $f->id }}" {{ request('filiere_id') == $f->id ? 'selected' : '' }}>{{ $f->nom_filiere }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Du</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-input w-36">
            </div>
            <div>
                <label class="form-label">Au</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-input w-36">
            </div>
            <button type="submit" class="btn-primary">Filtrer</button>
            <a href="{{ route('agent.requetes.index') }}" class="btn-secondary">Réinitialiser</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        @if($requetes->isEmpty())
            <div class="p-6">
                <x-empty-state title="Aucune requête" description="Aucune requête ne correspond aux critères sélectionnés." />
            </div>
        @else
            <table class="w-full">
                <thead class="bg-surface border-b border-surface-container">
                    <tr>
                        <th class="table-th">Réf.</th>
                        <th class="table-th">Étudiant</th>
                        <th class="table-th">Filière</th>
                        <th class="table-th">Cours</th>
                        <th class="table-th">Type</th>
                        <th class="table-th">Soumise le</th>
                        <th class="table-th">Agent</th>
                        <th class="table-th">Statut</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requetes as $req)
                    <tr class="table-tr">
                        <td class="table-td font-mono text-xs font-bold text-primary">{{ $req->ref_requete }}</td>
                        <td class="table-td">
                            <p class="font-medium text-sm">{{ $req->etudiant->utilisateur->nom_complet }}</p>
                            <p class="text-xs text-slate-400">{{ $req->etudiant->matricule }}</p>
                        </td>
                        <td class="table-td text-xs text-slate-600">{{ Str::limit($req->etudiant->filiere->nom_filiere, 20) }}</td>
                        <td class="table-td text-xs">{{ Str::limit($req->cours->nom_cours, 20) }}</td>
                        <td class="table-td text-xs text-slate-500">{{ $req->type_anomalie->label() }}</td>
                        <td class="table-td text-xs text-slate-500">{{ $req->date_soumission->format('d/m/Y') }}</td>
                        <td class="table-td text-xs">
                            @if($req->agent)
                                {{ $req->agent->utilisateur->prenom }}
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="table-td"><x-badge-statut :statut="$req->statut" /></td>
                        <td class="table-td">
                            <a href="{{ route('agent.requetes.show', $req->id) }}"
                               class="text-slate-400 hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-4 border-t border-surface-container">
                {{ $requetes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
