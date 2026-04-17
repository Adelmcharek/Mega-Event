@extends('layouts.organizer')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Welcome back, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-400">Here is what is happening with your events.</p>
        </div>
        <a href="{{ route('organizer.events.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
            <span>+</span> Create New Event
        </a>
    </div>

    {{-- Stats Cards --}}
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

    {{-- Recent Events Table --}}
    <div class="mt-10 rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-lg font-bold text-white">Recent events</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Participants</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recentEvents ?? [] as $row)
                        @php $ev = is_object($row) ? $row : (object) $row; @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $ev->title ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ isset($ev->date) ? \Carbon\Carbon::parse($ev->date)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$ev->status ?? 'DRAFT'" />
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $ev->registrations_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('organizer.events.edit', $ev->id ?? 1) }}"
                                   class="rounded-lg bg-[#29ABE2]/20 px-3 py-1 text-xs font-semibold text-[#29ABE2] border border-[#29ABE2]/30 hover:bg-[#29ABE2]/30 transition-all">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">📭</span>
                                    <p class="font-semibold text-white">No events yet</p>
                                    <p class="text-slate-400 text-sm">Create your first event to see it here.</p>
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