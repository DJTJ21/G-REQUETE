@extends('layouts.app')
@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="pt-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Utilisateurs</h1>
            <p class="text-sm text-slate-500 mt-1">{{ number_format($utilisateurs->total()) }} compte(s) enregistré(s).</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.utilisateurs.import') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import CSV
            </a>
            <a href="{{ route('admin.utilisateurs.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau compte
            </a>
        </div>
    </div>

    {{-- Search/Filter --}}
    <div class="card mb-5">
        <form method="GET" class="flex items-end gap-3 flex-wrap">
            <div class="flex-1 min-w-48">
                <label class="form-label">Rechercher</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Nom, prénom, email..."
                           class="form-input pl-9">
                </div>
            </div>
            <div>
                <label class="form-label">Rôle</label>
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="etudiant" {{ request('role') === 'etudiant' ? 'selected' : '' }}>Étudiant</option>
                    <option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-primary">Filtrer</button>
            <a href="{{ route('admin.utilisateurs.index') }}" class="btn-secondary">Réinitialiser</a>
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <table class="w-full">
            <thead class="bg-surface border-b border-surface-container">
                <tr>
                    <th class="table-th">Utilisateur</th>
                    <th class="table-th">Email</th>
                    <th class="table-th">Rôle</th>
                    <th class="table-th">Identifiant</th>
                    <th class="table-th">Statut</th>
                    <th class="table-th">Créé le</th>
                    <th class="table-th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($utilisateurs as $u)
                <tr class="table-tr">
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background: linear-gradient(135deg, #002444, #1a3a5c)">
                                {{ strtoupper(substr($u->prenom, 0, 1) . substr($u->nom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $u->nom_complet }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="table-td text-sm text-slate-500">{{ $u->email }}</td>
                    <td class="table-td">
                        @php
                            $roleClasses = ['etudiant' => 'badge-blue', 'agent' => 'badge-green', 'admin' => 'badge-red'];
                        @endphp
                        <span class="{{ $roleClasses[$u->role->value] ?? 'badge-gray' }} badge">{{ $u->role->label() }}</span>
                    </td>
                    <td class="table-td font-mono text-xs">
                        @if($u->isEtudiant()) {{ $u->etudiant?->matricule ?? '—' }}
                        @elseif($u->isAgent()) {{ $u->agent?->matricule_agent ?? '—' }}
                        @else <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="table-td">
                        @if($u->est_actif)
                            <span class="badge badge-green">Actif</span>
                        @else
                            <span class="badge badge-red">Désactivé</span>
                        @endif
                    </td>
                    <td class="table-td text-xs text-slate-500">{{ $u->created_at->format('d/m/Y') }}</td>
                    <td class="table-td">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.utilisateurs.edit', $u->id) }}"
                               class="w-7 h-7 flex items-center justify-center rounded-lg bg-surface-container hover:bg-primary/10 hover:text-primary transition-colors text-slate-500"
                               title="Modifier">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.utilisateurs.toggle-actif', $u->id) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors
                                               {{ $u->est_actif ? 'bg-red-50 text-red-500 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}"
                                        title="{{ $u->est_actif ? 'Désactiver' : 'Activer' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="{{ $u->est_actif ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-6"><x-empty-state title="Aucun utilisateur trouvé" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-surface-container">
            {{ $utilisateurs->links() }}
        </div>
    </div>
</div>
@endsection
