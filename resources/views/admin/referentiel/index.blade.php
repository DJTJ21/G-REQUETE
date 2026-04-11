@extends('layouts.app')
@section('title', 'Référentiels')

@section('content')
<div class="pt-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Gestion des référentiels</h1>
            <p class="text-sm text-slate-500 mt-1">Filières, cours et sessions d'examens.</p>
        </div>
        <a href="{{ route('admin.filieres.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter
        </a>
    </div>

    <div x-data="{ tab: 'sessions' }" class="space-y-5">

        {{-- Tabs --}}
        <div class="flex gap-2 border-b border-surface-container">
            @foreach([['sessions','Sessions'],['filieres','Filières'],['cours','Cours']] as [$key,$label])
            <button @click="tab='{{ $key }}'"
                    :class="tab==='{{ $key }}' ? 'border-b-2 border-primary text-primary font-semibold' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 text-sm transition-colors">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Sessions --}}
        <div x-show="tab==='sessions'">
            <div class="card p-0 overflow-hidden">
                @if($sessions->isEmpty())
                    <div class="p-6"><x-empty-state title="Aucune session" description="Aucune session d'examen n'a été créée." /></div>
                @else
                    <table class="w-full">
                        <thead class="bg-surface border-b border-surface-container">
                            <tr>
                                <th class="table-th">Code</th>
                                <th class="table-th">Libellé</th>
                                <th class="table-th">Année acad.</th>
                                <th class="table-th">Date publication</th>
                                <th class="table-th">Statut</th>
                                <th class="table-th">Requêtes</th>
                                <th class="table-th">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $s)
                            <tr class="table-tr">
                                <td class="table-td font-mono text-xs font-bold text-primary">{{ $s->code }}</td>
                                <td class="table-td font-medium">{{ $s->libelle }}</td>
                                <td class="table-td text-sm">{{ $s->annee_acad }}</td>
                                <td class="table-td text-sm">{{ $s->date_publication?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="table-td">
                                    <span class="badge {{ $s->est_active ? 'badge-green' : 'badge-gray' }}">
                                        {{ $s->est_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="table-td text-sm">{{ $s->requetes_count ?? $s->requetes->count() }}</td>
                                <td class="table-td">
                                    <form method="POST"
                                          action="{{ route('admin.sessions.toggle-actif', $s->id) }}"
                                          onsubmit="return confirm('{{ $s->est_active ? 'Désactiver' : 'Activer' }} la session \'{{ addslashes($s->libelle) }}\' ?')">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-medium px-2.5 py-1 rounded-lg border transition-colors
                                                       {{ $s->est_active
                                                            ? 'border-red-200 text-red-600 hover:bg-red-50'
                                                            : 'border-green-200 text-green-700 hover:bg-green-50' }}">
                                            {{ $s->est_active ? 'Désactiver' : 'Activer' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-5 py-4 border-t border-surface-container">{{ $sessions->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Filières --}}
        <div x-show="tab==='filieres'" x-cloak>
            <div class="card p-0 overflow-hidden">
                @if($filieres->isEmpty())
                    <div class="p-6"><x-empty-state title="Aucune filière" description="Aucune filière n'a été créée." /></div>
                @else
                    <table class="w-full">
                        <thead class="bg-surface border-b border-surface-container">
                            <tr>
                                <th class="table-th">Filière</th>
                                <th class="table-th">Cycle</th>
                                <th class="table-th">Département</th>
                                <th class="table-th">Cours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filieres as $f)
                            <tr class="table-tr">
                                <td class="table-td font-medium">{{ $f->nom_filiere }}</td>
                                <td class="table-td"><span class="badge badge-blue">{{ $f->cycle instanceof \App\Enums\CycleFiliere ? $f->cycle->value : $f->cycle }}</span></td>
                                <td class="table-td text-sm text-slate-600">{{ $f->departement?->nom_dep ?? '—' }}</td>
                                <td class="table-td text-sm">{{ $f->cours_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-5 py-4 border-t border-surface-container">{{ $filieres->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Cours --}}
        <div x-show="tab==='cours'" x-cloak>
            <div class="card p-0 overflow-hidden">
                @if($cours->isEmpty())
                    <div class="p-6"><x-empty-state title="Aucun cours" description="Aucun cours n'a été créé." /></div>
                @else
                    <table class="w-full">
                        <thead class="bg-surface border-b border-surface-container">
                            <tr>
                                <th class="table-th">Cours</th>
                                <th class="table-th">Filière</th>
                                <th class="table-th">Niveau</th>
                                <th class="table-th">Crédits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cours as $c)
                            <tr class="table-tr">
                                <td class="table-td font-medium">{{ $c->nom_cours }}</td>
                                <td class="table-td text-sm text-slate-600">{{ $c->filiere?->nom_filiere ?? '—' }}</td>
                                <td class="table-td"><span class="badge badge-gray">Niveau {{ $c->niveau }}</span></td>
                                <td class="table-td text-sm">{{ $c->credits ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-5 py-4 border-t border-surface-container">{{ $cours->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
