@extends('layouts.organizer')

@section('title', 'Participants')

@section('content')
    <h1 class="text-2xl font-bold text-white">Participants</h1>
    <p class="mt-2 text-slate-400">Overview of participants across all your events.</p>

    <div class="mt-8 rounded-2xl border border-white/10 p-8 shadow-sm" style="background-color: #1a1a2e;">
        <div class="flex flex-col items-center gap-3 py-12 text-center">
            <span class="text-5xl">👥</span>
            <p class="font-semibold text-white">Cross-event participants</p>
            <p class="text-slate-400 text-sm max-w-sm">
                Open an event and use View Participants for detailed lists per event.
            </p>
            <a href="{{ route('organizer.events.index') }}"
               class="mt-3 inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
               style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                Go to My Events
            </a>
        </div>
    </div>
@endsection
