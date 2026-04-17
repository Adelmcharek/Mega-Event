@props(['href', 'active' => false])

@php
    $base  = 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200';
    $state = $active
        ? 'bg-[#29ABE2]/15 text-white border-l-2 border-[#29ABE2] rounded-l-none shadow-sm'
        : 'text-slate-400 hover:bg-white/5 hover:text-white';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $base . ' ' . $state]) }}>
    @isset($icon)
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-white/90
                     {{ $active ? 'bg-[#29ABE2]/20' : 'bg-white/5' }}" aria-hidden="true">
            {{ $icon }}
        </span>
    @endisset
    <span class="truncate">{{ $slot }}</span>
</a>