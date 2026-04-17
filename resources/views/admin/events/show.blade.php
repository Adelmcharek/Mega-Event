@extends('layouts.admin')

@section('title', 'Review Event')

@section('content')
    <a href="{{ route('admin.events.index') }}"
       class="text-sm font-semibold text-[#29ABE2] hover:text-[#4CBDE8]">← All events</a>

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
                            Rejection reason: {{ $event->rejection_reason }}
                        </div>
                    @endif
                    <p class="mt-4 text-sm text-slate-500">
                        Organizer: <span class="text-slate-300">{{ optional($event->organiser)->name ?? '—' }}</span>
                        · Capacity: <span class="text-slate-300">{{ $event->capacity }}</span>
                        · Registered: <span class="text-[#29ABE2]">{{ $event->registrations->count() }}</span>
                    </p>
                </div>
            </div>

            {{-- Registered participants --}}
            @if ($event->registrations->isNotEmpty())
                <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm overflow-hidden">
                    <div class="border-b border-white/10 px-6 py-4">
                        <h2 class="text-lg font-bold text-white">Registered participants</h2>
                    </div>
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                            <tr>
                                <th class="px-6 py-3">Name</th>
                                <th class="px-6 py-3">Email</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Registered</th>
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
                <h2 class="text-lg font-bold text-white">Admin actions</h2>

                @if ($event->status !== 'published')
                    <form method="POST" action="{{ route('admin.events.approve', $event->id) }}">
                        @csrf
                        <button type="submit"
                                class="w-full rounded-xl py-3 text-sm font-semibold text-white transition-all hover:scale-[1.02]"
                                style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                            Approve event
                        </button>
                    </form>
                @endif

                @if ($event->status !== 'rejected')
                    <form method="POST" action="{{ route('admin.events.reject', $event->id) }}" class="space-y-3">
                        @csrf
                        <label class="block text-sm font-medium text-slate-300">Rejection reason</label>
                        <textarea name="reason" rows="3"
                                  class="w-full rounded-xl border border-white/10 bg-[#0A0A0A] px-3 py-2 text-sm text-white placeholder-slate-500"
                                  placeholder="Explain why this event is rejected...">{{ old('reason') }}</textarea>
                        <button type="submit"
                                class="w-full rounded-xl border border-red-500/30 bg-red-500/10 py-2.5 text-sm font-semibold text-red-400 hover:bg-red-500/20">
                            Reject
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}"
                      onsubmit="return confirm('Permanently delete this event?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full rounded-xl border border-white/10 py-2.5 text-sm font-semibold text-slate-400 hover:bg-white/5">
                        Delete event
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
