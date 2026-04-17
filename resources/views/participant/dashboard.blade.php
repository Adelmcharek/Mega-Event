@extends('layouts.participant')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Hello, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-400">Your upcoming events and registration history.</p>
        </div>
        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
            Explore Events
        </a>
    </div>

    {{-- Upcoming Events --}}
    <section class="mb-12">
        <h2 class="text-lg font-bold text-white mb-6">My Upcoming Events</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($upcoming ?? [] as $ev)
                <x-event-card :event="$ev" />
            @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <div class="flex flex-col items-center gap-3 rounded-2xl border border-white/10 py-16 text-center"
                         style="background-color: #1a1a2e;">
                        <span class="text-5xl">📭</span>
                        <p class="font-semibold text-white">No upcoming events</p>
                        <p class="text-sm text-slate-400">Browse the catalog and register for your next experience.</p>
                        <a href="{{ route('events.index') }}"
                           class="mt-2 inline-flex rounded-xl px-5 py-2.5 text-sm font-semibold text-white"
                           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                            Browse Events
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Recommended --}}
    @if(isset($recommended) && $recommended->isNotEmpty())
        <section class="mb-12">
            <h2 class="text-lg font-bold text-white mb-2">Recommended for you</h2>
            <p class="text-sm text-slate-400 mb-6">Based on your registration history.</p>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($recommended as $ev)
                    <div class="relative">
                        <span class="absolute left-3 top-3 z-10 rounded-full bg-[#29ABE2] px-3 py-1 text-xs font-bold text-white shadow">
                            ✦ Recommended
                        </span>
                        <x-event-card :event="$ev" :show-register="true" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Registration History --}}
    <section>
        <h2 class="text-lg font-bold text-white mb-4">Registration history</h2>
        <div class="overflow-x-auto rounded-2xl border border-white/10 shadow-sm"
             style="background-color: #1a1a2e;">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($history ?? [] as $i => $reg)
                        @php
                            $eventDate = optional($reg->event)->date;
                            $isFuture  = $eventDate && \Carbon\Carbon::parse($eventDate)->isFuture();
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-3 font-medium text-white">
                                {{ optional($reg->event)->title ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-slate-400">
                                {{ $eventDate ? \Carbon\Carbon::parse($eventDate)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-3">
                                <x-status-badge :status="$reg->status ?? 'confirmed'" />
                            </td>
                            <td class="px-6 py-3 text-right">
                                @if ($isFuture && $reg->status !== 'cancelled')
                                    <button type="button"
                                            class="rounded-lg bg-red-500/20 px-3 py-1 text-xs font-semibold text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all"
                                            @click="$dispatch('open-modal', 'cancel-reg-{{ $reg->id }}')">
                                        Cancel
                                    </button>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                No registration history yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Cancel Modals --}}
    @foreach ($history ?? [] as $reg)
        @php
            $eventDate = optional($reg->event)->date;
        @endphp
        @if ($eventDate && \Carbon\Carbon::parse($eventDate)->isFuture() && $reg->status !== 'cancelled')
            <x-confirm-modal id="cancel-reg-{{ $reg->id }}" title="Cancel registration?">
                Cancel your spot for
                <strong class="text-white">{{ optional($reg->event)->title ?? 'this event' }}</strong>?
                <x-slot name="confirm">
                    <form method="POST" action="{{ route('registrations.cancel', $reg->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Cancel registration
                        </button>
                    </form>
                </x-slot>
            </x-confirm-modal>
        @endif
    @endforeach
@endsection
