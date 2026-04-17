@extends('layouts.public')

@section('title', 'Upcoming Events')

@section('content')

{{-- Hero filter bar --}}
<div class="border-b border-white/10" style="background-color: #111111;">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white">Upcoming Events</h1>
        <p class="mt-2 text-slate-400">Browse professional, cultural, and corporate experiences.</p>

        <form method="GET" action="{{ route('events.index') }}"
              class="mt-8 space-y-5"
              x-data="{
                  cat: '{{ request('category', 'all') }}',
                  mode: '{{ request('mode', 'all') }}',
                  sort: '{{ request('sort', 'date_asc') }}'
              }">

            {{-- ROW 1: Search + Dates + Location + Apply --}}
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-slate-400 mb-1">🔍 Keyword search</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">🔍</span>
                        <input name="search" type="search"
                               value="{{ request('search') }}"
                               placeholder="Search by title, description..."
                               class="w-full rounded-xl border border-white/10 py-2.5 pl-10 pr-3 text-white placeholder-slate-500 focus:border-[#29ABE2] focus:outline-none"
                               style="background-color: #1a1a2e;">
                    </div>
                </div>

                <div class="w-full lg:w-44">
                    <label class="block text-xs font-medium text-slate-400 mb-1">📅 From date</label>
                    <input name="date_from" type="date"
                           value="{{ request('date_from') }}"
                           class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white focus:border-[#29ABE2] focus:outline-none"
                           style="background-color: #1a1a2e;">
                </div>

                <div class="w-full lg:w-44">
                    <label class="block text-xs font-medium text-slate-400 mb-1">📅 To date</label>
                    <input name="date_to" type="date"
                           value="{{ request('date_to') }}"
                           class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white focus:border-[#29ABE2] focus:outline-none"
                           style="background-color: #1a1a2e;">
                </div>

                <div class="w-full lg:w-52">
                    <label class="block text-xs font-medium text-slate-400 mb-1">📍 Location</label>
                    <input name="location" type="text"
                           value="{{ request('location') }}"
                           placeholder="City or venue"
                           class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500 focus:border-[#29ABE2] focus:outline-none"
                           style="background-color: #1a1a2e;">
                </div>

                <button type="submit"
                        class="rounded-xl px-6 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25 whitespace-nowrap"
                        style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                    Apply Filters
                </button>
            </div>

            {{-- ROW 2: Category + Mode + Sort + Reset --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-medium text-slate-500">Category:</span>
                    <input type="hidden" name="category" x-model="cat">
                    @foreach(['all' => 'All', 'professional' => '💼 Professional', 'cultural' => '🎭 Cultural', 'corporate' => '🏢 Corporate'] as $val => $label)
                        <button type="button"
                                @click="cat = '{{ $val }}'; $el.closest('form').submit()"
                                class="rounded-full px-4 py-1.5 text-xs font-semibold transition-all border"
                                :class="cat === '{{ $val }}'
                                    ? 'border-[#29ABE2] text-white'
                                    : 'border-white/10 text-slate-400 hover:border-white/20 hover:text-white'"
                                :style="cat === '{{ $val }}'
                                    ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8)'
                                    : 'background-color: #1a1a2e'">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-slate-500 mr-1">Mode:</span>
                        <input type="hidden" name="mode" x-model="mode">
                        @foreach(['all' => '🌐 All', 'online' => '💻 Online', 'onsite' => '📍 On-site'] as $val => $label)
                            <button type="button"
                                    @click="mode = '{{ $val }}'; $el.closest('form').submit()"
                                    class="rounded-full px-3 py-1 text-xs font-semibold transition-all border"
                                    :class="mode === '{{ $val }}'
                                        ? 'bg-[#29ABE2]/20 text-[#29ABE2] border-[#29ABE2]/50'
                                        : 'border-white/10 text-slate-400 hover:text-white'"
                                    style="background-color: #1a1a2e;">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-xs font-medium text-slate-500">Sort:</label>
                        <select name="sort" x-model="sort"
                                onchange="this.form.submit()"
                                class="rounded-xl border border-white/10 px-3 py-1.5 text-xs text-white focus:border-[#29ABE2] focus:outline-none"
                                style="background-color: #1a1a2e;">
                            <option value="date_asc">📅 Date (earliest)</option>
                            <option value="date_desc">📅 Date (latest)</option>
                            <option value="capacity_asc">🪑 Spots (fewest)</option>
                            <option value="title_asc">🔤 Title A-Z</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['search', 'date_from', 'date_to', 'location', 'category', 'mode', 'sort']))
                        <a href="{{ route('events.index') }}"
                           class="rounded-xl border border-red-500/30 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-400 hover:bg-red-500/20 transition-all">
                            ✕ Reset
                        </a>
                    @endif
                </div>
            </div>

            {{-- Active filters --}}
            @if(request()->hasAny(['search', 'date_from', 'date_to', 'location']))
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="text-xs text-slate-500">Active filters:</span>
                    @if(request('search'))
                        <span class="rounded-full bg-[#29ABE2]/10 border border-[#29ABE2]/30 px-3 py-0.5 text-xs text-[#29ABE2]">
                            🔍 "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('date_from'))
                        <span class="rounded-full bg-[#29ABE2]/10 border border-[#29ABE2]/30 px-3 py-0.5 text-xs text-[#29ABE2]">
                            From: {{ request('date_from') }}
                        </span>
                    @endif
                    @if(request('date_to'))
                        <span class="rounded-full bg-[#29ABE2]/10 border border-[#29ABE2]/30 px-3 py-0.5 text-xs text-[#29ABE2]">
                            To: {{ request('date_to') }}
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
    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($events as $event)
            <x-event-card :event="$event" :show-register="true" />
        @empty
            <div class="sm:col-span-2 lg:col-span-3">
                <div class="flex flex-col items-center gap-3 rounded-2xl border border-white/10 py-16 text-center"
                     style="background-color: #1a1a2e;">
                    <span class="text-5xl">🔍</span>
                    <p class="font-semibold text-white">No events match your filters</p>
                    <p class="text-sm text-slate-400">Try adjusting your search or category.</p>
                    <a href="{{ route('events.index') }}"
                       class="mt-2 inline-flex rounded-xl px-5 py-2.5 text-sm font-semibold text-white"
                       style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        Clear filters
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>

@endsection