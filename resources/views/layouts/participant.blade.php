<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endifParticipant — {{ config('app.name', 'MEGA EVENT') }}</title>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-content font-sans text-slate-900 antialiased" x-data="{ mobileOpen: false }">
    <x-flash-messages />

    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('participant.dashboard') }}" class="text-lg font-extrabold text-brand-500">MEGA EVENT</a>
            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('events.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('events.*') ? 'bg-slate-100' : '' }}">Browse Events</a>
                <a href="{{ route('participant.registrations.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('participant.registrations.*') ? 'bg-slate-100' : '' }}">My Registrations</a>
                <a href="{{ route('participant.profile') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 {{ request()->routeIs('participant.profile') ? 'bg-slate-100' : '' }}">My Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="ml-2 inline">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Logout</button>
                </form>
            </nav>
            <button type="button" class="rounded-lg p-2 md:hidden" @click="mobileOpen = !mobileOpen" aria-label="Menu">☰</button>
        </div>
        <div x-show="mobileOpen" x-transition class="border-t border-slate-100 px-4 py-3 md:hidden" x-cloak>
            <div class="flex flex-col gap-1">
                <a href="{{ route('events.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium">Browse Events</a>
                <a href="{{ route('participant.registrations.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium">My Registrations</a>
                <a href="{{ route('participant.profile') }}" class="rounded-lg px-3 py-2 text-sm font-medium">My Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>
</body>
</html>
