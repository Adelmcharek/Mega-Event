@extends('layouts.organizer')

@section('title', 'My Events')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">My Events</h1>
            <p class="text-slate-400">Manage drafts, submissions, and published events.</p>
        </div>
        <a href="{{ route('organizer.events.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
            + New Event
        </a>
    </div>

    <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Capacity</th>
                        <th class="px-4 py-3">Registered</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($events ?? [] as $event)
                        @php $e = is_object($event) ? $event : (object) $event; @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 font-medium text-white">{{ $e->title }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ \Carbon\Carbon::parse($e->date)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $e->location }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $e->capacity }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $e->registrations_count ?? 0 }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$e->status" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('organizer.events.edit', $e->id) }}"
                                   class="rounded-lg bg-[#29ABE2]/20 px-3 py-1 text-xs font-semibold text-[#29ABE2] border border-[#29ABE2]/30 hover:bg-[#29ABE2]/30 transition-all">
                                    Edit
                                </a>
                                <button type="button"
                                        class="ml-1 rounded-lg bg-red-500/20 px-3 py-1 text-xs font-semibold text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all"
                                        @click="$dispatch('open-modal', 'delete-event-{{ $e->id }}')">
                                    Delete
                                </button>
                                <a href="{{ route('organizer.events.participants', $e->id) }}"
                                   class="ml-1 rounded-lg bg-white/10 px-3 py-1 text-xs font-semibold text-slate-300 border border-white/10 hover:bg-white/20 transition-all">
                                    Participants
                                </a>
                            </td>
                        </tr>

                        <x-confirm-modal id="delete-event-{{ $e->id }}" title="Delete this event?">
                            This action cannot be undone.
                            <x-slot name="confirm">
                                <form method="POST" action="{{ route('organizer.events.destroy', $e->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </x-slot>
                        </x-confirm-modal>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">📭</span>
                                    <p class="font-semibold text-white">No events yet</p>
                                    <p class="text-slate-400 text-sm">Start by creating an event and submit it for review.</p>
                                    <a href="{{ route('organizer.events.create') }}"
                                       class="mt-2 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white"
                                       style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                        + Create Event
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
