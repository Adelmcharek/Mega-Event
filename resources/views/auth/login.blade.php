@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="rounded-2xl border border-white/10 bg-white/95 p-8 shadow-2xl backdrop-blur-sm">
        <h1 class="text-center text-2xl font-bold text-slate-900">Welcome back</h1>
        <p class="mt-1 text-center text-sm text-slate-600">Sign in to continue to MEGA EVENT</p>

        <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <div class="relative mt-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400" aria-hidden="true">✉️</span>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        class="w-full rounded-xl border pl-10 pr-3 py-2.5 text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('email') ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-200' }}"
                    >
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <div class="relative mt-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400" aria-hidden="true">🔒</span>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl border pl-10 pr-3 py-2.5 text-slate-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('password') ? 'border-red-500 ring-2 ring-red-200' : 'border-slate-200' }}"
                    >
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center gap-2">
                <input id="remember" name="remember" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-500 focus:ring-brand-500" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-sm text-slate-600">Remember me</label>
            </div>
            <button type="submit" class="btn-gradient w-full py-3 text-base">Sign in</button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-600">
            No account?
            <a href="{{ route('register') }}" class="font-semibold text-brand-500 hover:text-brand-600">Register</a>
        </p>
    </div>
@endsection
