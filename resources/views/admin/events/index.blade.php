@extends('layouts.admin')

@section('title', 'All Events')

@section('content')
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">All Events</h1>
            <p class="text-slate-400">Filter, approve, and manage platform events.</p>
        </div>
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="status"
                    class="rounded-xl border border-white/10 bg-[#1a1a2e] px-3 py-2 text-sm text-white">
                <option value="">All statuses</option>
                @foreach (['draft', 'pending', 'published', 'rejected'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <input type="text" name="organizer" value="{{ request('organizer') }}" placeholder="Organizer"
                   class="rounded-xl border border-white/10 bg-[#1a1a2e] px-3 py-2 text-sm text-white placeholder-slate-500">
            <input type="date" name="from" value="{{ request('from') }}"
                   class="rounded-xl border border-white/10 bg-[#1a1a2e] px-3 py-2 text-sm text-white">
            <input type="date" name="to" value="{{ request('to') }}"
                   class="rounded-xl border border-white/10 bg-[#1a1a2e] px-3 py-2 text-sm text-white">
            <button type="submit"
                    class="rounded-xl px-4 py-2 text-sm font-semibold text-white"
                    style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                Filter
            </button>
        </form>
    </div>

    <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm overflow-hidden"
         x-data="{ selected: [] }">
        <div class="flex flex-col gap-3 border-b border-white/10 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <span class="text-sm text-slate-400" x-show="selected.length" x-text="selected.length + ' selected'" x-cloak></span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-4 py-3"><span class="sr-only">Select</span></th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Organizer</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Registrations</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($events ?? [] as $event)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" :value="{{ $event->id }}" x-model.number="selected"
                                       class="rounded border-white/20">
                            </td>
                            <td class="px-4 py-3 font-medium text-white">{{ $event->title }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ optional($event->organiser)->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $event->date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-slate-400">{{ $event->registrations_count ?? 0 }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$event->status" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap text-xs font-semibold">
                                @if ($event->status === 'pending')
                                    <form method="POST" action="{{ route('admin.events.approve', $event->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-emerald-400 hover:text-emerald-300">Approve</button>
                                    </form>
                                    <span class="mx-1 text-white/20">|</span>
                                @endif
                                <a href="{{ route('admin.events.show', $event->id) }}" class="text-[#29ABE2] hover:text-[#4CBDE8]">View</a>
                                <span class="mx-1 text-white/20">|</span>
                                <button type="button" class="text-red-400 hover:text-red-300"
                                        @click="$dispatch('open-modal', 'del-{{ $event->id }}')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-slate-500">
                                No events found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($events ?? [] as $event)
        <x-confirm-modal id="del-{{ $event->id }}" title="Delete event?">
            Permanently delete <strong class="text-white">{{ $event->title }}</strong>?
            <x-slot name="confirm">
                <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </x-slot>
        </x-confirm-modal>
    @endforeach
@endsection
