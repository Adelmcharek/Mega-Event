@extends('layouts.organizer')

@section('title', 'Participants')

@section('content')
    @php $name = $eventName ?? ($event->title ?? 'Event'); @endphp
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $name }}</h1>
            <p class="text-slate-400">Registered participants for this event.</p>
        </div>
        <a href="{{ route('organizer.events.index') }}"
           class="rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-slate-300 hover:bg-white/5 transition-all">
            ← Back to events
        </a>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <x-stats-card label="Total registered" :value="(string) ($stats['total'] ?? count($participants ?? []))" />
        <x-stats-card label="Spots left"       :value="(string) ($stats['spots_left'] ?? '—')" />
        <x-stats-card label="Fill rate"        :value="($stats['attendance'] ?? 0) . '%'" />
    </div>

    <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Registration date</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($participants ?? [] as $i => $p)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-white">
                                {{ optional($p->user)->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-400">
                                {{ optional($p->user)->email ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-400">
                                {{ $p->registered_at ? $p->registered_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$p->status ?? 'confirmed'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12">
                                <x-empty-state title="No participants yet"
                                               message="Share your event link to start collecting registrations." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
