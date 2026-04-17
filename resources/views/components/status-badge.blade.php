@props(['status'])

@php
    $key = strtoupper((string) $status);
    $map = [
        'DRAFT' => 'bg-gray-500/20 text-gray-400 border border-gray-500/30',
        'PENDING' => 'bg-[#29ABE2]/20 text-[#29ABE2] border border-[#29ABE2]/30',
        'PUBLISHED' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
        'REJECTED' => 'bg-red-500/20 text-red-400 border border-red-500/30',
        'OPEN' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
        'FULL' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
        'COMING SOON' => 'bg-[#0A7FA8]/20 text-[#0A7FA8] border border-[#0A7FA8]/30',
        'CONFIRMED' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
        'ADMIN' => 'bg-red-500/20 text-red-400 border border-red-500/30',
        'ORGANISATEUR' => 'bg-[#29ABE2]/20 text-[#29ABE2] border border-[#29ABE2]/30',
        'ORGANIZER' => 'bg-[#29ABE2]/20 text-[#29ABE2] border border-[#29ABE2]/30',
        'PARTICIPANT' => 'bg-[#0A7FA8]/20 text-[#0A7FA8] border border-[#0A7FA8]/30',
        'ACTIVE' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
        'INACTIVE' => 'bg-gray-500/20 text-gray-400 border border-gray-500/30',
    ];
    $class = $map[$key] ?? 'bg-gray-500/20 text-gray-400 border border-gray-500/30';
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-medium uppercase tracking-wide '.$class]) }}>
    {{ $status }}
</span>
