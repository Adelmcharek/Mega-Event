@extends('layouts.organizer')

@section('title', 'Partners')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Partners</h1>
    <p class="mt-2 text-slate-600">Associate sponsors and partners with your events (placeholder).</p>
    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <x-empty-state title="Partner CRM coming soon" message="You will manage partner logos, tiers, and event sponsorships here." />
    </div>
@endsection
