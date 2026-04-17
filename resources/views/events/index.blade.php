@extends('layouts.public')

@section('title', 'Upcoming Events')

@section('content')
    <div class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-900">Upcoming Events</h1>
            <p class="mt-2 text-slate-600">Browse professional, cultural, and corporate experiences.</p>

            <form method="GET" action="{{ route('events.index') }}" class="mt-8 space-y-4" x-data="{ cat: '{{ request('category', 'all') }}' }">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">🔍</span>
                            <input
                                id="search"
                                name="search"
                                type="search"
                                value="{{ request('search') }}"
                                placeholder="Search events..."
                                class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-3 text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                            >
                        </div>
                    </div>
                    <div class="w-full lg:w-48">
                        <label for="date" class="block text-xs font-medium text-slate-500">From date</label>
                        <input
                            id="date"
                            name="date"
                            type="date"
                            value="{{ request('date') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                        >
                    </div>
                    <div class="w-full lg:w-56">
                        <label for="location" class="block text-xs font-medium text-slate-500">Location</label>
                        <input
                            id="location"
                            name="location"
                            type="text"
                            value="{{ request('location') }}"
                            placeholder="City or venue"
                            class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                        >
                    </div>
                    <button type="submit" class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Apply</button>
                </div>
                <input type="hidden" name="category" x-model="cat">
                <div class="flex flex-wrap gap-2">
                    @foreach (['all' => 'All', 'Professional' => 'Professional', 'Cultural' => 'Cultural', 'Corporate' => 'Corporate'] as $val => $label)
                        <button
                            type="button"
                            @click="cat = '{{ $val === 'all' ? 'all' : $val }}'; $el.closest('form').submit()"
                            class="rounded-full px-4 py-1.5 text-sm font-semibold transition"
                            :class="cat === '{{ $val === 'all' ? 'all' : $val }}' ? 'bg-gradient-to-r from-purple-600 to-blue-500 text-white shadow' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($events as $event)
                <x-event-card :event="$event" :show-register="true" />
            @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <x-empty-state title="No events match your filters" message="Try adjusting search or category.">
                        <x-slot name="action">
                            <a href="{{ route('events.index') }}" class="btn-gradient text-sm">Clear filters</a>
                        </x-slot>
                    </x-empty-state>
                </div>
            @endforelse
        </div>
    </div>
@endsection
