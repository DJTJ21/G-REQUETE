@extends('layouts.app')
@section('title', 'Modifier ' . $utilisateur->nom_complet)

@section('content')
<div class="pt-4 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.utilisateurs.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Modifier le compte</h1>
    </div>

    <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur->id) }}">
        @csrf @method('PUT')

        <div class="card space-y-5">
            <div class="flex items-center gap-4 pb-4 border-b border-surface-container">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl font-bold"
                     style="background: linear-gradient(135deg, #002444, #1a3a5c)">
                    {{ strtoupper(substr($utilisateur->prenom, 0, 1) . substr($utilisateur->nom, 0, 1)) }}
                </div>
                <div>
                    <p class="text-base font-bold text-slate-800">{{ $utilisateur->nom_complet }}</p>
                    <span class="badge {{ ['etudiant'=>'badge-blue','agent'=>'badge-green','admin'=>'badge-red'][$utilisateur->role->value] ?? 'badge-gray' }}">
                        {{ $utilisateur->role->label() }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nom <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" value="{{ old('nom', $utilisateur->nom) }}" class="form-input" required>
                    @error('nom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Prénom <span class="text-red-500">*</span></label>
                    <input type="text" name="prenom" value="{{ old('prenom', $utilisateur->prenom) }}" class="form-input" required>
                    @error('prenom')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $utilisateur->email) }}" class="form-input" required>
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            @if($utilisateur->isEtudiant() && $utilisateur->etudiant)
            <div class="border-t border-surface-container pt-4">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Informations académiques</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Matricule</label>
                        <input type="text" value="{{ $utilisateur->etudiant->matricule }}" class="form-input opacity-60" readonly>
                    </div>
                    <div>
                        <label class="form-label">Filière</label>
                        <input type="text" value="{{ $utilisateur->etudiant->filiere->nom_filiere }}" class="form-input opacity-60" readonly>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.utilisateurs.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Sauvegarder
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
