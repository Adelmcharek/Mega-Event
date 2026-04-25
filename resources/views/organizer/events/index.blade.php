@extends('layouts.organizer')

@section('title', __('app.my_events'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('app.my_events') }}</h1>
            <p class="text-slate-400">{{ __('app.manage_events_subtitle') }}</p>
        </div>
        <a href="{{ route('organizer.events.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
            {{ __('app.new_event') }}
        </a>
    </div>

    <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-4 py-3">{{ __('app.title') }}</th>
                        <th class="px-4 py-3">{{ __('app.date') }}</th>
                        <th class="px-4 py-3">{{ __('app.location') }}</th>
                        <th class="px-4 py-3">{{ __('app.capacity') }}</th>
                        <th class="px-4 py-3">{{ __('app.registered') }}</th>
                        <th class="px-4 py-3">{{ __('app.status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('app.actions') }}</th>
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
                                    {{ __('app.edit') }}
                                </a>
                                <button type="button"
                                        class="ml-1 rounded-lg bg-red-500/20 px-3 py-1 text-xs font-semibold text-red-400 border border-red-500/30 hover:bg-red-500/30 transition-all"
                                        @click="$dispatch('open-modal', 'delete-event-{{ $e->id }}')">
                                    {{ __('app.delete') }}
                                </button>
                                <a href="{{ route('organizer.events.participants', $e->id) }}"
                                   class="ml-1 rounded-lg bg-white/10 px-3 py-1 text-xs font-semibold text-slate-300 border border-white/10 hover:bg-white/20 transition-all">
                                    {{ __('app.participants') }}
                                </a>
                            </td>
                        </tr>

                        <x-confirm-modal id="delete-event-{{ $e->id }}" :title="__('app.delete') . ' ' . __('app.event') . '?'">
                            {{ __('app.cannot_undo') }}
                            <x-slot name="confirm">
                                <form method="POST" action="{{ route('organizer.events.destroy', $e->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                        {{ __('app.delete') }}
                                    </button>
                                </form>
                            </x-slot>
                        </x-confirm-modal>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">📭</span>
                                    <p class="font-semibold text-white">{{ __('app.no_events_yet') }}</p>
                                    <p class="text-slate-400 text-sm">{{ __('app.create_first_submit') }}</p>
                                    <a href="{{ route('organizer.events.create') }}"
                                       class="mt-2 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white"
                                       style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                        + {{ __('app.create_event') }}
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
