@props([
    'event',
    'variant' => 'default',
    'showRegister' => false,
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

    $categoryGradient = match(strtolower($category)) {
        'cultural'    => 'from-blue-700 to-cyan-600',
        'corporate'   => 'from-teal-700 to-emerald-600',
        default       => 'from-[#29ABE2] to-[#0A7FA8]',
    };

    $wrapperClass = 'event-card group flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#29ABE2]/40 hover:shadow-xl hover:shadow-[#29ABE2]/10' . ($isPast ? ' opacity-50' : '');
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}
     style="background-color: #1a1a2e;">

    {{-- Banner --}}
    <div class="relative h-40 overflow-hidden bg-gradient-to-br {{ $categoryGradient }}">
        @if ($cover)
            <img src="{{ $cover }}" alt="" class="h-full w-full object-cover opacity-90 transition group-hover:scale-105">
        @else
            <div class="absolute inset-0 opacity-20"
                 style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.3\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/svg%3E')">
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
        <h3 class="text-lg font-bold text-white transition-colors group-hover:text-[#29ABE2] line-clamp-2">
            {{ $title }}
        </h3>
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

        {{-- Status badge --}}
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <x-status-badge :status="$status" />
        </div>

        {{-- Action buttons --}}
        <div class="mt-auto flex gap-2 pt-5">
            <a href="{{ $href }}"
               class="inline-flex flex-1 items-center justify-center rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-300 transition-all hover:border-[#29ABE2]/50 hover:text-[#29ABE2]">
                {{ __('app.view_details') }}
            </a>
            @if ($showRegister)
                @if ($isPast)
                    <button type="button" disabled
                            class="flex-1 cursor-not-allowed rounded-xl bg-white/5 px-4 py-2 text-sm font-semibold text-slate-500">
                        {{ __('app.event_past') }}
                    </button>
                @elseif ($isFull || $status === 'FULL')
                    <button type="button" disabled
                            class="flex-1 cursor-not-allowed rounded-xl bg-white/5 px-4 py-2 text-sm font-semibold text-slate-500">
                        {{ __('app.event_full') }}
                    </button>
                @elseif ($status === 'COMING SOON')
                    <button type="button" disabled
                            class="flex-1 cursor-not-allowed rounded-xl bg-white/5 px-4 py-2 text-sm font-semibold text-slate-500">
                        Coming Soon
                    </button>
                @else
                    <a href="{{ $href }}"
                       class="flex-1 text-center rounded-xl px-4 py-2 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
                       style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        {{ __('app.register_now') }}
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>
