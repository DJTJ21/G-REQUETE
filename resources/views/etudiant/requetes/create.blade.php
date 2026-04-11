@extends('layouts.app')
@section('title', 'Nouvelle Requête')

@section('content')
<div class="pt-4 max-w-3xl mx-auto">

    {{-- 72h banner --}}
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm mb-5"
         id="fenetre-banner">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex-1">
            <strong>⚠ Attention</strong> — Vous disposez de <strong>72h</strong> après la publication des résultats pour soumettre une requête.
        </div>
        <span class="font-mono text-xs font-bold bg-amber-100 px-2 py-1 rounded" id="countdown-display">--h --m --s</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Soumission d'une requête académique</h1>
        <p class="text-sm text-slate-500 mt-1">Remplissez le formulaire ci-dessous pour signaler une erreur ou demander une révision.</p>
    </div>

    {{-- Stepper --}}
    <div class="mb-8"
         x-data="{
            step: 1,
            filieres: [],
            cours: [],
            loadingFilieres: false,
            loadingCours: false,
            selectedCycle: '',
            selectedFiliere: '',
            selectedNiveau: '',
            selectedCours: null,
            selectedSession: '',

            async fetchFilieres() {
                if (!this.selectedCycle) return;
                this.loadingFilieres = true;
                const r = await fetch('/etudiant/api/filieres?cycle=' + this.selectedCycle);
                this.filieres = await r.json();
                this.loadingFilieres = false;
                this.selectedFiliere = '';
                this.cours = [];
                this.selectedCours = null;
            },

            async fetchCours() {
                if (!this.selectedFiliere || !this.selectedNiveau) return;
                this.loadingCours = true;
                const r = await fetch('/etudiant/api/cours?filiere_id=' + this.selectedFiliere + '&niveau=' + this.selectedNiveau);
                this.cours = await r.json();
                this.loadingCours = false;
                this.selectedCours = null;
            },

            nextStep() {
                if (this.step === 1 && (!this.selectedCours || !this.selectedSession)) return;
                this.step++;
            },

            prevStep() { if (this.step > 1) this.step--; }
         }">

        {{-- Progress bar --}}
        <div class="flex items-center gap-0 mb-8">
            <template x-for="i in 3" :key="i">
                <div class="flex items-center flex-1">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                             :class="step >= i ? 'text-white' : 'bg-surface-container text-slate-400'"
                             :style="step >= i ? 'background: #002444' : ''">
                            <template x-if="step > i">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="step <= i">
                                <span x-text="i"></span>
                            </template>
                        </div>
                        <span class="text-xs mt-1.5 font-medium" :class="step >= i ? 'text-primary' : 'text-slate-400'">
                            <span x-text="['Académique','Détails','Justificatifs'][i-1]"></span>
                        </span>
                    </div>
                    <div x-show="i < 3" class="flex-1 h-0.5 mx-2 mb-4 transition-all"
                         :class="step > i ? 'bg-primary' : 'bg-surface-container'"></div>
                </div>
            </template>
        </div>

        <form id="requete-form" method="POST" action="{{ route('etudiant.requetes.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- STEP 1: Académique --}}
            <div x-show="step === 1" class="card space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Cycle d'étude</label>
                        <select class="form-select" x-model="selectedCycle" @change="fetchFilieres()">
                            <option value="">-- Sélectionner --</option>
                            <option value="BTS">BTS</option>
                            <option value="HND">HND</option>
                            <option value="LP">Licence Professionnelle</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Filière / Département</label>
                        <select class="form-select" x-model="selectedFiliere"
                                @change="fetchCours()"
                                :disabled="!selectedCycle || loadingFilieres">
                            <option value="">-- Sélectionner --</option>
                            <template x-for="f in filieres" :key="f.id">
                                <option :value="f.id" x-text="f.nom_filiere"></option>
                            </template>
                        </select>
                        <div x-show="loadingFilieres" class="mt-1 text-xs text-slate-500">Chargement...</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Niveau</label>
                        <select class="form-select" x-model="selectedNiveau" @change="fetchCours()">
                            <option value="">-- Sélectionner --</option>
                            <option value="1">Niveau 1</option>
                            <option value="2">Niveau 2</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Session d'examen</label>
                        <select name="session_id" class="form-select" x-model="selectedSession"
                                id="session-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}"
                                        data-pub="{{ $s->date_publication->toISOString() }}">
                                    {{ $s->libelle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Cours concerné</label>
                    <div x-show="loadingCours" class="text-xs text-slate-500 mb-2">Chargement des cours...</div>
                    <div x-show="!loadingCours && cours.length === 0 && selectedFiliere && selectedNiveau"
                         class="text-xs text-slate-500 mb-2">Aucun cours disponible.</div>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="c in cours" :key="c.id">
                            <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                   :class="selectedCours == c.id ? 'border-primary bg-primary/5' : 'border-surface-container hover:border-slate-300'">
                                <input type="radio" name="cours_id" :value="c.id"
                                       x-model="selectedCours" class="sr-only">
                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                                     :class="selectedCours == c.id ? 'border-primary bg-primary' : 'border-slate-300'">
                                    <div x-show="selectedCours == c.id" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                </div>
                                <span class="text-sm text-slate-700" x-text="c.nom_cours"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <a href="{{ route('etudiant.dashboard') }}" class="btn-secondary">Annuler</a>
                    <button type="button" @click="nextStep()"
                            :disabled="!selectedCours || !selectedSession"
                            class="btn-primary disabled:opacity-40 disabled:cursor-not-allowed">
                        Continuer
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- STEP 2: Détails --}}
            <div x-show="step === 2" class="card space-y-5">
                <div>
                    <label class="form-label">Type d'anomalie</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(App\Enums\TypeAnomalie::cases() as $type)
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all border-surface-container hover:border-slate-300 has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            <input type="radio" name="type_anomalie" value="{{ $type->value }}"
                                   class="sr-only" required>
                            <div class="w-4 h-4 rounded-full border-2 border-slate-300 flex-shrink-0"></div>
                            <span class="text-sm text-slate-700">{{ $type->label() }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="form-label">Description (optionnel)</label>
                    <textarea name="description" rows="4" maxlength="500"
                              placeholder="Décrivez le problème que vous avez constaté..."
                              class="form-input resize-none"></textarea>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="prevStep()" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Précédent
                    </button>
                    <button type="button" @click="nextStep()" class="btn-primary">
                        Continuer
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- STEP 3: Justificatifs --}}
            <div x-show="step === 3" class="card space-y-5"
                 x-data="{
                    files: [],
                    dragging: false,
                    handleDrop(e) {
                        this.dragging = false;
                        this.addFiles(e.dataTransfer.files);
                    },
                    handleSelect(e) { this.addFiles(e.target.files); },
                    addFiles(fileList) {
                        for (let f of fileList) {
                            if (f.size > 10 * 1024 * 1024) { alert(f.name + ' dépasse 10 Mo.'); continue; }
                            if (!['application/pdf','image/jpeg','image/png','image/jpg'].includes(f.type)) {
                                alert(f.name + ' : type non supporté (PDF, JPG, PNG seulement).'); continue;
                            }
                            const reader = new FileReader();
                            reader.onload = (ev) => {
                                this.files.push({ name: f.name, size: f.size, type: f.type, url: ev.target.result, file: f });
                            };
                            reader.readAsDataURL(f);
                        }
                    },
                    remove(i) { this.files.splice(i, 1); },
                    formatSize(s) { return s < 1048576 ? Math.round(s/1024) + ' Ko' : (s/1048576).toFixed(1) + ' Mo'; }
                 }">

                <div>
                    <label class="form-label">Pièces justificatives <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-surface-container rounded-2xl p-8 text-center cursor-pointer transition-all"
                         :class="dragging ? 'border-primary bg-primary/5' : 'hover:border-slate-300'"
                         @dragover.prevent="dragging = true"
                         @dragleave="dragging = false"
                         @drop.prevent="handleDrop($event)"
                         @click="$refs.fileInput.click()">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-slate-600 font-medium">Glissez vos fichiers ici ou <span class="text-primary font-semibold">parcourir</span></p>
                        <p class="text-xs text-slate-400 mt-1">PDF, JPG, PNG — Max 10 Mo par fichier</p>
                        <input type="file" x-ref="fileInput" class="sr-only"
                               name="pieces_jointes[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                               @change="handleSelect($event)">
                    </div>

                    <div class="mt-3 space-y-2">
                        <template x-for="(f, i) in files" :key="i">
                            <div class="flex items-center gap-3 p-3 bg-surface rounded-xl">
                                <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center"
                                     :class="f.type === 'application/pdf' ? 'bg-red-50' : 'bg-blue-50'">
                                    <svg class="w-4 h-4" :class="f.type === 'application/pdf' ? 'text-red-500' : 'text-blue-500'"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-700 truncate" x-text="f.name"></p>
                                    <p class="text-xs text-slate-400" x-text="formatSize(f.size)"></p>
                                </div>
                                <button type="button" @click="remove(i)"
                                        class="text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-between pt-2">
                    <button type="button" @click="prevStep()" class="btn-secondary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Précédent
                    </button>
                    <button type="submit"
                            :disabled="files.length === 0"
                            class="btn-success disabled:opacity-40 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Soumettre la requête
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const display   = document.getElementById('countdown-display');
    const select    = document.getElementById('session-select');
    const submitBtn = document.querySelector('button[type="submit"]');
    if (!display || !select) return;

    let intervalId = null;

    function startCountdown(pubIso) {
        if (intervalId) clearInterval(intervalId);
        if (!pubIso) { display.textContent = '--h --m --s'; return; }

        const limite = new Date(new Date(pubIso).getTime() + 72 * 3600 * 1000);

        function tick() {
            const diff = limite - new Date();
            if (diff <= 0) {
                display.textContent = 'Délai dépassé';
                display.classList.remove('bg-amber-100');
                display.classList.add('bg-red-100', 'text-red-600');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.title = 'Le délai de soumission est dépassé pour cette session.';
                }
                clearInterval(intervalId);
                return;
            }
            display.classList.remove('bg-red-100', 'text-red-600');
            display.classList.add('bg-amber-100');
            if (submitBtn) submitBtn.disabled = false;
            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            display.textContent = `${h}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
        }
        tick();
        intervalId = setInterval(tick, 1000);
    }

    select.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        startCountdown(opt ? opt.dataset.pub : null);
    });

    // Au chargement : démarrer avec la première option ayant une date de publication
    const firstWithPub = Array.from(select.options).find(o => o.dataset.pub);
    if (firstWithPub) startCountdown(firstWithPub.dataset.pub);
});
</script>
@endpush
