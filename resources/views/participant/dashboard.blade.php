@extends('layouts.participant')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Hello, {{ auth()->user()->name }}</h1>
            <p class="text-slate-600">Your upcoming events and registration history.</p>
        </div>
        <a href="{{ route('events.index') }}" class="btn-gradient text-sm">Explore Events</a>
    </div>

    <section class="mb-12">
        <h2 class="text-lg font-bold text-slate-900">My Upcoming Events</h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($upcoming ?? [] as $ev)
                @php $e = is_object($ev) ? $ev : (object) $ev; @endphp
                <x-event-card :event="$e" />
            @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <x-empty-state title="No upcoming events" message="Browse the catalog and register for your next experience.">
                        <x-slot name="action">
                            <a href="{{ route('events.index') }}" class="btn-gradient text-sm">Browse events</a>
                        </x-slot>
                    </x-empty-state>
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="text-lg font-bold text-slate-900">Registration history</h2>
        <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($history ?? [] as $h)
                        @php $row = is_object($h) ? $h : (object) $h; @endphp
                        @php
                            $eventDate = isset($row->event_date) ? \Carbon\Carbon::parse($row->event_date) : null;
                            $isFuture = $eventDate && $eventDate->isFuture();
                        @endphp
                        <tr>
                            <td class="px-6 py-3 font-medium text-slate-900">{{ $row->event_title ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $eventDate ? $eventDate->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-3"><x-status-badge :status="$row->status ?? 'CONFIRMED'" /></td>
                            <td class="px-6 py-3 text-right">
                                @if ($isFuture)
                                    <button type="button" class="font-semibold text-red-600 hover:text-red-700" @click="$dispatch('open-modal', 'cancel-reg-{{ $loop->index }}')">Cancel</button>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">No history yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @foreach ($history ?? [] as $i => $h)
        @php $row = is_object($h) ? $h : (object) $h; @endphp
        @php $eventDate = isset($row->event_date) ? \Carbon\Carbon::parse($row->event_date) : null; @endphp
        @if ($eventDate && $eventDate->isFuture())
            <x-confirm-modal id="cancel-reg-{{ $i }}" title="Cancel registration?">
                Cancel your spot for {{ $row->event_title ?? 'this event' }}?
                <x-slot name="confirm">
                    <form method="POST" action="#">
                        @csrf
                        <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Cancel registration</button>
                    </form>
                </x-slot>
            </x-confirm-modal>
        @endif
    @endforeach
@endsection
