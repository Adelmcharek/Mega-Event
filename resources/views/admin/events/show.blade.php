@extends('layouts.admin')

@section('title', __('app.review'))

@section('content')
    <a href="{{ route('admin.events.index') }}"
       class="text-sm font-semibold text-[#29ABE2] hover:text-[#4CBDE8]">← {{ __('app.all_events') }}</a>

    <div class="mt-6 grid gap-8 lg:grid-cols-10">
        {{-- Main info --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm">
                @if ($event->cover_image)
                    <img src="{{ asset(\Illuminate\Support\Facades\Storage::url($event->cover_image)) }}"
                         alt="" class="h-48 w-full object-cover">
                @else
                    <div class="h-48 bg-gradient-to-br from-[#29ABE2] to-[#0A7FA8]"></div>
                @endif
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-white">{{ $event->title }}</h1>
                    <p class="mt-2 text-slate-400">
                        {{ $event->date->format('D, M d Y H:i') }} · {{ $event->location }}
                    </p>
                    <p class="mt-4 text-slate-300">{{ $event->description }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <x-status-badge :status="$event->status" />
                        <x-status-badge :status="$event->category" />
                    </div>
                    @if ($event->rejection_reason)
                        <div class="mt-4 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                            {{ __('app.status_rejected') }}: {{ $event->rejection_reason }}
                        </div>
                    @endif
                    <p class="mt-4 text-sm text-slate-500">
                        {{ __('app.organizer_label') }}: <span class="text-slate-300">{{ optional($event->organiser)->name ?? '—' }}</span>
                        · {{ __('app.event_capacity') }}: <span class="text-slate-300">{{ $event->capacity }}</span>
                        · {{ __('app.registered') }}: <span class="text-[#29ABE2]">{{ $event->registrations->count() }}</span>
                    </p>
                </div>
            </div>

            {{-- Partners --}}
            @if($event->partners->isNotEmpty())
                <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm overflow-hidden">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-lg font-bold text-white">{{ __('app.event_partners') }}</h2>
                    </div>
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                            <tr>
                                <th class="px-6 py-3">{{ __('app.name') }}</th>
                                <th class="px-6 py-3">{{ __('app.website') }}</th>
                                <th class="px-6 py-3">{{ __('app.email') }}</th>
                                <th class="px-6 py-3">{{ __('app.phone') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($event->partners as $partner)
                                <tr class="hover:bg-white/5">
                                    <td class="px-6 py-3 font-semibold text-white">{{ $partner->name }}</td>
                                    <td class="px-6 py-3 text-slate-400">
                                        @if($partner->website)
                                            <a href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer" class="text-[#29ABE2] hover:underline">{{ $partner->website }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-slate-400">{{ $partner->email ?? '—' }}</td>
                                    <td class="px-6 py-3 text-slate-400">{{ $partner->phone ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Registered participants --}}
            @if ($event->registrations->isNotEmpty())
                <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm overflow-hidden">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-lg font-bold text-white">{{ __('app.participants') }}</h2>
                    </div>
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                            <tr>
                                <th class="px-6 py-3">{{ __('app.name') }}</th>
                                <th class="px-6 py-3">{{ __('app.email') }}</th>
                                <th class="px-6 py-3">{{ __('app.status') }}</th>
                                <th class="px-6 py-3">{{ __('app.joined') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($event->registrations as $reg)
                                <tr class="hover:bg-white/5">
                                    <td class="px-6 py-3 text-white">{{ optional($reg->user)->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-slate-400">{{ optional($reg->user)->email ?? '—' }}</td>
                                    <td class="px-6 py-3"><x-status-badge :status="$reg->status" /></td>
                                    <td class="px-6 py-3 text-slate-400">
                                        {{ $reg->registered_at ? $reg->registered_at->format('M d, Y') : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Admin actions --}}
        <div class="lg:col-span-3">
            <div class="sticky top-24 space-y-4 rounded-2xl border border-white/10 bg-[#1a1a2e] p-6 shadow-lg">
                <h2 class="text-lg font-bold text-white">{{ __('app.actions') }}</h2>

                @if ($event->status !== 'published')
                    <form method="POST" action="{{ route('admin.events.approve', $event->id) }}">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-xl py-3 text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                                style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                            {{ __('app.approve') }}
                        </button>
                    </form>
                @endif

                @if ($event->status !== 'rejected')
                    <form method="POST" action="{{ route('admin.events.reject', $event->id) }}" class="space-y-3">
                        @csrf
                        <label class="block text-sm font-medium text-slate-300">{{ __('app.status_rejected') }}</label>
                        <textarea name="reason" rows="3"
                                  class="w-full rounded-xl border border-white/10 bg-[#0A0A0A] px-3 py-2 text-sm text-white placeholder-slate-500"
                                  placeholder="{{ __('app.approval_warning') }}">{{ old('reason') }}</textarea>
                        <button type="submit"
                                class="w-full rounded-xl border border-red-500/30 bg-red-500/10 py-2.5 text-sm font-semibold text-red-400 hover:bg-red-500/20">
                            {{ __('app.reject') }}
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}"
                      onsubmit="return confirm('{{ __('app.cannot_undo') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full rounded-xl border border-white/10 py-2.5 text-sm font-semibold text-slate-400 hover:bg-white/5">
                        {{ __('app.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
