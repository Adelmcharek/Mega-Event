@extends('layouts.participant')

@section('title', __('app.dashboard'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('app.welcome_back') }}, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-400">{{ __('app.my_upcoming_events') }}</p>
        </div>
        <a href="{{ route('events.index') }}"
           class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
            {{ __('app.browse_events') }}
        </a>
    </div>

    {{-- Upcoming Events --}}
    <section class="mb-12">
        <h2 class="text-lg font-bold text-white mb-6">{{ __('app.my_upcoming_events') }}</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($upcomingRegistrations ?? [] as $reg)
                <x-event-card :event="$reg->event" />
            @empty
                <div class="sm:col-span-2 lg:col-span-3">
                    <div class="flex flex-col items-center gap-3 rounded-2xl border border-white/10 py-16 text-center"
                         style="background-color: #1a1a2e;">
                        <span class="text-5xl">📭</span>
                        <p class="font-semibold text-white">{{ __('app.no_registrations') }}</p>
                        <p class="text-sm text-slate-400">{{ __('app.upcoming_events') }}</p>
                        <a href="{{ route('events.index') }}"
                           class="mt-2 inline-flex rounded-xl px-5 py-2.5 text-sm font-semibold text-white"
                           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                            {{ __('app.browse_events') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Recommended --}}
    <div class="mt-6 rounded-2xl border border-white/10 p-6"
         style="background-color: #1a1a2e;">

      <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
        🤖 {{ __('app.recommendations') }}
        <span class="text-xs font-normal text-slate-400 ml-2">
          {{ __('app.rec_powered_by') }}
        </span>
      </h2>

      @if(!isset($recommendedEvents) || $recommendedEvents->isEmpty())
        <p class="text-slate-400 text-sm">
          {{ __('app.no_recommendations') }}
        </p>
      @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @foreach($recommendedEvents as $event)
            <a href="{{ route('events.show', $event) }}"
               class="block rounded-xl border border-white/10 p-4
                      hover:border-[#29ABE2]/40 hover:bg-white/5 transition-all">

              {{-- Category badge --}}
              <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold mb-2"
                    style="background: #29ABE220; color: #29ABE2;">
                {{ ucfirst($event->category) }}
              </span>

              {{-- Title --}}
              <h3 class="text-white font-semibold text-sm mb-1">
                {{ $event->title }}
              </h3>

              {{-- Date & Location --}}
              <p class="text-slate-400 text-xs mb-1">
                📅 {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
              </p>
              <p class="text-slate-400 text-xs mb-3">
                📍 {{ $event->location }}
              </p>

              {{-- Reason badge --}}
              <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">
                  @if($event->recommendation_reason === 'collaborative_filtering')
                    🤖 {{ __('app.rec_similar_users') }}
                  @else
                    🔥 {{ __('app.rec_popular') }}
                  @endif
                </span>
                @if($event->price > 0)
                  <span class="text-xs font-bold text-[#29ABE2]">
                    {{ $event->price }} TND
                  </span>
                @else
                  <span class="text-xs font-bold text-emerald-400">{{ __('app.event_free') }}</span>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Registration History --}}
    <section>
        <h2 class="text-lg font-bold text-white mb-4">{{ __('app.my_registrations') }}</h2>
        <div class="overflow-x-auto rounded-2xl border border-white/10 shadow-sm"
             style="background-color: #1a1a2e;">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('app.event') }}</th>
                        <th class="px-6 py-3">{{ __('app.date') }}</th>
                        <th class="px-6 py-3">{{ __('app.status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('app.actions') }}</th>
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
                                        {{ __('app.cancel_registration') }}
                                    </button>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                {{ __('app.no_registrations') }}
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
            <x-confirm-modal id="cancel-reg-{{ $reg->id }}" :title="__('app.cancel_registration')">
                {{ __('app.cannot_undo') }}
                <strong class="text-white">{{ optional($reg->event)->title ?? 'this event' }}</strong>
                <x-slot name="confirm">
                    <form method="POST" action="{{ route('registrations.cancel', $reg->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            {{ __('app.confirm') }}
                        </button>
                    </form>
                </x-slot>
            </x-confirm-modal>
        @endif
    @endforeach
@endsection
