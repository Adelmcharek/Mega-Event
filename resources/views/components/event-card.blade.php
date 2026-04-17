@props([
    'event',
    'variant' => 'default',
    'showRegister' => false,
])

@php
    $id = data_get($event, 'id', 0);
    $title = data_get($event, 'title', 'Event');
    $date = data_get($event, 'date');
    $location = data_get($event, 'location', '—');
    $category = data_get($event, 'category', 'Professional');
    $capacity = (int) data_get($event, 'capacity', 100);
    $registered = (int) data_get($event, 'registered', data_get($event, 'registered_count', 0));
    $status = strtoupper((string) data_get($event, 'status', 'OPEN'));
    $cover = data_get($event, 'cover_url');
    $href = data_get($event, 'url', route('events.show', $id));
    $isFull = $registered >= $capacity && $capacity > 0;
@endphp
<div {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg']) }}>
    <div class="relative h-40 overflow-hidden bg-gradient-to-br from-brand-500 to-blue-500">
        @if ($cover)
            <img src="{{ $cover }}" alt="" class="h-full w-full object-cover opacity-90 transition group-hover:scale-105 group-hover:opacity-100">
        @else
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.08\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-80"></div>
        @endif
        <span class="absolute left-3 top-3 rounded-lg bg-black/40 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur-sm">{{ $category }}</span>
        @if ($date)
            <span class="absolute bottom-3 left-3 rounded-lg bg-white/95 px-2.5 py-1 text-xs font-bold text-slate-800 shadow">
                {{ \Illuminate\Support\Carbon::parse($date)->format('D, M d Y') }}
            </span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-bold text-slate-900 group-hover:text-brand-500">{{ $title }}</h3>
        <p class="mt-2 flex items-center gap-2 text-sm text-slate-600">
            <span aria-hidden="true">📍</span> {{ $location }}
        </p>
        <div class="mt-4">
            <x-capacity-bar :current="$registered" :max="$capacity" />
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <x-status-badge :status="$status" />
        </div>
        <div class="mt-auto flex gap-2 pt-5">
            <a href="{{ $href }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-brand-500 hover:text-brand-500">
                View Details
            </a>
            @if ($showRegister)
                @if ($isFull || $status === 'FULL')
                    <button type="button" disabled class="btn-gradient flex-1 cursor-not-allowed opacity-50">Register Now</button>
                @elseif ($status === 'COMING SOON')
                    <button type="button" disabled class="inline-flex flex-1 cursor-not-allowed items-center justify-center rounded-xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-500">Coming Soon</button>
                @else
                    <a href="{{ $href }}" class="btn-gradient flex-1 text-center text-sm">Register Now</a>
                @endif
            @endif
        </div>
    </div>
</div>
