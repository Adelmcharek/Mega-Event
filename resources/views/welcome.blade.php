@extends('layouts.public')

@section('title', 'Home')

@section('content')
    
   

    {{-- Hero --}}
    <section
        class="hero-section relative min-h-screen w-full overflow-hidden flex items-center"
    >
        <div class="pointer-events-none absolute left-1/4 top-20 z-0 h-72 w-72 animate-pulse rounded-full bg-[#29ABE2]/20 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute bottom-20 right-1/4 z-0 h-96 w-96 animate-pulse rounded-full bg-[#0A7FA8]/15 blur-3xl" style="animation-delay: 1s;" aria-hidden="true"></div>

        <div class="relative z-10 mx-auto flex w-full max-w-7xl flex-col items-center gap-12 px-6 py-20 lg:flex-row">
            <div class="min-w-0 flex-1 text-white">
                <h1 class="mb-6 text-5xl font-extrabold leading-tight text-white lg:text-6xl">
                    {{ __('app.hero_title') }}
                </h1>
                <p class="mb-8 max-w-lg text-lg text-slate-300">
                    {{ __('app.hero_subtitle') }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('events.index') }}"
                       class="inline-block rounded-full bg-gradient-to-r from-[#29ABE2] to-[#0A7FA8] px-8 py-4 font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-[#29ABE2]/25">
                        {{ __('app.browse_events') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-block rounded-full border-2 border-[#29ABE2] px-8 py-4 font-semibold text-[#29ABE2] transition-all duration-300 hover:bg-[#29ABE2]/10">
                        {{ __('app.organize_event') }}
                    </a>
                </div>
            </div>

            <div class="relative hidden h-96 w-full min-w-0 flex-1 overflow-hidden lg:block">
                <div class="relative mx-auto h-full w-full max-w-md">
                    <div class="absolute left-0 top-3 z-10 w-60 rounded-2xl border border-white/20 bg-white/10 p-4 text-white shadow-xl backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-[#29ABE2] to-[#0A7FA8] text-lg">🎤</span>
                            <div class="min-w-0">
                                <p class="text-xs text-[#29ABE2]">Conference</p>
                                <p class="truncate font-bold text-white">Innovation Summit</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-slate-200">Jun 12 · Convention Center</p>
                    </div>
                    <div class="absolute right-0 top-[8.5rem] z-20 w-60 rounded-2xl border border-white/20 bg-white/10 p-4 text-white shadow-xl backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-[#29ABE2] to-[#0A7FA8] text-lg">🎭</span>
                            <div class="min-w-0">
                                <p class="text-xs text-[#29ABE2]">Cultural</p>
                                <p class="truncate font-bold text-white">Art &amp; Jazz Night</p>
                            </div>
                        </div>
                        <p class="mt-3 text-xs text-slate-200">Jul 03 · Grand Theatre</p>
                    </div>
                    <div class="absolute bottom-6 left-4 z-10 w-60 rounded-2xl border border-white/20 bg-white/10 p-4 text-white shadow-xl backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-[#29ABE2] to-[#0A7FA8] text-lg">🏢</span>
                            <div class="min-w-0">
                                <p class="text-xs text-[#29ABE2]">Corporate</p>
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
        class="border-y border-white/10 py-10"
        style="background-color: #111111;"
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
                    this.targets.forEach((t, i) => { this.counts[i] = Math.floor(t * p) })
                    if (p < 1) requestAnimationFrame(tick)
                    else this.targets.forEach((t, i) => { this.counts[i] = t })
                }
                requestAnimationFrame(tick)
            }
        }"
        x-init="start()"
    >
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-center gap-8 px-4 sm:flex-row sm:gap-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-3xl font-extrabold text-[#29ABE2]" x-text="counts[0] + '+'">0+</p>
                <p class="text-sm font-medium text-slate-400">{{ __('app.stat_events') }}</p>
            </div>
            <div class="hidden h-10 w-px bg-white/10 sm:block" aria-hidden="true"></div>
            <div class="text-center">
                <p class="text-3xl font-extrabold text-[#29ABE2]" x-text="counts[1].toLocaleString() + '+'">0+</p>
                <p class="text-sm font-medium text-slate-400">{{ __('app.stat_participants') }}</p>
            </div>
            <div class="hidden h-10 w-px bg-white/10 sm:block" aria-hidden="true"></div>
            <div class="text-center">
                <p class="text-3xl font-extrabold text-[#29ABE2]" x-text="counts[2] + '+'">0+</p>
                <p class="text-sm font-medium text-slate-400">{{ __('app.stat_organizers') }}</p>
            </div>
        </div>
    </section>

    {{-- Featured Events --}}
    <section class="welcome-section py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-3xl font-bold text-white">{{ __('app.featured_events') }}</h2>
                    <p class="mt-2 text-slate-400">{{ __('app.featured_subtitle') }}</p>
                </div>
                <a href="{{ route('events.index') }}" class="text-sm font-semibold text-[#29ABE2] hover:text-[#4CBDE8]">{{ __('app.view_all_events') }}</a>
            </div>
            <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($featuredEvents ?? [] as $event)
                    <x-event-card :event="$event" />
                @empty
                    @foreach ([
                        ['id' => 1, 'title' => 'Digital Leadership Forum', 'date' => now()->addDays(14), 'location' => 'Tunis Convention Center', 'category' => 'Professional', 'capacity' => 200, 'registered' => 142, 'status' => 'OPEN'],
                        ['id' => 2, 'title' => 'Summer Culture Fest', 'date' => now()->addDays(30), 'location' => 'Sfax Grand Theatre', 'category' => 'Cultural', 'capacity' => 500, 'registered' => 500, 'status' => 'FULL'],
                        ['id' => 3, 'title' => 'Corporate Innovation Day', 'date' => now()->addDays(45), 'location' => 'Sousse Tech Hub', 'category' => 'Corporate', 'capacity' => 120, 'registered' => 40, 'status' => 'OPEN'],
                    ] as $placeholder)
                        <x-event-card :event="(object) $placeholder" />
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="welcome-section border-t border-white/10 py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-3xl font-bold text-white">{{ __('app.how_it_works') }}</h2>
            <p class="mx-auto mt-2 max-w-2xl text-center text-slate-400">{{ __('app.how_subtitle') }}</p>
            <div class="mt-14 grid gap-10 md:grid-cols-3">
                @foreach([
                    ['num' => '1', 'title' => __('app.step1_title'), 'desc' => __('app.step1_desc')],
                    ['num' => '2', 'title' => __('app.step2_title'), 'desc' => __('app.step2_desc')],
                    ['num' => '3', 'title' => __('app.step3_title'), 'desc' => __('app.step3_desc')],
                ] as $step)
                <div class="how-card rounded-2xl border border-white/10 p-8 text-center shadow-sm transition hover:border-[#29ABE2]/30 hover:shadow-[#29ABE2]/10"
                     style="background-color: #1a1a2e;">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#29ABE2] to-[#0A7FA8] text-2xl font-bold text-white shadow-lg">
                        {{ $step['num'] }}
                    </div>
                    <h3 class="mt-6 text-lg font-bold text-white">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-400">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA Banner --}}
    <section style="background: linear-gradient(135deg, #0A7FA8, #29ABE2, #0A7FA8);" class="py-14">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-2xl font-extrabold text-white sm:text-3xl">{{ __('app.cta_title') }}</h2>
            <p class="mt-2 text-white/80">{{ __('app.cta_subtitle') }}</p>
            <a href="{{ route('register') }}"
               class="mt-8 inline-flex rounded-xl bg-white px-8 py-3 font-bold text-[#0A7FA8] shadow-lg transition hover:bg-slate-100">
                {{ __('app.cta_button') }}
            </a>
        </div>
    </section>
@endsection