<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endif{{ config('app.name', 'MEGA EVENT') }}</title>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-content font-sans text-slate-900 antialiased">
    <x-flash-messages />

    <header
        class="sticky top-0 z-50 border-b border-white/10 bg-hero/95 shadow-sm backdrop-blur-md"
        x-data="{ mobileOpen: false }"
    >
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold tracking-tight text-white">
                <span class="rounded-lg bg-gradient-to-br from-purple-600 to-blue-500 px-2 py-0.5 text-sm text-white shadow">ME</span>
                <span>GA EVENT</span>
            </a>

            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('home') ? 'bg-white/10 text-white' : '' }}">Home</a>
                <a href="{{ route('events.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10 hover:text-white {{ request()->routeIs('events.*') ? 'bg-white/10 text-white' : '' }}">Events</a>
                @guest
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10 hover:text-white">Login</a>
                    <a href="{{ route('register') }}" class="ml-2 rounded-xl bg-gradient-to-r from-purple-600 to-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:opacity-95">Register</a>
                @else
                    @php $r = auth()->user()->role; @endphp
                    <a
                        href="{{ $r === 'admin' ? route('admin.dashboard') : ($r === 'organizer' ? route('organizer.dashboard') : route('participant.dashboard')) }}"
                        class="rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10 hover:text-white"
                    >Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="ml-2 inline">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-violet-200 hover:text-white">Logout</button>
                    </form>
                @endguest
            </nav>

            <button
                type="button"
                class="inline-flex rounded-lg p-2 text-white md:hidden"
                @click="mobileOpen = !mobileOpen"
                aria-expanded="false"
                :aria-expanded="mobileOpen"
                aria-label="Menu"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div
            x-show="mobileOpen"
            x-transition
            class="border-t border-white/10 bg-hero px-4 py-4 md:hidden"
            x-cloak
        >
            <div class="flex flex-col gap-1">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white">Home</a>
                <a href="{{ route('events.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white">Events</a>
                @guest
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white">Login</a>
                    <a href="{{ route('register') }}" class="btn-gradient mt-2 text-center text-sm">Register</a>
                @else
                    @php $r = auth()->user()->role; @endphp
                    <a href="{{ $r === 'admin' ? route('admin.dashboard') : ($r === 'organizer' ? route('organizer.dashboard') : route('participant.dashboard')) }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-violet-200">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-auto border-t border-slate-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} MEGA PIXEL. All rights reserved.</p>
            <p class="mt-1 text-xs text-slate-400">MEGA EVENT — Professional, cultural &amp; corporate events.</p>
        </div>
    </footer>
</body>
</html>
