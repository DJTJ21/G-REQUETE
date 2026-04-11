@props(['icon' => null, 'value' => 0, 'label' => '', 'color' => 'blue', 'variation' => null])

@php
$colors = [
    'blue'   => 'bg-blue-50 text-blue-600',
    'green'  => 'bg-green-50 text-green-600',
    'amber'  => 'bg-amber-50 text-amber-600',
    'red'    => 'bg-red-50 text-red-600',
    'purple' => 'bg-purple-50 text-purple-600',
];
$iconBg = $colors[$color] ?? $colors['blue'];
@endphp

<div class="card flex flex-col gap-3">
    <div class="flex items-center justify-between">
        @if($icon)
            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $iconBg }}">
                {!! $icon !!}
            </div>
        @endif
        @if($variation)
            <span class="text-xs font-semibold {{ str_starts_with($variation, '+') ? 'text-green-600' : 'text-red-500' }}">
                {{ $variation }}
            </span>
        @endif
    </div>
    <div>
        <div class="text-3xl font-bold text-slate-800" style="font-size: 2rem; line-height: 1">
            {{ is_numeric($value) ? number_format($value) : $value }}
        </div>
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-1">{{ $label }}</div>
    </div>
</div>
