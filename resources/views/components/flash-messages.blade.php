@php
    $flashes = collect([
        ['type' => 'success', 'msg' => session('success')],
        ['type' => 'error',   'msg' => session('error')],
        ['type' => 'warning', 'msg' => session('warning')],
        ['type' => 'info',    'msg' => session('info')],
    ])->filter(fn ($f) => filled($f['msg']));
@endphp

@foreach($flashes as $flash)
<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    x-init="setTimeout(() => show = false, 5000)"
    class="fixed right-4 z-[100] max-w-md"
    style="top: {{ ($loop->index * 70) + 16 }}px"
>
    <div class="flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg
        @if($flash['type'] === 'success') border-emerald-500/40 bg-emerald-950 text-emerald-50
        @elseif($flash['type'] === 'error') border-red-500/40 bg-red-950 text-red-50
        @elseif($flash['type'] === 'warning') border-amber-500/40 bg-amber-950 text-amber-50
        @else border-[#0A7FA8]/40 bg-slate-900 text-slate-50
        @endif">
        <p class="flex-1 text-sm font-medium">{{ $flash['msg'] }}</p>
        <button type="button" class="opacity-70 hover:opacity-100"
                @click="show = false" aria-label="Dismiss">&times;</button>
    </div>
</div>
@endforeach

@if($errors->any())
<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    x-init="setTimeout(() => show = false, 8000)"
    class="fixed right-4 z-[100] max-w-md"
    style="top: {{ ($flashes->count() * 70) + 16 }}px"
>
    <div class="flex items-start gap-3 rounded-xl border border-red-500/40 bg-red-950 px-4 py-3 shadow-lg text-red-50">
        <div class="flex-1">
            <p class="text-sm font-semibold mb-1">{{ __('app.validation_errors') }}</p>
            <ul class="text-sm space-y-0.5 opacity-90">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="opacity-70 hover:opacity-100 shrink-0"
                @click="show = false" aria-label="Dismiss">&times;</button>
    </div>
</div>
@endif