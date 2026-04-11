@extends('layouts.app')
@section('title', "Journal d'audit")

@section('content')
<div class="pt-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Journal d'audit</h1>
            <p class="text-sm text-slate-500 mt-1">{{ number_format($totalActions) }} action(s) enregistrée(s) au total.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.export.journal') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exporter CSV
            </a>
            <a href="{{ route('admin.export.journal.pdf') }}" class="btn-secondary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Exporter PDF
            </a>
        </div>
    </div>

    <div class="flex gap-6 items-start">

        {{-- ── Panneau filtres gauche ── --}}
        <div class="w-64 flex-shrink-0">
            <div class="card sticky top-4">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Filtres de recherche</h2>
                <form method="GET" id="filter-form" class="space-y-5">

                    {{-- Période --}}
                    <div>
                        <label class="form-label">Période</label>
                        <div class="space-y-2">
                            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                                   class="form-input w-full text-sm" placeholder="Du">
                            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                                   class="form-input w-full text-sm" placeholder="Au">
                        </div>
                    </div>

                    {{-- Type d'action --}}
                    <div>
                        <label class="form-label">Type d'action</label>
                        <div class="space-y-1.5">
                            @php
                                $types = [
                                    'decision_requete' => ['Décision rendue', 'bg-blue-100 text-blue-700'],
                                    'generation_pv'    => ['Génération de PV', 'bg-amber-100 text-amber-700'],
                                    'prise_en_charge'  => ['Prise en charge', 'bg-purple-100 text-purple-700'],
                                    'creation_compte'  => ['Création compte', 'bg-green-100 text-green-700'],
                                    'toggle_compte'    => ['Toggle compte', 'bg-orange-100 text-orange-700'],
                                    'import_csv'       => ['Import CSV', 'bg-slate-100 text-slate-700'],
                                ];
                            @endphp
                            @foreach($types as $val => [$lib, $cls])
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="type" value="{{ $val }}"
                                       {{ request('type') === $val ? 'checked' : '' }}
                                       onchange="document.getElementById('filter-form').submit()"
                                       class="text-primary focus:ring-primary/30">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $cls }}">
                                    {{ $lib }}
                                </span>
                            </label>
                            @endforeach
                            @if(request('type'))
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-500 mt-1">
                                <input type="radio" name="type" value=""
                                       onchange="document.getElementById('filter-form').submit()"
                                       class="text-primary focus:ring-primary/30">
                                Tous les types
                            </label>
                            @endif
                        </div>
                    </div>

                    {{-- Agent --}}
                    <div>
                        <label class="form-label">Agent</label>
                        <select name="agent_id" class="form-select w-full text-sm"
                                onchange="this.form.submit()">
                            <option value="">Tous les agents</option>
                            @foreach($agents as $ag)
                                <option value="{{ $ag->id }}" {{ request('agent_id') == $ag->id ? 'selected' : '' }}>
                                    {{ $ag->utilisateur->nom_complet }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Référence requête --}}
                    <div>
                        <label class="form-label">Réf. Requête</label>
                        <input type="text" name="requete_ref" value="{{ request('requete_ref') }}"
                               placeholder="Ex: REQ-20250101"
                               class="form-input w-full text-sm">
                    </div>

                    <div class="flex flex-col gap-2 pt-1">
                        <button type="submit" class="btn-primary w-full justify-center text-sm py-2">
                            Appliquer
                        </button>
                        <a href="{{ route('admin.journal.index') }}"
                           class="btn-secondary w-full justify-center text-sm py-2">
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Zone timeline ── --}}
        <div class="flex-1 min-w-0">

            @if($historiques->isEmpty())
                <div class="card">
                    <x-empty-state title="Aucune action trouvée" description="Aucun enregistrement ne correspond aux filtres appliqués." />
                </div>
            @else

                {{-- Compteur résultats --}}
                <p class="text-xs text-slate-400 mb-4">
                    Affichage de {{ $historiques->firstItem() }}–{{ $historiques->lastItem() }} sur {{ number_format($historiques->total()) }} entrées
                </p>

                {{-- Timeline --}}
                <div class="relative">
                    {{-- Ligne verticale --}}
                    <div class="absolute left-5 top-0 bottom-0 w-px bg-surface-container"></div>

                    <div class="space-y-1">
                        @foreach($historiques as $h)
                        @php
                            $acteur = $h->agent?->utilisateur ?? $h->admin?->utilisateur;
                            [$dotCls, $badgeCls, $badgeLabel] = match(true) {
                                str_contains($h->type_action, 'decision') && str_contains(strtolower($h->details ?? ''), 'fond') && !str_contains(strtolower($h->details ?? ''), 'non fond')
                                    => ['bg-green-500', 'bg-green-100 text-green-700', 'ACCEPTÉE'],
                                str_contains($h->type_action, 'decision')
                                    => ['bg-red-500', 'bg-red-100 text-red-700', 'REJETÉE'],
                                str_contains($h->type_action, 'pv')
                                    => ['bg-amber-500', 'bg-amber-100 text-amber-700', 'PV GÉNÉRÉ'],
                                str_contains($h->type_action, 'prise')
                                    => ['bg-blue-500', 'bg-blue-100 text-blue-700', 'EN COURS'],
                                str_contains($h->type_action, 'creation')
                                    => ['bg-emerald-500', 'bg-emerald-100 text-emerald-700', 'CRÉATION'],
                                str_contains($h->type_action, 'toggle')
                                    => ['bg-orange-500', 'bg-orange-100 text-orange-700', 'TOGGLE'],
                                str_contains($h->type_action, 'import')
                                    => ['bg-purple-500', 'bg-purple-100 text-purple-700', 'IMPORT'],
                                default => ['bg-slate-400', 'bg-slate-100 text-slate-600', strtoupper(str_replace('_', ' ', $h->type_action))],
                            };
                            $initiales = $acteur ? strtoupper(mb_substr($acteur->prenom ?? '', 0, 1) . mb_substr($acteur->nom ?? '', 0, 1)) : 'SY';
                        @endphp
                        <div class="relative flex items-start gap-4 pl-2 py-3 group">
                            {{-- Dot --}}
                            <div class="relative z-10 flex-shrink-0 mt-1">
                                <div class="w-6 h-6 rounded-full {{ $dotCls }} flex items-center justify-center shadow-sm ring-2 ring-white">
                                    <div class="w-2 h-2 rounded-full bg-white/60"></div>
                                </div>
                            </div>

                            {{-- Contenu --}}
                            <div class="flex-1 bg-white rounded-xl border border-surface-container p-4 shadow-card
                                        group-hover:border-slate-300 transition-colors min-w-0">
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $badgeCls }}">
                                            {{ $badgeLabel }}
                                        </span>
                                        @if($h->requete)
                                            <span class="font-mono text-xs font-bold text-primary bg-primary/5 px-2 py-0.5 rounded">
                                                {{ $h->requete->ref_requete }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        {{-- Heure --}}
                                        <span class="text-xs text-slate-400 whitespace-nowrap">
                                            {{ $h->created_at->format('d/m/Y') }}
                                            <span class="text-slate-300 mx-0.5">·</span>
                                            {{ $h->created_at->format('H:i') }}
                                        </span>
                                        {{-- Avatar --}}
                                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0"
                                             title="{{ $acteur?->nom_complet ?? 'Système' }}">
                                            <span class="text-[9px] font-bold text-primary">{{ $initiales }}</span>
                                        </div>
                                    </div>
                                </div>

                                <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                                    @if($acteur)
                                        <span class="font-semibold text-slate-900">{{ $acteur->nom_complet }}</span>
                                        <span class="text-slate-400 text-xs">({{ $acteur->role->label() }})</span>
                                        —
                                    @endif
                                    {{ Str::limit($h->details, 120) }}
                                </p>

                                @if($h->ip_address)
                                <p class="mt-1.5 font-mono text-[10px] text-slate-400">
                                    <svg class="w-3 h-3 inline-block mr-1 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    {{ $h->ip_address }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $historiques->links() }}
                </div>

            @endif

            {{-- ── Stats cards ── --}}
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="card text-center">
                    <p class="text-3xl font-black text-primary">{{ number_format($totalActions) }}</p>
                    <p class="text-xs text-slate-500 mt-1 uppercase tracking-wide">Total actions</p>
                </div>
                <div class="card text-center">
                    <p class="text-3xl font-black text-blue-600">{{ number_format($totalDecisions) }}</p>
                    <p class="text-xs text-slate-500 mt-1 uppercase tracking-wide">Décisions</p>
                </div>
                <div class="card text-center">
                    <p class="text-3xl font-black text-amber-600">{{ number_format($totalPV) }}</p>
                    <p class="text-xs text-slate-500 mt-1 uppercase tracking-wide">PV générés</p>
                </div>
            </div>

        </div>{{-- fin timeline --}}
    </div>{{-- fin flex --}}
</div>
@endsection
