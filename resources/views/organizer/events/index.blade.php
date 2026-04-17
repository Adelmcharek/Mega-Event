@extends('layouts.organizer')

@section('title', 'My Events')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My Events</h1>
            <p class="text-slate-600">Manage drafts, submissions, and published events.</p>
        </div>
        <a href="{{ route('organizer.events.create') }}" class="btn-gradient text-sm">+ New event</a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($events ?? [] as $event)
                        @php $e = is_object($event) ? $event : (object) $event; @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $e->title }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($e->date)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $e->location }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $e->capacity }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $e->registered ?? 0 }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$e->status" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('organizer.events.edit', $e->id) }}" class="font-semibold text-brand-500 hover:text-brand-600">Edit</a>
                                <span class="mx-2 text-slate-300">|</span>
                                <button type="button" class="font-semibold text-red-600 hover:text-red-700" @click="$dispatch('open-modal', 'delete-event-{{ $e->id }}')">Delete</button>
                                <span class="mx-2 text-slate-300">|</span>
                                <a href="{{ route('organizer.events.participants', $e->id) }}" class="font-semibold text-slate-600 hover:text-slate-900">Participants</a>
                            </td>
                        </tr>

                        <x-confirm-modal id="delete-event-{{ $e->id }}" title="Delete this event?">
                            This action cannot be undone. Demo only — hook your destroy route here.
                            <x-slot name="confirm">
                                <form method="POST" action="{{ route('organizer.events.destroy', $e->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
                                </form>
                            </x-slot>
                        </x-confirm-modal>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12">
                                <x-empty-state title="No events yet" message="Start by creating an event and submit it for review.">
                                    <x-slot name="action">
                                        <a href="{{ route('organizer.events.create') }}" class="btn-gradient text-sm">Create event</a>
                                    </x-slot>
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
