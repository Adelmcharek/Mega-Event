@extends('layouts.participant')

@section('title', 'My Registrations')

@section('content')
    <h1 class="text-2xl font-bold text-white">My Registrations</h1>
    <p class="mt-1 text-slate-400">All events you have signed up for.</p>

    <div class="mt-8 overflow-x-auto rounded-2xl border border-white/10 shadow-sm"
         style="background-color: #1a1a2e;">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                <tr>
                    <th class="px-6 py-3">Event</th>
                    <th class="px-6 py-3">Event date</th>
                    <th class="px-6 py-3">Registered on</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($registrations ?? [] as $reg)
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
                        <td class="px-6 py-3 text-slate-400">
                            {{ $reg->registered_at ? $reg->registered_at->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-3">
                            <x-status-badge :status="$reg->status ?? 'confirmed'" />
                        </td>
                        <td class="px-6 py-3 text-right">
                            @if ($isFuture && $reg->status !== 'cancelled')
                                <button type="button"
                                        class="rounded-lg bg-red-500/20 px-3 py-1 text-xs font-semibold text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all"
                                        @click="$dispatch('open-modal', 'cancel-{{ $reg->id }}')">
                                    Cancel
                                </button>
                            @else
                                <span class="text-slate-600">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="text-5xl">📋</span>
                                <p class="font-semibold text-white">No registrations yet</p>
                                <p class="text-sm text-slate-400 max-w-sm">
                                    Discover conferences, culture nights, and corporate meetups.
                                </p>
                                <a href="{{ route('events.index') }}"
                                   class="mt-2 inline-flex rounded-xl px-5 py-2.5 text-sm font-semibold text-white"
                                   style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                    Browse Events →
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Cancel Modals --}}
    @foreach ($registrations ?? [] as $reg)
        @php
            $eventDate = optional($reg->event)->date;
        @endphp
        @if ($eventDate && \Carbon\Carbon::parse($eventDate)->isFuture() && $reg->status !== 'cancelled')
            <x-confirm-modal id="cancel-{{ $reg->id }}" title="Cancel registration?">
                This will free your spot for
                <strong class="text-white">{{ optional($reg->event)->title ?? 'this event' }}</strong>.
                <x-slot name="confirm">
                    <form method="POST" action="{{ route('registrations.cancel', $reg->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Confirm cancel
                        </button>
                    </form>
                </x-slot>
            </x-confirm-modal>
        @endif
    @endforeach
@endsection
