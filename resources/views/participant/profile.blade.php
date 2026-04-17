@extends('layouts.participant')

@section('title', 'My Profile')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">My Profile</h1>
    <p class="mt-1 text-slate-600">Keep your participant details up to date.</p>

    <form method="POST" action="#" class="mt-8 max-w-xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700">Full name</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2">
        </div>
        <button type="button" class="btn-gradient text-sm">Save (placeholder)</button>
    </form>
@endsection
