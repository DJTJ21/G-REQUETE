@extends('layouts.app')
@section('title', 'PV ' . $pv->ref_pv)

@section('content')
<div class="pt-4 max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-slate-800">Procès-Verbal {{ $pv->ref_pv }}</h1>
                <span class="badge badge-green">Généré</span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Généré le {{ $pv->date_generation->format('d/m/Y à H:i') }}
                — Session : {{ $pv->session->libelle }}
            </p>
        </div>
        <a href="{{ route('agent.pv.download', $pv->id) }}"
           class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Télécharger PDF
        </a>
    </div>

    <div class="card p-0 overflow-hidden">
        <table class="w-full">
            <thead class="bg-surface border-b border-surface-container">
                <tr>
                    <th class="table-th">#</th>
                    <th class="table-th">Réf. Requête</th>
                    <th class="table-th">Étudiant</th>
                    <th class="table-th">Cours</th>
                    <th class="table-th">Statut</th>
                    <th class="table-th">Note Avant</th>
                    <th class="table-th">Note Après</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pv->lignes->sortBy('ordre_affichage') as $ligne)
                @php $req = $ligne->requete->load(['etudiant.utilisateur', 'cours', 'note']); @endphp
                <tr class="table-tr">
                    <td class="table-td text-slate-400">{{ $ligne->ordre_affichage + 1 }}</td>
                    <td class="table-td font-mono text-xs font-bold text-primary">{{ $req->ref_requete }}</td>
                    <td class="table-td">
                        <p class="font-medium text-sm">{{ $req->etudiant->utilisateur->nom_complet }}</p>
                        <p class="text-xs text-slate-400">{{ $req->etudiant->matricule }}</p>
                    </td>
                    <td class="table-td text-sm">{{ Str::limit($req->cours->nom_cours, 25) }}</td>
                    <td class="table-td"><x-badge-statut :statut="$req->statut" /></td>
                    <td class="table-td text-sm font-semibold text-red-600">
                        {{ $req->note?->note_avant ?? '—' }}
                    </td>
                    <td class="table-td text-sm font-semibold text-green-600">
                        {{ $req->note?->note_apres ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-xs text-right text-slate-400">
        {{ $pv->lignes->count() }} requête(s) incluse(s) — Signé par {{ $pv->agent->utilisateur->nom_complet }}
    </div>
</div>
@endsection
