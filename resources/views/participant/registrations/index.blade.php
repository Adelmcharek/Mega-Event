@extends('layouts.participant')

@section('title', 'My Registrations')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">My Registrations</h1>
    <p class="mt-1 text-slate-600">All events you have signed up for.</p>

    <div class="mt-8 overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-3">Event</th>
                    <th class="px-6 py-3">Event date</th>
                    <th class="px-6 py-3">Registration date</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($registrations ?? [] as $i => $r)
                    @php $row = is_object($r) ? $r : (object) $r; @endphp
                    @php
                        $eventDate = isset($row->event_date) ? \Carbon\Carbon::parse($row->event_date) : null;
                        $isFuture = $eventDate && $eventDate->isFuture();
                    @endphp
                    <tr>
                        <td class="px-6 py-3 font-medium text-slate-900">{{ $row->event_title ?? '—' }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $eventDate ? $eventDate->format('M d, Y') : '—' }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ isset($row->registered_at) ? \Carbon\Carbon::parse($row->registered_at)->format('M d, Y') : '—' }}</td>
                        <td class="px-6 py-3"><x-status-badge :status="$row->status ?? 'CONFIRMED'" /></td>
                        <td class="px-6 py-3 text-right">
                            @if ($isFuture)
                                <button type="button" class="font-semibold text-red-600 hover:text-red-700" @click="$dispatch('open-modal', 'cancel-{{ $i }}')">Cancel</button>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16">
                            <x-empty-state
                                title="You haven't registered for any events yet."
                                message="Discover conferences, culture nights, and corporate meetups."
                            >
                                <x-slot name="action">
                                    <a href="{{ route('events.index') }}" class="btn-gradient text-sm">Browse events →</a>
                                </x-slot>
                            </x-empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach ($registrations ?? [] as $i => $r)
        @php $row = is_object($r) ? $r : (object) $r; @endphp
        @php $eventDate = isset($row->event_date) ? \Carbon\Carbon::parse($row->event_date) : null; @endphp
        @if ($eventDate && $eventDate->isFuture())
            <x-confirm-modal id="cancel-{{ $i }}" title="Cancel registration?">
                This will free your spot for {{ $row->event_title ?? 'this event' }}.
                <x-slot name="confirm">
                    <form method="POST" action="#">
                        @csrf
                        <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Confirm cancel</button>
                    </form>
                </x-slot>
            </x-confirm-modal>
        @endif
    @endforeach
@endsection
