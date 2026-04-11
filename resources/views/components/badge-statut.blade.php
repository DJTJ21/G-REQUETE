@props(['statut'])

@php
use App\Enums\StatutRequete;
$s = $statut instanceof StatutRequete ? $statut : StatutRequete::from($statut);
$class = $s->badgeClass();
$label = $s->label();
@endphp

<span class="{{ $class }}">{{ $label }}</span>
