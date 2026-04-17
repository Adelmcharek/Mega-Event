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
    <div class="relative h-64 w-full overflow-hidden md:h-80"
         style="background: linear-gradient(135deg, #0D1F2D 0%, #29ABE2 100%);">
        @if(!empty($e->cover_url))
            <img src="{{ $e->cover_url }}" alt="" class="absolute inset-0 h-full w-full object-cover mix-blend-overlay opacity-90">
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
                <span>📍 {{ $e->location ?? 'TBA' }}</span>
            </p>
        </div>
    </div>

    {{-- Content --}}
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
         style="background-color: #0A0A0A;">

        <a href="{{ route('events.index') }}"
           class="text-sm font-semibold text-[#29ABE2] hover:text-[#4CBDE8] transition-colors">
            ← Back to events
        </a>

        <div class="mt-8 grid gap-10 lg:grid-cols-10">

            {{-- LEFT: Main content --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- About --}}
                <div class="rounded-2xl border border-white/10 p-6 shadow-sm"
                     style="background-color: #1a1a2e;">
                    <h2 class="text-lg font-bold text-white">About this event</h2>
                    <div class="mt-4 text-slate-300 leading-relaxed">
                        {!! nl2br(e($e->description ?? 'Join us for an unforgettable experience. More details will be available soon.')) !!}
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="rounded-2xl border border-white/10 p-6 shadow-sm"
                     style="background-color: #1a1a2e;">
                    <h2 class="text-lg font-bold text-white">Schedule &amp; agenda</h2>
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
                <div class="rounded-2xl border border-white/10 p-6 shadow-sm"
                     style="background-color: #1a1a2e;">
                    <h2 class="text-lg font-bold text-white">Partners</h2>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @forelse($partners ?? ['MEGA PIXEL', 'EventHub', 'City Sponsors'] as $p)
                            @php $name = is_string($p) ? $p : ($p->name ?? 'Partner'); @endphp
                            <span class="inline-flex items-center rounded-xl border border-[#29ABE2]/30 bg-[#29ABE2]/10 px-4 py-2 text-sm font-semibold text-[#29ABE2]">
                                {{ $name }}
                            </span>
                        @empty
                            <p class="text-sm text-slate-500">Partners to be announced.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Participants count --}}
                <div class="rounded-2xl border border-white/10 p-6"
                     style="background: linear-gradient(135deg, rgba(41,171,226,0.05), rgba(10,127,168,0.05));">
                    <h2 class="text-lg font-bold text-white">Participants</h2>
                    <p class="mt-2 text-4xl font-extrabold text-[#29ABE2]">
                        {{ $participantsCount ?? $reg }}
                    </p>
                    <p class="text-sm text-slate-400">people registered so far</p>
                </div>
            </div>

            {{-- RIGHT: Registration card --}}
            <div class="lg:col-span-3">
                <div class="sticky top-24 rounded-2xl border border-[#29ABE2]/30 p-6 shadow-2xl shadow-[#29ABE2]/10"
                     style="background-color: #1a1a2e;">

                    <p class="text-sm font-medium text-slate-400">Price</p>
                    <p class="mt-1 text-2xl font-bold text-white">
                        {{ $price !== null && $price !== '' ? number_format((float) $price, 2).' TND' : 'Free' }}
                    </p>

                    <div class="mt-4">
                        <p class="text-sm font-medium text-slate-400">Spots remaining</p>
                        <p class="text-xl font-bold text-[#29ABE2]">{{ $remaining }} / {{ $cap }}</p>
                    </div>

                    {{-- Capacity bar --}}
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-slate-400 mb-1">
                            <span>{{ $reg }} / {{ $cap }} spots filled</span>
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
                            <p class="text-sm text-slate-300 text-center">Login to register for this event</p>
                            <a href="{{ route('login') }}"
                               class="mt-3 block w-full rounded-xl py-2.5 text-center text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                               style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                Login to register
                            </a>
                            <a href="{{ route('register') }}"
                               class="mt-2 block text-center text-sm font-semibold text-[#29ABE2] hover:text-[#4CBDE8]">
                                Create an account
                            </a>
                        </div>
                    @else
                        @if($remaining <= 0)
                            <button type="button" disabled
                                    class="mt-6 w-full cursor-not-allowed rounded-xl py-3 text-sm font-semibold text-slate-500 bg-white/5">
                                Event Full
                            </button>
                        @else
                            <form method="POST" action="{{ route('events.register', $e->id) }}" class="mt-6">
                                @csrf
                                <button type="submit"
                                        class="w-full rounded-xl py-3 text-sm font-semibold text-white transition-all hover:scale-[1.02] hover:shadow-lg hover:shadow-[#29ABE2]/25"
                                        style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                    Register Now
                                </button>
                            </form>
                            <p class="mt-2 text-center text-xs text-slate-500">
                                Secure your spot today
                            </p>
                        @endif
                    @endguest
                </div>
            </div>

        </div>
    </div>
@endsection