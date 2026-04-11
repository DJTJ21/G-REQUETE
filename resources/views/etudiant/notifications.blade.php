@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="pt-4 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Notifications</h1>
            <p class="text-sm text-slate-500 mt-1">Toutes vos mises à jour récentes.</p>
        </div>
        @if($notificationsCount > 0)
        <form method="POST" action="{{ route('etudiant.notifications.tout-lue') }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn-secondary text-xs">Tout marquer comme lu</button>
        </form>
        @endif
    </div>

    <div class="card space-y-0 overflow-hidden p-0">
        @forelse($notifications as $notif)
        <div class="flex items-start gap-4 px-5 py-4 border-b border-surface-container last:border-0
                    {{ !$notif->est_lue ? 'bg-primary/[.03]' : '' }} transition-colors hover:bg-surface">
            <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center
                        {{ !$notif->est_lue ? 'bg-primary/10' : 'bg-surface-container' }}">
                <svg class="w-4 h-4 {{ !$notif->est_lue ? 'text-primary' : 'text-slate-400' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm {{ !$notif->est_lue ? 'font-semibold text-slate-800' : 'text-slate-700' }}">
                    {{ $notif->message }}
                </p>
                <div class="flex items-center gap-3 mt-1">
                    <p class="text-xs text-slate-400">{{ $notif->date_envoi->diffForHumans() }}</p>
                    @if($notif->requete)
                    <a href="{{ route('etudiant.requetes.show', $notif->requete_id) }}"
                       class="text-xs text-primary font-semibold hover:underline">
                        Voir la requête →
                    </a>
                    @endif
                </div>
            </div>
            @if(!$notif->est_lue)
            <form method="POST" action="{{ route('etudiant.notifications.lue', $notif->id) }}">
                @csrf @method('PATCH')
                <button type="submit" class="text-slate-300 hover:text-primary transition-colors mt-0.5" title="Marquer comme lue">
                    <div class="w-2 h-2 rounded-full bg-primary"></div>
                </button>
            </form>
            @endif
        </div>
        @empty
        <x-empty-state title="Aucune notification" description="Vous n'avez pas encore reçu de notifications." />
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
</div>
@endsection
