@extends('layouts.organizer')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Welcome back, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-600">Here is what is happening with your events.</p>
        </div>
        <a href="{{ route('organizer.events.create') }}" class="btn-gradient inline-flex items-center gap-2 px-5 py-2.5 text-sm">
            <span>+</span> Create New Event
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stats-card label="Total events created" :value="$stats['total_events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">📅</span></x-slot>
        </x-stats-card>
        <x-stats-card label="Total participants" :value="$stats['total_participants'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">👥</span></x-slot>
        </x-stats-card>
        <x-stats-card label="Pending approval" :value="$stats['pending_events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">⏳</span></x-slot>
        </x-stats-card>
        <x-stats-card label="Published events" :value="$stats['published_events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">✅</span></x-slot>
        </x-stats-card>
    </div>

    <div class="mt-10 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Recent events</h2>
        </div>
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Participants</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentEvents ?? [] as $row)
                        @php $ev = is_object($row) ? $row : (object) $row; @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $ev->title ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ isset($ev->date) ? \Carbon\Carbon::parse($ev->date)->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-4"><x-status-badge :status="$ev->status ?? 'DRAFT'" /></td>
                            <td class="px-6 py-4 text-slate-600">{{ $ev->participants ?? $ev->registered ?? 0 }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('organizer.events.edit', $ev->id ?? 1) }}" class="font-semibold text-brand-500 hover:text-brand-600">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12">
                                <x-empty-state title="No events yet" message="Create your first event to see it here.">
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
