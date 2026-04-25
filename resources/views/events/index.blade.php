@extends('layouts.public')

@section('title', __('app.upcoming_events'))

@section('content')

{{-- Hero filter bar --}}
<div class="border-b border-white/10" style="background-color: #111111;">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white">{{ __('app.upcoming_events') }}</h1>
        <p class="mt-2 text-slate-400">{{ __('app.browse_professional') }}</p>

        <form method="GET" action="{{ route('events.index') }}"
              id="filter-form"
              class="mt-8 space-y-5">

            {{-- ROW 1: Search + Dates + Location + Apply --}}
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-slate-400 mb-1">🔍 {{ __('app.keyword_search') }}</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">🔍</span>
                        <input name="search" type="search"
                               value="{{ request('search') }}"
                               placeholder="{{ __('app.search_placeholder') }}"
                               class="w-full rounded-xl border border-white/10 py-2.5 pl-10 pr-3 text-white placeholder-slate-500 focus:border-[#29ABE2] focus:outline-none"
                               style="background-color: #1a1a2e;">
                    </div>
                </div>

                <div class="w-full lg:w-44">
                    <label class="block text-xs font-medium text-slate-400 mb-1">📅 {{ __('app.from_date') }}</label>
                    <input name="date_from" type="date"
                           value="{{ request('date_from') }}"
                           class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white focus:border-[#29ABE2] focus:outline-none"
                           style="background-color: #1a1a2e;">
                </div>

                <div class="w-full lg:w-44">
                    <label class="block text-xs font-medium text-slate-400 mb-1">📅 {{ __('app.to_date') }}</label>
                    <input name="date_to" type="date"
                           value="{{ request('date_to') }}"
                           class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white focus:border-[#29ABE2] focus:outline-none"
                           style="background-color: #1a1a2e;">
                </div>

                <div class="w-full lg:w-52">
                    <label class="block text-xs font-medium text-slate-400 mb-1">📍 {{ __('app.location_filter') }}</label>
                    <input name="location" type="text"
                           value="{{ request('location') }}"
                           placeholder="{{ __('app.city_venue') }}"
                           class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500 focus:border-[#29ABE2] focus:outline-none"
                           style="background-color: #1a1a2e;">
                </div>

                <button type="submit"
                        class="rounded-xl px-6 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25 whitespace-nowrap"
                        style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                    {{ __('app.apply_filters') }}
                </button>
            </div>

            {{-- ROW 2: Category + Mode + Sort + Reset --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-slate-500">{{ __('app.category_label') }}</span>
                    @foreach([
                        'all'          => __('app.category_all'),
                        'professional' => __('app.cat_professional'),
                        'cultural'     => __('app.cat_cultural'),
                        'corporate'    => __('app.cat_corporate'),
                        'educational'  => __('app.cat_educational'),
                        'social'       => __('app.cat_social'),
                        'sports'       => __('app.cat_sports'),
                    ] as $cat => $catLabel)
                        <a href="{{ route('events.index', array_merge(request()->except('category'), ['category' => $cat])) }}"
                           class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all
                           {{ request('category', 'all') === $cat
                              ? 'text-white'
                              : 'border border-white/10 text-slate-400 hover:text-white hover:border-white/30' }}"
                           style="{{ request('category', 'all') === $cat
                              ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);'
                              : 'background-color: #1a1a2e;' }}">
                            {{ $catLabel }}
                        </a>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-slate-500 mr-1">{{ __('app.mode_label') }}</span>
                        @foreach(['all' => __('app.mode_all'), 'online' => __('app.mode_online'), 'onsite' => __('app.mode_onsite')] as $val => $label)
                            <a href="{{ route('events.index', array_merge(request()->except('mode'), ['mode' => $val])) }}"
                               class="px-3 py-1 rounded-full text-xs font-semibold transition-all
                               {{ request('mode', 'all') === $val
                                  ? 'text-white'
                                  : 'border border-white/10 text-slate-400 hover:text-white' }}"
                               style="{{ request('mode', 'all') === $val
                                  ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);'
                                  : 'background-color: #1a1a2e;' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-medium text-slate-500">{{ __('app.sort_label') }}</label>
                        <select name="sort" form="filter-form"
                                onchange="this.form.submit()"
                                class="rounded-xl border border-white/10 px-3 py-1.5 text-xs text-white focus:border-[#29ABE2] focus:outline-none"
                                style="background-color: #1a1a2e;">
                            <option value="date_asc"  {{ request('sort', 'date_asc') === 'date_asc'  ? 'selected' : '' }}>📅 {{ __('app.sort_date_asc') }}</option>
                            <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>📅 {{ __('app.sort_date_desc') }}</option>
                            <option value="capacity"  {{ request('sort') === 'capacity'  ? 'selected' : '' }}>🪑 {{ __('app.sort_capacity') }}</option>
                            <option value="alpha_asc" {{ request('sort') === 'alpha_asc' ? 'selected' : '' }}>🔤 {{ __('app.sort_alpha') }}</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['search', 'date_from', 'date_to', 'location', 'category', 'mode', 'sort']))
                        <a href="{{ route('events.index') }}"
                           class="rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-400 hover:bg-red-500/20 transition-all">
                            {{ __('app.reset_filters') }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Active filters --}}
            @if(request()->hasAny(['search', 'date_from', 'date_to', 'location']))
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="text-xs text-slate-500">{{ __('app.active_filters') }}</span>
                    @if(request('search'))
                        <span class="rounded-full bg-[#29ABE2]/10 border border-[#29ABE2]/30 px-3 py-0.5 text-xs text-[#29ABE2]">
                            🔍 "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('date_from'))
                        <span class="rounded-full bg-[#29ABE2]/10 border border-[#29ABE2]/30 px-3 py-0.5 text-xs text-[#29ABE2]">
                            {{ __('app.filter_from') }} {{ request('date_from') }}
                        </span>
                    @endif
                    @if(request('date_to'))
                        <span class="rounded-full bg-[#29ABE2]/10 border border-[#29ABE2]/30 px-3 py-0.5 text-xs text-[#29ABE2]">
                            {{ __('app.filter_to') }} {{ request('date_to') }}
                        </span>
                    @endif
                    @if(request('location'))
                        <span class="rounded-full bg-[#29ABE2]/10 border border-[#29ABE2]/30 px-3 py-0.5 text-xs text-[#29ABE2]">
                            📍 {{ request('location') }}
                        </span>
                    @endif
                </div>
            @endif

        </form>
    </div>
