@extends('layouts.organizer')

@section('title', 'Participants')

@section('content')
    @php $name = $eventName ?? ($event->title ?? 'Event'); @endphp
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $name }}</h1>
            <p class="text-slate-600">Registered participants for this event.</p>
        </div>
        <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Export CSV (placeholder)</button>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <x-stats-card label="Total registered" :value="(string) ($stats['total'] ?? count($participants ?? []))" />
        <x-stats-card label="Spots left" :value="(string) ($stats['spots_left'] ?? '—')" />
        <x-stats-card label="Attendance rate" :value="($stats['attendance'] ?? '—').(isset($stats['attendance']) && is_numeric($stats['attendance']) ? '%' : '')" />
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Registration date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($participants ?? [] as $i => $p)
                        @php $row = is_object($p) ? $p : (object) $p; @endphp
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ isset($row->registered_at) ? \Carbon\Carbon::parse($row->registered_at)->format('M d, Y') : '—' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$row->status ?? 'CONFIRMED'" /></td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" class="font-semibold text-red-600 hover:text-red-700" @click="$dispatch('open-modal', 'remove-participant-{{ $i }}')">Remove</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12">
                                <x-empty-state title="No participants yet" message="Share your event link to start collecting registrations." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($participants ?? [] as $i => $p)
        @php $row = is_object($p) ? $p : (object) $p; @endphp
        <x-confirm-modal id="remove-participant-{{ $i }}" title="Remove participant?">
            Remove {{ $row->name ?? 'this participant' }}? This is a UI placeholder.
            <x-slot name="confirm">
                <form method="POST" action="#">
                    @csrf
                    <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Remove</button>
                </form>
            </x-slot>
        </x-confirm-modal>
    @endforeach
@endsection
