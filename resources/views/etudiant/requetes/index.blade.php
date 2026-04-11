@extends('layouts.app')
@section('title', 'Mes Requêtes')

@section('content')
<div class="pt-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Mes Requêtes</h1>
            <p class="text-sm text-slate-500 mt-1">Gérez et suivez l'évolution de vos demandes académiques.</p>
        </div>
        <a href="{{ route('etudiant.requetes.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouvelle requête
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-5">
        <form method="GET" class="flex items-center gap-3 flex-wrap">
            @foreach([''=>'Tous les statuts','en_attente'=>'En attente','en_cours_verification'=>'En cours','traitee_fondee'=>'Fondée','traitee_non_fondee'=>'Non fondée'] as $val => $lib)
            <a href="{{ route('etudiant.requetes.index', $val ? ['statut'=>$val] : []) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition-all
                      {{ request('statut', '') === $val ? 'bg-primary text-white' : 'bg-surface-container text-slate-600 hover:bg-slate-200' }}">
                {{ $lib }}
            </a>
            @endforeach
        </form>
    </div>

    {{-- List --}}
    @if($requetes->isEmpty())
        <div class="card">
            <x-empty-state
                title="Aucune requête trouvée"
                description="Aucune requête ne correspond à vos critères de filtrage."
                actionLabel="Soumettre une requête"
                :actionRoute="route('etudiant.requetes.create')" />
        </div>
    @else
        <div class="card">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-surface-container">
                        <th class="table-th">Référence</th>
                        <th class="table-th">Cours</th>
                        <th class="table-th">Session</th>
                        <th class="table-th">Type</th>
                        <th class="table-th">Date</th>
                        <th class="table-th">Statut</th>
                        <th class="table-th"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requetes as $requete)
                    <tr class="table-tr">
                        <td class="table-td font-mono text-xs font-semibold text-primary">{{ $requete->ref_requete }}</td>
                        <td class="table-td">
                            <p class="font-medium text-slate-800 text-sm">{{ Str::limit($requete->cours->nom_cours, 30) }}</p>
                        </td>
                        <td class="table-td text-xs text-slate-500">{{ $requete->session->code }}</td>
                        <td class="table-td text-xs">{{ $requete->type_anomalie->label() }}</td>
                        <td class="table-td text-xs text-slate-500">{{ $requete->date_soumission->format('d/m/Y') }}</td>
                        <td class="table-td"><x-badge-statut :statut="$requete->statut" /></td>
                        <td class="table-td">
                            <a href="{{ route('etudiant.requetes.show', $requete->id) }}"
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
            <div class="mt-4 border-t border-surface-container pt-4">
                {{ $requetes->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
