@extends('layouts.app')
@section('title', 'Mon Historique')

@section('content')
<div class="pt-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Historique de mes actions</h1>
        <p class="text-sm text-slate-500 mt-1">{{ number_format($historiques->total()) }} action(s) enregistrée(s).</p>
    </div>

    <div class="card p-0 overflow-hidden">
        @if($historiques->isEmpty())
            <div class="p-6">
                <x-empty-state title="Aucune action" description="Votre historique d'actions est vide." />
            </div>
        @else
            <table class="w-full">
                <thead class="bg-surface border-b border-surface-container">
                    <tr>
                        <th class="table-th">Date</th>
                        <th class="table-th">Action</th>
                        <th class="table-th">Requête</th>
                        <th class="table-th">Détails</th>
                        <th class="table-th">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historiques as $h)
                    <tr class="table-tr">
                        <td class="table-td text-xs text-slate-500 whitespace-nowrap">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                        <td class="table-td">
                            <span class="badge badge-blue">{{ str_replace('_', ' ', $h->type_action) }}</span>
                        </td>
                        <td class="table-td">
                            @if($h->requete)
                                <a href="{{ route('agent.requetes.show', $h->requete_id) }}"
                                   class="font-mono text-xs font-bold text-primary hover:underline">
                                    {{ $h->requete->ref_requete }}
                                </a>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="table-td text-sm text-slate-600 max-w-xs">{{ Str::limit($h->details, 80) }}</td>
                        <td class="table-td font-mono text-xs text-slate-400">{{ $h->ip_address }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-5 py-4 border-t border-surface-container">
                {{ $historiques->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
