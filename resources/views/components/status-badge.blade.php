@props(['status'])

@php
    $key = strtoupper((string) $status);
    $map = [
        'DRAFT' => 'bg-slate-600 text-white',
        'PENDING' => 'bg-amber-500 text-slate-900',
        'PUBLISHED' => 'bg-emerald-600 text-white',
        'REJECTED' => 'bg-red-600 text-white',
        'OPEN' => 'bg-emerald-500/20 text-emerald-700 ring-1 ring-emerald-500/40',
        'FULL' => 'bg-red-500/20 text-red-700 ring-1 ring-red-500/40',
        'COMING SOON' => 'bg-blue-500/20 text-blue-700 ring-1 ring-blue-500/40',
        'CONFIRMED' => 'bg-emerald-600 text-white',
        'ADMIN' => 'bg-red-600 text-white',
        'ORGANISATEUR' => 'bg-brand-500 text-white',
        'ORGANIZER' => 'bg-brand-500 text-white',
        'PARTICIPANT' => 'bg-blue-600 text-white',
        'ACTIVE' => 'bg-emerald-600 text-white',
        'INACTIVE' => 'bg-slate-500 text-white',
    ];
    $class = $map[$key] ?? 'bg-slate-500/20 text-slate-700 ring-1 ring-slate-300';
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide '.$class]) }}>
    {{ $status }}
</span>