</div>

{{-- Events Grid --}}
<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8"
     style="background-color: #0A0A0A;">

    @if($upcomingEvents->isEmpty() && $pastEvents->isEmpty())
        {{-- Empty state --}}
        <div class="flex flex-col items-center gap-3 rounded-2xl border border-white/10 py-16 text-center"
             style="background-color: #1a1a2e;">
            <span class="text-5xl">🔍</span>
            <p class="font-semibold text-white">{{ __('app.no_events_match') }}</p>
            <p class="text-sm text-slate-400">{{ __('app.try_adjusting') }}</p>
            <a href="{{ route('events.index') }}"
               class="mt-2 inline-flex rounded-xl px-5 py-2.5 text-sm font-semibold text-white"
               style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                {{ __('app.clear_filters') }}
            </a>
        </div>
    @else
        {{-- Upcoming events --}}
        @if($upcomingEvents->isNotEmpty())
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($upcomingEvents as $event)
                    <x-event-card :event="$event" :show-register="true" />
                @endforeach
            </div>
        @endif

        {{-- Past events separator + grid --}}
        @if($pastEvents->isNotEmpty())
            <div class="mt-12 mb-8 flex items-center gap-4">
                <div class="h-px flex-1" style="background-color: rgba(255,255,255,0.1);"></div>
                <span class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                    {{ __('app.past_events') }}
                </span>
                <div class="h-px flex-1" style="background-color: rgba(255,255,255,0.1);"></div>
            </div>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($pastEvents as $event)
                    <x-event-card :event="$event" :show-register="true" />
                @endforeach
            </div>
        @endif
    @endif
</div>

@endsection