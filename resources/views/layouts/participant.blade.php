<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endif MEGA EVENT — {{ config('app.name', 'MEGA EVENT') }}</title>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen font-sans antialiased" style="background-color: var(--bg-primary);"
      x-data="{ mobileOpen: false }">
    <x-flash-messages />

    <header class="sticky top-0 z-50 border-b border-white/10 backdrop-blur"
            style="background-color: var(--bg-navbar);">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">

            {{-- Logo --}}
            <a href="{{ route('participant.dashboard') }}"
               class="flex items-center gap-2 text-lg font-extrabold text-white">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold text-white"
                     style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">ME</div>
                MEGA EVENT
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('events.index') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition-colors
                          {{ request()->routeIs('events.*')
                              ? 'bg-[#29ABE2]/10 text-[#29ABE2]'
                              : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Browse Events
                </a>
                <a href="{{ route('participant.registrations.index') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition-colors
                          {{ request()->routeIs('participant.registrations.*')
                              ? 'bg-[#29ABE2]/10 text-[#29ABE2]'
                              : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    My Registrations
                </a>
                <a href="{{ route('participant.profile') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition-colors
                          {{ request()->routeIs('participant.profile')
                              ? 'bg-[#29ABE2]/10 text-[#29ABE2]'
                              : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    My Profile
                </a>

                {{-- Avatar + Logout --}}
                <div class="ml-3 flex items-center gap-3 border-l border-white/10 pl-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white"
                         style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden text-sm font-medium text-white lg:block">
                        {{ auth()->user()->name }}
                    </span>
                    <button onclick="toggleTheme()" class="theme-toggle-btn" title="Toggle theme" aria-label="Toggle theme">
                        <span class="theme-toggle-icon">☀️</span>
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="rounded-xl border border-white/10 px-3 py-1.5 text-sm font-semibold text-slate-300 hover:bg-white/5 hover:text-white transition-all">
                            Logout
                        </button>
                    </form>
                </div>
            </nav>

            {{-- Mobile hamburger --}}
            <button type="button"
                    class="rounded-lg p-2 text-slate-400 hover:bg-white/5 md:hidden"
                    @click="mobileOpen = !mobileOpen"
                    aria-label="Menu">☰</button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-transition
             class="border-t border-white/10 px-4 py-3 md:hidden"
             style="background-color: var(--bg-secondary);"
             x-cloak>
            <div class="flex flex-col gap-1">
                <a href="{{ route('events.index') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">
                    Browse Events
                </a>
                <a href="{{ route('participant.registrations.index') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">
                    My Registrations
                </a>
                <a href="{{ route('participant.profile') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">
                    My Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>