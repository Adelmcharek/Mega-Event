@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'animate-pulse overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm '.$class]) }}>
    <div class="h-40 bg-slate-200"></div>
    <div class="space-y-3 p-5">
        <div class="h-4 w-3/4 rounded bg-slate-200"></div>
        <div class="h-3 w-1/2 rounded bg-slate-200"></div>
        <div class="h-2 w-full rounded bg-slate-200"></div>
        <div class="h-9 w-full rounded-xl bg-slate-200"></div>
    </div>
</div>
