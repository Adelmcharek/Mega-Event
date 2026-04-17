@props([
    'current' => 0,
    'max' => 1,
    'showLabel' => true,
])

@php
    $max = max(1, (int) $max);
    $current = min((int) $current, $max);
    $pct = (int) round(($current / $max) * 100);
    $barClass = match (true) {
        $pct >= 100 => 'bg-red-500',
        $pct >= 80 => 'bg-orange-500',
        default => 'bg-emerald-500',
    };
@endphp
<div {{ $attributes->merge(['class' => 'w-full']) }}>
    @if ($showLabel)
        <div class="mb-1 flex justify-between text-xs font-medium text-slate-600">
            <span>{{ $current }} / {{ $max }} spots filled</span>
            <span>{{ $pct }}%</span>
        </div>
    @endif
    <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-200">
        <div class="h-full rounded-full transition-all duration-500 {{ $barClass }}" style="width: {{ min(100, $pct) }}%"></div>
    </div>
</div>
