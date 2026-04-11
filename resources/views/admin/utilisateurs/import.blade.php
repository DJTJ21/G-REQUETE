@extends('layouts.app')
@section('title', 'Import CSV étudiants')

@section('content')
<div class="pt-4 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.utilisateurs.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-surface-container hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Import CSV — Étudiants</h1>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
            <p class="font-semibold mb-1">Erreur(s) :</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.utilisateurs.import.store') }}"
          enctype="multipart/form-data">
        @csrf
        <div class="card space-y-6">

            {{-- Zone de dépôt fichier --}}
            <div>
                <label class="form-label">Fichier CSV <span class="text-red-500">*</span></label>
                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed
                              border-slate-300 rounded-xl cursor-pointer hover:border-primary hover:bg-blue-50/40
                              transition-all group">
                    <svg class="w-8 h-8 text-slate-400 group-hover:text-primary mb-2 transition-colors"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span class="text-sm text-slate-500 group-hover:text-primary transition-colors">
                        Glisser-déposer ou <span class="font-semibold underline">parcourir</span>
                    </span>
                    <span class="text-xs text-slate-400 mt-1">Format CSV, séparateur point-virgule (;), max 2 Mo</span>
                    <input type="file" name="fichier_csv" accept=".csv,.txt" class="sr-only" required>
                </label>
                @error('fichier_csv')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Format attendu --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm">
                <p class="font-semibold text-slate-700 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Format CSV attendu (1ère ligne = en-têtes)
                </p>
                <div class="overflow-x-auto">
                    <table class="text-xs w-full">
                        <thead>
                            <tr class="bg-slate-200 text-slate-600">
                                @foreach(['nom','prenom','email','matricule','filiere_id','niveau'] as $col)
                                    <th class="px-3 py-1.5 font-mono font-semibold text-left">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-white text-slate-500 border-t border-slate-200">
                                <td class="px-3 py-1.5 font-mono">Kamdem</td>
                                <td class="px-3 py-1.5 font-mono">Arthur</td>
                                <td class="px-3 py-1.5 font-mono">a.kamdem@esg.cm</td>
                                <td class="px-3 py-1.5 font-mono">23G00100</td>
                                <td class="px-3 py-1.5 font-mono">1</td>
                                <td class="px-3 py-1.5 font-mono">1</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-slate-500">
                    <span class="font-semibold">filiere_id</span> — IDs disponibles :
                    @foreach($filieres as $f)
                        <span class="inline-flex items-center gap-1 bg-white border border-slate-200 rounded px-1.5 py-0.5 font-mono text-slate-700 mr-1">
                            {{ $f->id }} = {{ $f->nom_filiere }}
                        </span>
                    @endforeach
                </p>
            </div>

            {{-- Lien modèle CSV --}}
            <div class="flex items-center gap-2 text-sm text-primary hover:underline cursor-pointer"
                 onclick="downloadTemplate()">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Télécharger le modèle CSV vide
            </div>

            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.utilisateurs.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Importer les étudiants
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function downloadTemplate() {
    const header = 'nom;prenom;email;matricule;filiere_id;niveau\n';
    const example = 'Dupont;Jean;jean.dupont@esg.cm;23G00100;1;1\n';
    const blob = new Blob(['\uFEFF' + header + example], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = 'modele_import_etudiants.csv'; a.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection
