@props(['href', 'active' => false])

@php
    $base = 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition';
    $state = $active
        ? 'bg-white/15 text-white shadow-sm ring-1 ring-white/10'
        : 'text-slate-300 hover:bg-white/10 hover:text-white';
@endphp
<a href="{{ $href }}" {{ $attributes->merge(['class' => $base.' '.$state]) }}>
    @isset($icon)
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 text-white/90" aria-hidden="true">
            {{ $icon }}
        </span>
    @endisset
    <span class="truncate">{{ $slot }}</span>
</a>
