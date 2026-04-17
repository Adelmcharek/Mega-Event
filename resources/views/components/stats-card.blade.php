@props(['label', 'value'])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $value }}</p>
        </div>
        @isset($icon)
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-purple-600 to-blue-500 text-white shadow-inner">
                {{ $icon }}
            </div>
        @endisset
    </div>
</div>
