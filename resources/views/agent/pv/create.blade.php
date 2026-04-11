@extends('layouts.app')
@section('title', 'Génération de Procès-Verbal')

@section('content')
<div class="pt-4" id="pv-app">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Génération de Procès-Verbal</h1>
        <p class="text-sm text-slate-500 mt-1">Sélectionnez une session et une filière pour inclure automatiquement toutes les requêtes traitées.</p>
    </div>

    @if(session('error'))
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('agent.pv.store') }}" id="pv-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Colonne gauche : config + récapitulatif ── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Configuration --}}
                <div class="card">
                    <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Configuration du Lot
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Session académique</label>
                            <select name="session_id" id="sel-session" class="form-select" required>
                                <option value="">— Sélectionner —</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->libelle }} ({{ $session->annee_acad }})
                                    </option>
                                @endforeach
                            </select>
                            @error('session_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Filière / Département</label>
                            <select name="filiere_id" id="sel-filiere" class="form-select" required>
                                <option value="">— Sélectionner —</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom_filiere }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filiere_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Récapitulatif --}}
                <div class="card" id="recap-section" style="display:none">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Récapitulatif des Requêtes Traitées
                        </h2>
                        <div class="flex items-center gap-3" id="recap-badges">
                            <span class="badge badge-green text-xs" id="badge-fondees">0 Fondées</span>
                            <span class="badge badge-red text-xs" id="badge-non-fondees">0 Non fondées</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700" id="badge-total">0 Total</span>
                        </div>
                    </div>

                    {{-- Spinner --}}
                    <div id="recap-loading" class="flex items-center justify-center py-8 gap-3 text-slate-400" style="display:none!important">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Chargement…
                    </div>

                    {{-- Empty state --}}
                    <div id="recap-empty" class="py-8 text-center text-slate-400 text-sm" style="display:none">
                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Aucune requête traitée pour cette combinaison.
                    </div>

                    {{-- Table --}}
                    <div id="recap-table-wrap" style="display:none">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-surface-container">
                                    <th class="table-th">Étudiant</th>
                                    <th class="table-th">Matière / Objet</th>
                                    <th class="table-th">Décision</th>
                                    <th class="table-th text-center">Note Av.</th>
                                    <th class="table-th text-center">Note Ap.</th>
                                </tr>
                            </thead>
                            <tbody id="recap-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── Colonne droite : aperçu + action ── --}}
            <div class="space-y-5">

                {{-- Aperçu PDF --}}
                <div class="card">
                    <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Aperçu du PV
                    </h2>
                    <div id="preview-placeholder"
                         class="rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center py-12 text-center text-slate-400 text-sm gap-2">
                        <svg class="w-10 h-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Sélectionnez une session et une filière pour voir l'aperçu
                    </div>
                    <div id="preview-content" style="display:none"
                         class="rounded-xl border border-slate-200 bg-white overflow-hidden text-xs">
                        <div class="bg-primary px-4 py-3 text-white font-bold text-center text-sm" id="prev-title">—</div>
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-slate-500 text-[11px] uppercase tracking-wide mb-1">Filière</p>
                            <p class="font-semibold text-slate-700" id="prev-filiere">—</p>
                        </div>
                        <div class="px-4 py-3 grid grid-cols-3 gap-2 text-center">
                            <div>
                                <p class="text-2xl font-black text-green-600" id="prev-fondees">0</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide">Fondées</p>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-red-500" id="prev-non-fondees">0</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide">Non fond.</p>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-primary" id="prev-total">0</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide">Total</p>
                            </div>
                        </div>
                        <div class="px-4 pb-3 text-center text-[10px] text-slate-400">
                            Format A4 paysage · PDF signé
                        </div>
                    </div>
                </div>

                {{-- Bouton générer --}}
                <button type="submit" id="btn-generer"
                        class="btn-primary w-full justify-center py-3.5 text-sm opacity-50 cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Générer le PV
                </button>

            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const selSession = document.getElementById('sel-session');
    const selFiliere = document.getElementById('sel-filiere');
    const recapSection = document.getElementById('recap-section');
    const recapLoading = document.getElementById('recap-loading');
    const recapEmpty   = document.getElementById('recap-empty');
    const recapWrap    = document.getElementById('recap-table-wrap');
    const recapTbody   = document.getElementById('recap-tbody');
    const badgeFondees    = document.getElementById('badge-fondees');
    const badgeNonFondees = document.getElementById('badge-non-fondees');
    const badgeTotal      = document.getElementById('badge-total');
    const preview       = document.getElementById('preview-content');
    const placeholder   = document.getElementById('preview-placeholder');
    const prevTitle     = document.getElementById('prev-title');
    const prevFiliere   = document.getElementById('prev-filiere');
    const prevFondees   = document.getElementById('prev-fondees');
    const prevNonFondees= document.getElementById('prev-non-fondees');
    const prevTotal     = document.getElementById('prev-total');
    const btnGenerer    = document.getElementById('btn-generer');

    const sessionLabels = {};
    const filiereLabels = {};
    selSession.querySelectorAll('option[value]').forEach(o => { if(o.value) sessionLabels[o.value] = o.textContent.trim(); });
    selFiliere.querySelectorAll('option[value]').forEach(o => { if(o.value) filiereLabels[o.value] = o.textContent.trim(); });

    function statut_label(s) {
        return s === 'traitee_fondee' ? 'Fondée' : 'Non fondée';
    }
    function statut_cls(s) {
        return s === 'traitee_fondee' ? 'badge-green' : 'badge-red';
    }

    async function load() {
        const sid = selSession.value;
        const fid = selFiliere.value;
        if (!sid || !fid) {
            recapSection.style.display = 'none';
            preview.style.display = 'none';
            placeholder.style.display = '';
            btnGenerer.disabled = true;
            btnGenerer.classList.add('opacity-50','cursor-not-allowed');
            return;
        }

        recapSection.style.display = '';
        recapLoading.style.display = '';
        recapEmpty.style.display   = 'none';
        recapWrap.style.display    = 'none';

        try {
            const url = '{{ route("agent.pv.apercu") }}?session_id=' + sid + '&filiere_id=' + fid;
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
            const data = await res.json();

            recapLoading.style.display = 'none';
            badgeFondees.textContent    = data.fondees + ' Fondée' + (data.fondees > 1 ? 's' : '');
            badgeNonFondees.textContent = data.non_fondees + ' Non fondée' + (data.non_fondees > 1 ? 's' : '');
            badgeTotal.textContent      = data.total + ' Total';

            if (data.total === 0) {
                recapEmpty.style.display = '';
                preview.style.display    = 'none';
                placeholder.style.display = '';
                btnGenerer.disabled = true;
                btnGenerer.classList.add('opacity-50','cursor-not-allowed');
                return;
            }

            recapTbody.innerHTML = '';
            data.requetes.forEach(function(r) {
                const tr = document.createElement('tr');
                tr.className = 'table-tr';
                tr.innerHTML =
                    '<td class="table-td">' +
                        '<p class="text-sm font-medium text-slate-800">' + r.etudiant + '</p>' +
                        '<p class="text-xs text-slate-400">' + r.matricule + '</p>' +
                    '</td>' +
                    '<td class="table-td text-sm text-slate-600">' + r.cours + '</td>' +
                    '<td class="table-td"><span class="badge ' + statut_cls(r.statut) + ' text-xs">' + statut_label(r.statut) + '</span></td>' +
                    '<td class="table-td text-center text-sm">' + (r.note_av !== null ? r.note_av + '/20' : '<span class="text-slate-300">—</span>') + '</td>' +
                    '<td class="table-td text-center text-sm font-semibold">' + (r.note_ap !== null ? '<span class="text-green-600">' + r.note_ap + '/20</span>' : '<span class="text-slate-300">—</span>') + '</td>';
                recapTbody.appendChild(tr);
            });
            recapWrap.style.display = '';

            prevTitle.textContent      = sessionLabels[sid] || '—';
            prevFiliere.textContent    = filiereLabels[fid] || '—';
            prevFondees.textContent    = data.fondees;
            prevNonFondees.textContent = data.non_fondees;
            prevTotal.textContent      = data.total;
            preview.style.display      = '';
            placeholder.style.display  = 'none';

            btnGenerer.disabled = false;
            btnGenerer.classList.remove('opacity-50','cursor-not-allowed');

        } catch(e) {
            recapLoading.style.display = 'none';
            recapEmpty.style.display   = '';
        }
    }

    selSession.addEventListener('change', load);
    selFiliere.addEventListener('change', load);

    if (selSession.value && selFiliere.value) load();
})();
</script>
@endpush
@endsection
