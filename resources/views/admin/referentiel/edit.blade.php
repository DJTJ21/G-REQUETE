@extends('layouts.app')
@section('title', 'Modifier un élément')

@section('content')
<div class="pt-4 max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.filieres.index') }}" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Modifier un élément de référentiel</h1>
    </div>

    <div class="card">
        <p class="text-sm text-slate-500">Sélectionnez la filière ou la session à modifier depuis la liste des référentiels.</p>
        <div class="mt-4">
            <a href="{{ route('admin.filieres.index') }}" class="btn-secondary">Retour à la liste</a>
        </div>
    </div>
</div>
@endsection
