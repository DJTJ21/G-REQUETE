@extends('layouts.app')
@section('title', 'Tableau de bord')

@section('content')
<div class="pt-4">
    {{-- Greeting --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Ravi de vous revoir, {{ $user->prenom }} 👋
        </h1>
        <p class="text-sm text-slate-500 mt-1">Voici l'état de vos demandes académiques pour le semestre en cours.</p>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        {{-- Total --}}
        <div class="card col-span-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Total Requêtes</p>
            <p class="text-4xl font-bold text-slate-800">{{ $stats['total'] }}</p>
            <div class="mt-4 bg-surface rounded-lg p-3">
                <div class="flex justify-between text-xs text-slate-600 mb-1.5">
                    <span>Taux de traitement</span>
                    <span class="font-semibold">{{ $stats['taux_traitement'] }}%</span>
                </div>
                <div class="w-full bg-surface-container rounded-full h-1.5">
                    <div class="h-1.5 rounded-full" style="width: {{ $stats['taux_traitement'] }}%; background: #002444;"></div>
                </div>
            </div>
        </div>

        {{-- En attente --}}
        <div class="card flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-amber-600 uppercase tracking-wide">En attente</span>
            </div>
            <p class="text-4xl font-bold text-slate-800">{{ str_pad($stats['en_attente'], 2, '0', STR_PAD_LEFT) }}</p>
            <p class="text-xs text-slate-500 mt-2">
                <svg class="w-3 h-3 inline-block text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dernière mise à jour : il y a 2h
            </p>
        </div>

        {{-- Traitées --}}
        <div class="card flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-green-600 uppercase tracking-wide">Requêtes traitées</span>
            </div>
            <p class="text-4xl font-bold text-slate-800">{{ $stats['traitees'] }}</p>
            <p class="text-xs text-green-600 mt-2 font-semibold">
                <svg class="w-3 h-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                </svg>
                +3 depuis hier
            </p>
        </div>
    </div>

    {{-- Historique récent --}}
    <div class="card">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-800">Historique récent</h2>
            </div>
            <a href="{{ route('etudiant.requetes.index') }}"
               class="text-sm text-primary font-semibold flex items-center gap-1 hover:underline">
                Voir tout
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if($requetesRecentes->isEmpty())
            <x-empty-state
                title="Aucune requête pour le moment"
                description="Soumettez votre première requête académique."
                actionLabel="Nouvelle requête"
                :actionRoute="route('etudiant.requetes.create')" />
        @else
            <table class="w-full">
                <thead>
                    <tr class="border-b border-surface-container">
                        <th class="table-th">Date</th>
                        <th class="table-th">Objet</th>
                        <th class="table-th">Matières</th>
                        <th class="table-th">Statut</th>
                        <th class="table-th">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requetesRecentes as $requete)
                    <tr class="table-tr">
                        <td class="table-td">{{ $requete->date_soumission->format('d/m/Y') }}</td>
                        <td class="table-td font-medium">{{ $requete->type_anomalie->label() }}</td>
                        <td class="table-td">
                            <span class="badge badge-gray">{{ Str::upper(Str::limit($requete->cours->nom_cours, 8)) }}</span>
                        </td>
                        <td class="table-td"><x-badge-statut :statut="$requete->statut" /></td>
                        <td class="table-td">
                            <a href="{{ route('etudiant.requetes.show', $requete->id) }}"
                               class="text-slate-400 hover:text-primary transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="text-xs text-slate-400 text-right mt-3">
                Affichage de {{ $requetesRecentes->count() }} sur {{ $stats['total'] }} requêtes
            </p>
        @endif
    </div>
</div>

{{-- FAB --}}
<a href="{{ route('etudiant.requetes.create') }}"
   class="fixed bottom-8 right-8 flex items-center gap-2 btn-primary px-5 py-3 rounded-full shadow-ambient text-sm font-semibold">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Nouvelle Requête
</a>
@endsection
