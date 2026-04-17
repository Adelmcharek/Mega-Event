@extends('layouts.organizer')

@section('title', 'Participants')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Participants</h1>
    <p class="mt-2 text-slate-600">Overview of participants across all your events.</p>
    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <x-empty-state title="Cross-event participants" message="Open an event and use View Participants for detailed lists per event.">
            <x-slot name="action">
                <a href="{{ route('organizer.events.index') }}" class="btn-gradient text-sm">Go to My Events</a>
            </x-slot>
        </x-empty-state>
    </div>
@endsection
