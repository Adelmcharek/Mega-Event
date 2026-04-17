@extends('layouts.public')

@section('title', 'Home')

@section('content')
    {{-- Hero --}}
    <section
        class="relative min-h-screen w-full overflow-hidden flex items-center"
        style="background: linear-gradient(135deg, #0F172A 0%, #1E1040 50%, #0F172A 100%);"
    >
        {{-- Background blobs (behind content; no overlap with text — z-index) --}}
        <div
            class="pointer-events-none absolute left-1/4 top-20 z-0 h-72 w-72 animate-pulse rounded-full bg-purple-600/30 blur-3xl"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute bottom-20 right-1/4 z-0 h-96 w-96 animate-pulse rounded-full bg-blue-600/20 blur-3xl"
            style="animation-delay: 1s;"
            aria-hidden="true"
        ></div>

        {{-- Content container --}}
        <div class="relative z-10 mx-auto flex w-full max-w-7xl flex-col items-center gap-12 px-6 py-20 lg:flex-row">
            {{-- LEFT: 50% — headline, subtitle, buttons --}}
            <div class="min-w-0 flex-1 text-white">
                <p class="mb-4 text-sm font-semibold uppercase tracking-widest text-purple-400">
                    By Mega Pixel Agency
                </p>
                <h1 class="mb-6 text-5xl font-extrabold leading-tight text-white lg:text-6xl">
                    Discover &amp; Join <br>
                    <span class="bg-gradient-to-r from-purple-400 to-blue-400 bg-clip-text text-transparent">
                        Extraordinary Events
                    </span>
                </h1>
                <p class="mb-8 max-w-lg text-lg text-slate-300">
                    MEGA EVENT brings people together. Find professional, cultural, and corporate events near you.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a
                        href="{{ route('events.index') }}"
                        class="inline-block rounded-full bg-gradient-to-r from-purple-600 to-blue-500 px-8 py-4 font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-purple-500/50"
                    >
                        Browse Events
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="inline-block rounded-full border-2 border-purple-400 px-8 py-4 font-semibold text-purple-300 transition-all duration-300 hover:bg-purple-400/10"
                    >
                        Organize an Event
                    </a>
                </div>
            </div>

            {{-- RIGHT: 50% — floating cards inside h-96 relative container --}}
            <div class="relative hidden h-96 w-full min-w-0 flex-1 overflow-hidden lg:block">
                <div class="relative mx-auto h-full w-full max-w-md">
                    <div
                        class="absolute left-0 top-3 z-10 w-60 max-w-[calc(100%-0.5rem)] rounded-2xl border border-white/20 bg-white/10 p-4 text-white shadow-xl backdrop-blur-md"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-purple-600 to-blue-500 text-lg">🎤</span>
                            <div class="min-w-0">
                                <p class="text-xs text-purple-200">Conference</p>
                                <p class="truncate font-bold text-white">Innovation Summit</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-slate-200">Jun 12 · Convention Center</p>
                    </div>
                    <div
                        class="absolute right-0 top-[8.5rem] z-20 w-60 max-w-[calc(100%-0.5rem)] rounded-2xl border border-white/20 bg-white/10 p-4 text-white shadow-xl backdrop-blur-md"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-purple-600 to-blue-500 text-lg">🎭</span>
                            <div class="min-w-0">
                                <p class="text-xs text-purple-200">Cultural</p>
                                <p class="truncate font-bold text-white">Art &amp; Jazz Night</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-slate-200">Jul 03 · Grand Theatre</p>
                    </div>
                    <div
                        class="absolute bottom-6 left-4 z-10 w-60 max-w-[calc(100%-0.5rem)] rounded-2xl border border-white/20 bg-white/10 p-4 text-white shadow-xl backdrop-blur-md"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-purple-600 to-blue-500 text-lg">🏢</span>
                            <div class="min-w-0">
                                <p class="text-xs text-purple-200">Corporate</p>
                                <p class="truncate font-bold text-white">Team Summit 2026</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-slate-200">Aug 20 · HQ Rooftop</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section
        class="border-y border-slate-200 bg-white py-10"
        x-data="{
            targets: [500, 10000, 200],
            counts: [0, 0, 0],
            started: false,
            start() {
                if (this.started) return
                this.started = true
                const duration = 1800
                const start = performance.now()
                const tick = (now) => {
                    const p = Math.min(1, (now - start) / duration)
                    this.targets.forEach((t, i) => {
                        this.counts[i] = Math.floor(t * p)
                    })
                    if (p < 1) requestAnimationFrame(tick)
                    else this.targets.forEach((t, i) => { this.counts[i] = t })
                }
                requestAnimationFrame(tick)
            }
        }"
        x-init="start()"
    >
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-center gap-8 px-4 sm:flex-row sm:gap-16 sm:px-6 lg:px-8">
            <div class="animate-count text-center">
                <p class="text-3xl font-extrabold text-brand-500" x-text="counts[0] + '+'">0+</p>
                <p class="text-sm font-medium text-slate-600">Events</p>
            </div>
            <div class="hidden h-10 w-px bg-slate-200 sm:block" aria-hidden="true"></div>
            <div class="animate-count text-center">
                <p class="text-3xl font-extrabold text-brand-500" x-text="counts[1].toLocaleString() + '+'">0+</p>
                <p class="text-sm font-medium text-slate-600">Participants</p>
            </div>
            <div class="hidden h-10 w-px bg-slate-200 sm:block" aria-hidden="true"></div>
            <div class="animate-count text-center">
                <p class="text-3xl font-extrabold text-brand-500" x-text="counts[2] + '+'">0+</p>
                <p class="text-sm font-medium text-slate-600">Partners</p>
            </div>
        </div>
    </section>

    {{-- Featured events --}}
    <section class="bg-content py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Featured Events</h2>
                    <p class="mt-2 text-slate-600">Hand-picked experiences happening soon.</p>
                </div>
                <a href="{{ route('events.index') }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600">View all events →</a>
            </div>
            <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($featuredEvents ?? [] as $event)
                    <x-event-card :event="$event" />
                @empty
                    @foreach ([
                        ['id' => 1, 'title' => 'Digital Leadership Forum', 'date' => now()->addDays(14), 'location' => 'Paris', 'category' => 'Professional', 'capacity' => 200, 'registered' => 142, 'status' => 'OPEN'],
                        ['id' => 2, 'title' => 'Summer Culture Fest', 'date' => now()->addDays(30), 'location' => 'Lyon', 'category' => 'Cultural', 'capacity' => 500, 'registered' => 500, 'status' => 'FULL'],
                        ['id' => 3, 'title' => 'Corporate Innovation Day', 'date' => now()->addDays(45), 'location' => 'Remote', 'category' => 'Corporate', 'capacity' => 120, 'registered' => 40, 'status' => 'OPEN'],
                    ] as $placeholder)
                        <x-event-card :event="(object) $placeholder" />
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="border-t border-slate-200 bg-white py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl font-bold text-slate-900">How it works</h2>
            <p class="mx-auto mt-2 max-w-2xl text-center text-slate-600">Three simple steps from idea to unforgettable experience.</p>
            <div class="mt-14 grid gap-10 md:grid-cols-3">
                <div class="rounded-2xl border border-slate-100 bg-content p-8 text-center shadow-sm transition hover:shadow-md">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-600 to-blue-500 text-2xl text-white shadow-lg">1</div>
                    <h3 class="mt-6 text-lg font-bold text-slate-900">Create Event</h3>
                    <p class="mt-2 text-sm text-slate-600">Organizers submit events for admin review and publish when approved.</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-content p-8 text-center shadow-sm transition hover:shadow-md">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-600 to-blue-500 text-2xl text-white shadow-lg">2</div>
                    <h3 class="mt-6 text-lg font-bold text-slate-900">Register</h3>
                    <p class="mt-2 text-sm text-slate-600">Participants browse, filter, and secure their spot in seconds.</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-content p-8 text-center shadow-sm transition hover:shadow-md">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-600 to-blue-500 text-2xl text-white shadow-lg">3</div>
                    <h3 class="mt-6 text-lg font-bold text-slate-900">Attend</h3>
                    <p class="mt-2 text-sm text-slate-600">Show up, connect, and enjoy a seamless on-site experience.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA banner --}}
    <section class="bg-gradient-to-r from-brand-500 via-violet-600 to-blue-500 py-14">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold text-white sm:text-3xl">Ready to organize your next event?</h2>
            <p class="mt-2 text-violet-100">Get started free and reach thousands of attendees.</p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex rounded-xl bg-white px-8 py-3 font-bold text-brand-500 shadow-lg transition hover:bg-slate-100">
                Create your organizer account
            </a>
        </div>
    </section>
@endsection
