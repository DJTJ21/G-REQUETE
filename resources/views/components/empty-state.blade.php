@props(['title' => 'Aucune donnée', 'description' => '', 'actionLabel' => null, 'actionRoute' => null])

<div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
        </svg>
    </div>
    <h3 class="text-base font-semibold text-slate-700 mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-slate-500 max-w-xs">{{ $description }}</p>
    @endif
    @if($actionLabel && $actionRoute)
        <a href="{{ $actionRoute }}" class="btn-primary mt-5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ $actionLabel }}
        </a>
    @endif
</div>
