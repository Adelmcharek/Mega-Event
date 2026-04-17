@props([
    'title' => 'Nothing here yet',
    'message' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/80 px-8 py-16 text-center']) }}>
    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500/20 to-[#0A7FA8]/20 text-brand-500">
        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" />
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
    @if ($message)
        <p class="mt-2 max-w-sm text-sm text-slate-600">{{ $message }}</p>
    @endif
    @isset($action)
        <div class="mt-6">{{ $action }}</div>
    @endisset
</div>
