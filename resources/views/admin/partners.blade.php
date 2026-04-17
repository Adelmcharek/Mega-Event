@extends('layouts.admin')

@section('title', 'Partners')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Partners</h1>
    <p class="mt-2 text-slate-600">Platform-wide partner directory (placeholder).</p>
    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <x-empty-state title="Partner management" message="Sponsors and media partners will appear here for admin assignment to events." />
    </div>
@endsection
