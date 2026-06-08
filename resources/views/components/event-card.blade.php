@props([
    'event',
    'variant' => 'default',
])

@php
    $id         = data_get($event, 'id', 0);
    $title      = data_get($event, 'title', 'Event');
    $date       = data_get($event, 'date');
    $location   = data_get($event, 'location', '—');
    $category   = data_get($event, 'category', 'Professional');
    $capacity   = (int) data_get($event, 'capacity', 100);
    $registered = (int) data_get($event, 'registered',
        data_get($event, 'registered_count',
            data_get($event, 'registrations_count', 0)));
    $status     = strtoupper((string) data_get($event, 'status', 'OPEN'));
    $cover      = data_get($event, 'cover_url');
    $href       = data_get($event, 'url', route('events.show', $id));
    $isFull     = $registered >= $capacity && $capacity > 0;
    $percentage = $capacity > 0 ? round(($registered / $capacity) * 100) : 0;
    $isPast     = $date && \Illuminate\Support\Carbon::parse($date)->endOfDay()->isPast();

    $wrapperClass = 'event-card group flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#29ABE2]/40 hover:shadow-xl hover:shadow-[#29ABE2]/10' . ($isPast ? ' opacity-50' : '');
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}
     style="background-color: #1a1a2e;">

    {{-- Banner --}}
    <div class="relative h-40 overflow-hidden">
        @if ($cover)
            <img src="{{ $cover }}" alt="" class="h-full w-full object-cover opacity-90 transition group-hover:scale-105">
        @else
            <div class="w-full h-full event-placeholder"
                 data-category="{{ strtolower($category) }}"
                 style="background-color: #1a1a2e;">
            </div>
        @endif
        {{-- Category badge --}}
        <span class="category-badge absolute left-3 top-3 rounded-full bg-black/40 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm">
            {{ $category }}
        </span>
        {{-- Past event badge --}}
        @if ($isPast)
            <span class="absolute right-3 top-3 rounded-full bg-gray-700/90 px-3 py-1 text-xs font-bold text-gray-200 backdrop-blur-sm">
                {{ __('app.event_past') }}
            </span>
        @endif
        {{-- Date badge --}}
        @if ($date)
            <span class="date-badge absolute bottom-3 left-3 rounded-lg bg-black/50 px-2.5 py-1 text-xs font-bold text-white backdrop-blur-sm">
                {{ \Illuminate\Support\Carbon::parse($date)->format('D, M d Y') }}
            </span>
        @endif
    </div>

    {{-- Body --}}
    <div class="flex flex-1 flex-col p-5">
        <a href="{{ $href }}" class="text-lg font-bold text-white transition-colors group-hover:text-[#29ABE2] line-clamp-2 hover:underline">
            {{ $title }}
        </a>
        <p class="mt-2 flex items-center gap-2 text-sm text-slate-400">
            <span>📍</span> {{ $location }}
        </p>

        {{-- Capacity bar --}}
        <div class="mt-4">
            <div class="flex justify-between text-xs text-slate-400 mb-1">
                <span>{{ $registered }} / {{ $capacity }} spots</span>
                <span>{{ $percentage }}%</span>
            </div>
            <div class="h-2 w-full rounded-full bg-white/10">
                <div class="h-2 rounded-full transition-all duration-500"
                     style="width: {{ $percentage }}%; background: {{ $percentage < 50 ? 'linear-gradient(to right, #10b981, #34d399)' : ($percentage < 85 ? 'linear-gradient(to right, #f59e0b, #fbbf24)' : 'linear-gradient(to right, #ef4444, #f87171)') }}">
                </div>
            </div>
        </div>

        {{-- Price --}}
        <div class="mt-4">
            @if(data_get($event, 'price', 0) > 0)
              <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-bold text-[#29ABE2]">
                  {{ number_format(data_get($event, 'price', 0), 2) }} EUR
                </span>
                <span class="text-xs text-slate-500">≈</span>
                <span class="text-sm font-bold text-amber-400">
                  {{ number_format(round(data_get($event, 'price', 0) * 3.3, 2), 2) }} TND
                </span>
              </div>
            @else
              <span class="text-sm font-bold text-emerald-400">
                🎟️ {{ __('app.event_free') }}
              </span>
            @endif
        </div>

        {{-- Status badge --}}
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <x-status-badge :status="$status" />
        </div>

        {{-- Action buttons --}}
        <div class="mt-auto pt-5">
            <a href="{{ route('events.show', $event) }}"
               class="block w-full rounded-xl py-2.5 text-sm font-bold text-white text-center
                      transition hover:scale-105"
               style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                {{ __('app.view_details') }}
            </a>
        </div>
    </div>
</div>
