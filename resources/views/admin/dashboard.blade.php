@extends('layouts.app')
@section('title', 'Tableau de bord Admin')

@section('content')
<div class="pt-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Tableau de bord</h1>
        <p class="text-sm text-slate-500 mt-1">Vue d'ensemble complète du système G-REQUÊTES.</p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        <x-card-stat :value="$stats['total_etudiants']" label="Étudiants" color="blue"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7v-7m0 7l-9-5m9 5l9-5"/></svg>'/>
        <x-card-stat :value="$stats['total_requetes']" label="Total requêtes" color="purple"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>'/>
        <x-card-stat :value="$stats['en_attente']" label="En attente" color="amber"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'/>
        <x-card-stat :value="$stats['taux_traitement'] . '%'" label="Taux traitement" color="green"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'/>
    </div>

    <div class="grid grid-cols-3 gap-4">
        {{-- Chart: Requêtes par mois --}}
        <div class="col-span-2 card">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-slate-800">Évolution mensuelle {{ now()->year }}</h2>
            </div>
            <div class="relative h-48">
                <canvas id="chartMois"></canvas>
            </div>
        </div>

        {{-- Répartition statuts --}}
        <div class="col-span-1 card">
            <h2 class="text-base font-bold text-slate-800 mb-5">Répartition des statuts</h2>
            <div class="space-y-3">
                @foreach(['en_attente' => ['En attente', 'bg-amber-400'], 'en_cours' => ['En cours', 'bg-blue-400'], 'traitees_fondees' => ['Fondées', 'bg-green-400'], 'traitees_non_fondees' => ['Non fondées', 'bg-red-400']] as $key => [$label, $color])
                @php
                    $val = $stats[$key];
                    $total = $stats['total_requetes'];
                    $pct = $total > 0 ? round(($val / $total) * 100) : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-xs text-slate-600 mb-1">
                        <span class="font-medium">{{ $label }}</span>
                        <span class="font-bold">{{ $val }} <span class="font-normal text-slate-400">({{ $pct }}%)</span></span>
                    </div>
                    <div class="w-full bg-surface-container rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $color }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top agents --}}
        <div class="col-span-1 card">
            <h2 class="text-sm font-bold text-slate-700 mb-4 uppercase tracking-wide">Top agents</h2>
            <div class="space-y-3">
                @foreach($topAgents as $i => $agent)
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-400 w-4">{{ $i + 1 }}</span>
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                         style="background: linear-gradient(135deg, #002444, #1a3a5c)">
                        {{ strtoupper(substr($agent->utilisateur->prenom, 0, 1) . substr($agent->utilisateur->nom, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-800">{{ $agent->utilisateur->nom_complet }}</p>
                        <p class="text-xs text-slate-500">{{ $agent->requetes_traitees }} traitée(s)</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Journal récent --}}
        <div class="col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-800">Journal d'actions récentes</h2>
                <a href="{{ route('admin.journal.index') }}"
                   class="text-sm text-primary font-semibold hover:underline">Voir tout →</a>
            </div>
            <div class="space-y-2">
                @foreach($derniersHistos as $h)
                <div class="flex items-start gap-3 py-2 border-b border-surface-container last:border-0">
                    <div class="w-7 h-7 rounded-lg bg-surface-container flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-700">{{ Str::limit($h->details, 80) }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $h->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="badge badge-gray text-[10px]">{{ str_replace('_', ' ', $h->type_action) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const mois = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
const data = @json(array_values($requetesParMois));
new Chart(document.getElementById('chartMois').getContext('2d'), {
    type: 'bar',
    data: {
        labels: mois,
        datasets: [{
            label: 'Requêtes soumises',
            data: data,
            backgroundColor: 'rgba(0, 36, 68, 0.15)',
            borderColor: '#002444',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
