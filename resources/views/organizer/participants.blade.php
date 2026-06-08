@extends('layouts.organizer')

@section('title', __('app.participants'))

@section('content')
    <h1 class="text-2xl font-bold text-white">{{ __('app.participants') }}</h1>
    <p class="mt-2 text-slate-400">{{ __('app.participants_subtitle') }}</p>

    <div class="mt-8 space-y-6">
        @forelse($events as $event)
            <div class="rounded-2xl border border-white/10 p-6" style="background-color: #1a1a2e;">
                {{-- En-tête de l'événement --}}
                <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $event->title }}</h3>
                        <p class="text-sm text-slate-400">
                            📅 {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                            · 📍 {{ $event->location }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold"
                          style="background: #29ABE220; color: #29ABE2;">
                        {{ $event->registrations->count() }} / {{ $event->capacity }}
                        {{ __('app.participants') }}
                    </span>
                </div>

                {{-- Tableau des participants --}}
                @if($event->registrations->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-400 border-b border-white/10">
                                    <th class="py-2 px-3">{{ __('app.participant_name') }}</th>
                                    <th class="py-2 px-3">{{ __('app.participant_email') }}</th>
                                    <th class="py-2 px-3">{{ __('app.phone') }}</th>
                                    <th class="py-2 px-3">{{ __('app.registered_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($event->registrations as $registration)
                                    <tr class="border-b border-white/5 text-slate-300 hover:bg-white/5 transition-colors">
                                        <td class="py-2 px-3">{{ $registration->user->name }}</td>
                                        <td class="py-2 px-3">{{ $registration->user->email }}</td>
                                        <td class="py-2 px-3">{{ $registration->user->phone ?? '—' }}</td>
                                        <td class="py-2 px-3">
                                            {{ \Carbon\Carbon::parse($registration->registered_at)->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-slate-400 text-sm">{{ __('app.no_participants') }}</p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-white/10 p-8 text-center" style="background-color: #1a1a2e;">
                <span class="text-5xl">👥</span>
                <p class="mt-3 text-slate-400">{{ __('app.no_events_yet') }}</p>
            </div>
        @endforelse
    </div>
@endsection
