@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="rounded-2xl border border-white/10 bg-white/95 p-8 shadow-2xl backdrop-blur-sm">
        <h1 class="text-center text-2xl font-bold text-slate-900">Create account</h1>
        <p class="mt-1 text-center text-sm text-slate-600">Join MEGA EVENT as a participant or organizer</p>

        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Full name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    class="mt-1 w-full rounded-xl border px-3 py-2.5 text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('name') ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-200' }}"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    class="mt-1 w-full rounded-xl border px-3 py-2.5 text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('email') ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-200' }}"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="mt-1 w-full rounded-xl border px-3 py-2.5 text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('password') ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-200' }}"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                >
            </div>
            <div>
                <span class="block text-sm font-medium text-slate-700">Role</span>
                <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/5">
                        <input type="radio" name="role" value="participant" class="text-brand-500 focus:ring-brand-500" {{ old('role', 'participant') === 'participant' ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-slate-800">Participant</span>
                    </label>
                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/5">
                        <input type="radio" name="role" value="organizer" class="text-brand-500 focus:ring-brand-500" {{ old('role') === 'organizer' ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-slate-800">Organisateur</span>
                    </label>
                </div>
                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-gradient mt-2 w-full py-3 text-base">Create account</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-600">
            Already registered?
            <a href="{{ route('login') }}" class="font-semibold text-brand-500 hover:text-brand-600">Login</a>
        </p>
    </div>
@endsection
