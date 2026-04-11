@extends('layouts.app')
@section('title', 'Statistiques')

@section('content')
<div class="pt-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Statistiques</h1>
        <p class="text-sm text-slate-500 mt-1">Analyse détaillée de l'activité de la plateforme.</p>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-6">
        <x-card-stat :value="$stats['total_etudiants']" label="Étudiants inscrits" color="blue"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 7v-7"/></svg>'/>
        <x-card-stat :value="$stats['total_agents']" label="Agents actifs" color="green"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'/>
        <x-card-stat :value="$stats['total_requetes']" label="Total requêtes" color="purple"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>'/>
        <x-card-stat :value="$stats['taux_traitement'] . '%'" label="Taux traitement" color="amber"
            icon='<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'/>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="card">
            <h2 class="text-base font-bold text-slate-800 mb-5">Requêtes par mois — {{ now()->year }}</h2>
            <div class="relative h-64">
                <canvas id="chartMois"></canvas>
            </div>
        </div>

        <div class="card">
            <h2 class="text-base font-bold text-slate-800 mb-5">Répartition par statut</h2>
            <div class="relative h-64">
                <canvas id="chartStatuts"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const moisLabels = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
new Chart(document.getElementById('chartMois'), {
    type: 'line',
    data: {
        labels: moisLabels,
        datasets: [{
            label: 'Requêtes',
            data: @json(array_values($requetesParMois)),
            borderColor: '#002444', backgroundColor: 'rgba(0,36,68,0.08)',
            borderWidth: 2, fill: true, tension: 0.4, pointRadius: 4,
        }]
    },
    options: { responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } }
    }
});

new Chart(document.getElementById('chartStatuts'), {
    type: 'doughnut',
    data: {
        labels: ['En attente', 'En cours', 'Fondées', 'Non fondées'],
        datasets: [{
            data: [{{ $stats['en_attente'] }}, {{ $stats['en_cours'] }}, {{ $stats['traitees_fondees'] }}, {{ $stats['traitees_non_fondees'] }}],
            backgroundColor: ['#f59e0b','#3b82f6','#16a34a','#dc2626'],
            borderWidth: 0,
        }]
    },
    options: { responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 11 } } } }
    }
});
</script>
@endpush
