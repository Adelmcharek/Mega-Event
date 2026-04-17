@extends('layouts.public')

@section('title', $event->title ?? 'Event')

@section('content')
    @php
        $e = is_object($event) ? $event : (object) $event;
        $date = \Illuminate\Support\Carbon::parse($e->date ?? now());
        $cap = (int) ($e->capacity ?? 0);
        $reg = (int) ($e->registered ?? $e->registered_count ?? 0);
        $remaining = max(0, $cap - $reg);
        $price = $e->price ?? null;
    @endphp

    <div class="relative h-64 w-full overflow-hidden bg-gradient-to-br from-brand-500 via-violet-600 to-blue-500 md:h-80">
        @if (! empty($e->cover_url))
            <img src="{{ $e->cover_url }}" alt="" class="absolute inset-0 h-full w-full object-cover mix-blend-overlay opacity-90">
        @endif
        <div class="absolute inset-0 bg-hero/40"></div>
        <div class="relative mx-auto flex h-full max-w-7xl flex-col justify-end px-4 pb-10 sm:px-6 lg:px-8">
            <span class="inline-flex w-fit rounded-lg bg-white/20 px-2 py-1 text-xs font-semibold text-white backdrop-blur">{{ $e->category ?? 'Event' }}</span>
            <h1 class="mt-3 text-3xl font-extrabold text-white drop-shadow md:text-4xl">{{ $e->title ?? 'Event' }}</h1>
            <p class="mt-2 flex flex-wrap gap-4 text-sm font-medium text-violet-100">
                <span>📅 {{ $date->format('D, M d Y — H:i') }}</span>
                <span>📍 {{ $e->location ?? 'TBA' }}</span>
            </p>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <a href="{{ route('events.index') }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600">← Back to events</a>

        <div class="mt-8 grid gap-10 lg:grid-cols-10">
            <div class="lg:col-span-7">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">About this event</h2>
                    <div class="prose prose-slate mt-4 max-w-none text-slate-600">
                        {!! nl2br(e($e->description ?? 'Join us for an unforgettable experience. More details will be available soon.')) !!}
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Schedule &amp; agenda</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse($agenda ?? [] as $item)
                            <li class="flex gap-3 rounded-xl bg-slate-50 px-4 py-3">
                                <span class="font-semibold text-brand-500">{{ $item->time ?? $item['time'] ?? '' }}</span>
                                <span>{{ $item->label ?? $item['label'] ?? '' }}</span>
                            </li>
                        @empty
                            <li class="rounded-xl bg-slate-50 px-4 py-3">Opening remarks, keynotes, and networking — full timetable announced before the event.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Partners</h2>
                    <div class="mt-4 flex flex-wrap gap-4">
                        @forelse($partners ?? ['MEGA PIXEL', 'EventHub', 'City Sponsors'] as $p)
                            @php $name = is_string($p) ? $p : ($p->name ?? 'Partner'); @endphp
                            <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700">{{ $name }}</span>
                        @empty
                            <p class="text-sm text-slate-500">Partners to be announced.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-content p-6">
                    <h2 class="text-lg font-bold text-slate-900">Participants</h2>
                    <p class="mt-2 text-3xl font-extrabold text-brand-500">{{ $participantsCount ?? $reg }}</p>
                    <p class="text-sm text-slate-600">people registered so far</p>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="sticky top-24 rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                    <p class="text-sm font-medium text-slate-500">Price</p>
                    <p class="mt-1 text-2xl font-bold text-slate-900">{{ $price !== null && $price !== '' ? number_format((float) $price, 2).' €' : 'Free' }}</p>
                    <p class="mt-4 text-sm font-medium text-slate-500">Spots remaining</p>
                    <p class="text-xl font-bold text-brand-500">{{ $remaining }} / {{ $cap }}</p>
                    <div class="mt-4">
                        <x-capacity-bar :current="$reg" :max="max(1,$cap)" />
                    </div>
                    @guest
                        <p class="mt-6 text-sm text-slate-600">Please log in to register for this event.</p>
                        <a href="{{ route('login') }}" class="btn-gradient mt-4 block w-full py-3 text-center text-sm">Login to register</a>
                        <a href="{{ route('register') }}" class="mt-2 block text-center text-sm font-semibold text-brand-500">Create an account</a>
                    @else
                        @if ($remaining <= 0)
                            <button type="button" disabled class="btn-gradient mt-6 w-full cursor-not-allowed py-3 opacity-50">Event full</button>
                        @else
                            <form method="POST" action="#" class="mt-6" onsubmit="return false;">
                                @csrf
                                <button type="button" class="btn-gradient w-full py-3 text-sm">Register Now</button>
                            </form>
                            <p class="mt-2 text-center text-xs text-slate-400">Registration will connect to your backend route.</p>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </div>
@endsection
