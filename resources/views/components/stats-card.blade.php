@props(['label', 'value'])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-white/10 bg-[#1a1a2e] p-5 shadow-sm transition hover:shadow-md hover:border-[#29ABE2]/30']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-400">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $value }}</p>
        </div>
        @isset($icon)
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#29ABE2] to-[#0A7FA8] text-white shadow-inner">
                {{ $icon }}
            </div>
        @endisset
    </div>
</div>