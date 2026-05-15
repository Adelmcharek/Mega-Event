@extends('layouts.public')

@section('title', $event->title ?? 'Event')

@section('content')
    @php
        $e = is_object($event) ? $event : (object) $event;
        $date = \Illuminate\Support\Carbon::parse($e->date ?? now());
        $cap = (int) ($e->capacity ?? 0);
        $reg = (int) ($participantsCount ?? data_get($e, 'registrations_count', data_get($e, 'registered', 0)));
        $remaining = max(0, $cap - $reg);
        $price = $e->price ?? null;
        $percentage = $cap > 0 ? round(($reg / $cap) * 100) : 0;
    @endphp

    {{-- Hero Banner --}}
    <div class="relative h-64 w-full overflow-hidden md:h-80">
        @if($event->cover_image)
            <img src="{{ Storage::url($event->cover_image) }}"
                 class="w-full h-full object-cover"
                 alt="{{ $event->title }}">
        @else
            <div class="w-full h-full event-placeholder"
                 data-category="{{ $event->category }}"
                 style="background-color: #1a1a2e;">
            </div>
        @endif
        <div class="absolute inset-0" style="background: rgba(10,10,10,0.5);"></div>
        <div class="relative mx-auto flex h-full max-w-7xl flex-col justify-end px-4 pb-10 sm:px-6 lg:px-8">
            <span class="inline-flex w-fit rounded-lg bg-white/20 px-2 py-1 text-xs font-semibold text-white backdrop-blur">
                {{ $e->category ?? 'Event' }}
            </span>
            <h1 class="mt-3 text-3xl font-extrabold text-white drop-shadow md:text-4xl">
                {{ $e->title ?? 'Event' }}
            </h1>
            <p class="mt-2 flex flex-wrap gap-4 text-sm font-medium text-[#29ABE2]">
                <span>📅 {{ $date->format('D, M d Y — H:i') }}</span>
                <span class="flex items-center gap-2">
                    📍 {{ $e->location ?? 'TBA' }}
                    @if(!empty($e->location))
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($e->location) }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1 rounded-lg border border-[#29ABE2]/40 bg-[#29ABE2]/10 px-2 py-0.5 text-xs font-semibold text-[#29ABE2] transition-all hover:bg-[#29ABE2]/20">
                            📍 {{ __('app.view_on_map') }}
                        </a>
                    @endif
                </span>
            </p>
        </div>
    </div>

    {{-- Content --}}
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
         style="background-color: #0A0A0A;">

        <a href="{{ route('events.index') }}"
           class="text-sm font-semibold text-[#29ABE2] hover:text-[#4CBDE8] transition-colors">
            ← {{ __('app.back') }}
        </a>

        <div class="mt-8 grid gap-10 lg:grid-cols-10">

            {{-- LEFT: Main content --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- About --}}
                <div class="rounded-2xl border border-white/10 p-6 shadow-sm"
                     style="background-color: #1a1a2e;">
                    <h2 class="text-lg font-bold text-white">{{ __('app.about_event') }}</h2>
                    <div class="mt-4 text-slate-300 leading-relaxed">
                        {!! nl2br(e($e->description ?? 'Join us for an unforgettable experience. More details will be available soon.')) !!}
                    </div>
                    @if($e->lat && $e->lng)
                        <div id="show-map" style="height:250px;"
                             class="rounded-xl overflow-hidden border border-white/10 mt-4"></div>
                        <script>
                          document.addEventListener('DOMContentLoaded', function() {
                            const map = L.map('show-map').setView([{{ $e->lat }}, {{ $e->lng }}], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                            L.marker([{{ $e->lat }}, {{ $e->lng }}]).addTo(map)
                              .bindPopup('{{ addslashes($e->location) }}').openPopup();
                          });
                        </script>
                    @endif
                </div>

                {{-- Schedule --}}
                <div class="rounded-2xl border border-white/10 p-6 shadow-sm"
                     style="background-color: #1a1a2e;">
                    <h2 class="text-lg font-bold text-white">{{ __('app.event_details') }}</h2>
                    <ul class="mt-4 space-y-3 text-sm">
                        @forelse($agenda ?? [] as $item)
                            <li class="flex gap-3 rounded-xl px-4 py-3"
                                style="background-color: rgba(41,171,226,0.05); border: 1px solid rgba(41,171,226,0.15);">
                                <span class="font-semibold text-[#29ABE2] w-14 shrink-0">
                                    {{ $item->time ?? $item['time'] ?? '' }}
                                </span>
                                <span class="text-slate-300">{{ $item->label ?? $item['label'] ?? '' }}</span>
                            </li>
                        @empty
                            <li class="rounded-xl px-4 py-3 text-slate-400"
                                style="background-color: rgba(255,255,255,0.03);">
                                Opening remarks, keynotes, and networking — full timetable announced before the event.
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Partners --}}
                @if($event->partners->isNotEmpty())
                <div class="rounded-2xl border border-white/10 p-6 shadow-sm"
                     style="background-color: #1a1a2e;">
                    <h2 class="text-lg font-bold text-white">{{ __('app.event_partners') }}</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach($event->partners as $partner)
                            <div class="rounded-xl border border-white/10 p-4" style="background-color: #0f0f23;">
                                <p class="font-bold text-white">{{ $partner->name }}</p>
                                @if($partner->website)
                                    <a href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer"
                                       class="mt-1 flex items-center gap-1.5 text-sm text-[#29ABE2] hover:underline">
                                        🌐 {{ $partner->website }}
                                    </a>
                                @endif
                                @if($partner->email)
                                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-400">
                                        ✉️ {{ $partner->email }}
                                    </p>
                                @endif
                                @if($partner->phone)
                                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-400">
                                        📞 {{ $partner->phone }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Participants count --}}
                <div class="rounded-2xl border border-white/10 p-6"
                     style="background: linear-gradient(135deg, rgba(41,171,226,0.05), rgba(10,127,168,0.05));">
                    <h2 class="text-lg font-bold text-white">{{ __('app.participants') }}</h2>
                    <p class="mt-2 text-4xl font-extrabold text-[#29ABE2]">
                        {{ $participantsCount ?? $reg }}
                    </p>
                    <p class="text-sm text-slate-400">{{ __('app.registration_confirmed') }}</p>
                </div>
            </div>

            {{-- RIGHT: Registration card --}}
            <div class="lg:col-span-3">
                <div class="sticky top-24 rounded-2xl border border-[#29ABE2]/30 p-6 shadow-2xl shadow-[#29ABE2]/10"
                     style="background-color: #1a1a2e;">

                    <p class="text-sm font-medium text-slate-400">{{ __('app.event_price') }}</p>
                    @if($price !== null && $price !== '' && (float)$price > 0)
                        <span class="text-[#29ABE2] font-bold text-lg">{{ number_format((float)$price, 2) }} TND</span>
                    @else
                        <span class="text-emerald-400 font-bold text-lg">🎟️ {{ __('app.event_free') }}</span>
                    @endif

                    <div class="mt-4">
                        <p class="text-sm font-medium text-slate-400">{{ __('app.spots_left') }}</p>
                        <p class="text-xl font-bold text-[#29ABE2]">{{ $remaining }} / {{ $cap }}</p>
                    </div>

                    {{-- Capacity bar --}}
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-slate-400 mb-1">
                            <span>{{ $reg }} / {{ $cap }} {{ __('app.spots_left') }}</span>
                            <span>{{ $percentage }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-white/10">
                            <div class="h-2 rounded-full transition-all duration-500"
                                 style="width: {{ $percentage }}%; background: {{ $percentage < 50 ? 'linear-gradient(to right, #10b981, #34d399)' : ($percentage < 85 ? 'linear-gradient(to right, #f59e0b, #fbbf24)' : 'linear-gradient(to right, #ef4444, #f87171)') }}">
                            </div>
                        </div>
                    </div>

                    @if($remaining <= 10 && $remaining > 0)
                        <p class="mt-3 text-xs font-semibold text-amber-400">
                            🔥 Only {{ $remaining }} spots left!
                        </p>
                    @endif

                    @guest
                        <div class="mt-6 rounded-xl border border-[#29ABE2]/20 bg-[#29ABE2]/5 p-4">
                            <p class="text-sm text-slate-300 text-center">{{ __('app.sign_in') }}</p>
                            <a href="{{ route('login') }}"
                               class="mt-3 block w-full rounded-xl py-2.5 text-center text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                               style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                {{ __('app.login') }}
                            </a>
                            <a href="{{ route('register') }}"
                               class="mt-2 block text-center text-sm font-semibold text-[#29ABE2] hover:text-[#4CBDE8]">
                                {{ __('app.create_account') }}
                            </a>
                        </div>
                    @else
                        @if($remaining <= 0)
                            <button type="button" disabled
                                    class="mt-6 w-full cursor-not-allowed rounded-xl py-3 text-sm font-semibold text-slate-500 bg-white/5">
                                {{ __('app.event_full') }}
                            </button>
                        @else
                            <form method="POST" action="{{ route('registrations.store', $e->id) }}" class="mt-6">
                                @csrf
                                <button type="submit"
                                        class="w-full rounded-xl py-3 text-sm font-semibold text-white transition-all hover:scale-[1.02] hover:shadow-lg hover:shadow-[#29ABE2]/25"
                                        style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                    {{ __('app.register_now') }}
                                </button>
                            </form>
                            <p class="mt-2 text-center text-xs text-slate-500">
                                {{ __('app.registration_confirmed') }}
                            </p>
                        @endif
                    @endguest
                </div>
            </div>

        </div>
    </div>
@endsection