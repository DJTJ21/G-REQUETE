@extends('layouts.app')
@section('title', 'Créer un compte')

@section('content')
<div class="pt-4 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.utilisateurs.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Créer un compte</h1>
    </div>

    <form method="POST" action="{{ route('admin.utilisateurs.store') }}" id="create-user-form">
        @csrf

        <div class="card space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom') }}" class="form-input" required>
                    @error('nom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" class="form-input" required>
                    @error('prenom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Rôle <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['etudiant' => ['Étudiant', 'text-blue-600', 'bg-blue-50'], 'agent' => ['Agent', 'text-green-600', 'bg-green-50'], 'admin' => ['Administrateur', 'text-red-600', 'bg-red-50']] as $val => [$lib, $tc, $bg])
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all border-surface-container hover:border-slate-300 has-[:checked]:border-current">
                        <input type="radio" name="role" value="{{ $val }}" class="sr-only role-radio" {{ old('role', 'etudiant') === $val ? 'checked' : '' }}>
                        <div class="w-8 h-8 rounded-lg {{ $bg }} flex items-center justify-center flex-shrink-0">
                            <span class="{{ $tc }} font-bold text-xs">{{ strtoupper(substr($val, 0, 1)) }}</span>
                        </div>
                        <span class="text-sm font-medium text-slate-700">{{ $lib }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Étudiant fields --}}
            <div id="section-etudiant" class="space-y-4" style="display:none">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Matricule <span class="text-red-500">*</span></label>
                        <input type="text" name="matricule" value="{{ old('matricule') }}" class="form-input font-mono"
                               placeholder="Ex: 22G00123">
                        @error('matricule')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Niveau <span class="text-red-500">*</span></label>
                        <select name="niveau" class="form-select">
                            <option value="">-- Sélectionner --</option>
                            <option value="1" {{ old('niveau') == 1 ? 'selected' : '' }}>Niveau 1</option>
                            <option value="2" {{ old('niveau') == 2 ? 'selected' : '' }}>Niveau 2</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Filière <span class="text-red-500">*</span></label>
                    <select name="filiere_id" class="form-select">
                        <option value="">-- Sélectionner --</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}" {{ old('filiere_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->nom_filiere }} ({{ $f->cycle->value }})
                            </option>
                        @endforeach
                    </select>
                    @error('filiere_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Agent fields --}}
            <div id="section-agent" class="space-y-4" style="display:none">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Matricule agent <span class="text-red-500">*</span></label>
                        <input type="text" name="matricule_agent" value="{{ old('matricule_agent') }}" class="form-input font-mono"
                               placeholder="Ex: AGT003">
                        @error('matricule_agent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Service</label>
                        <input type="text" name="service" value="{{ old('service', 'Scolarité') }}" class="form-input">
                    </div>
                </div>
            </div>

            <div class="pt-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Un mot de passe temporaire sera généré automatiquement et affiché après la création.
            </div>

            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.utilisateurs.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Créer le compte
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    const sections = { etudiant: 'section-etudiant', agent: 'section-agent' };

    function showSection(role) {
        Object.values(sections).forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = 'none';
        });
        if (sections[role]) {
            const el = document.getElementById(sections[role]);
            if (el) el.style.display = '';
        }
    }

    function init() {
        const radios = document.querySelectorAll('.role-radio');
        radios.forEach(r => r.addEventListener('change', () => showSection(r.value)));

        const checked = document.querySelector('.role-radio:checked');
        showSection(checked ? checked.value : 'etudiant');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endpush
@endsection
