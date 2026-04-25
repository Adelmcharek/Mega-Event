<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endif{{ config('app.name', 'MEGA EVENT') }}</title>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen font-sans antialiased" style="background-color: var(--bg-primary);">
    <x-flash-messages />

    {{-- Navbar --}}
    <header class="public-navbar sticky top-0 z-50 border-b border-white/10 backdrop-blur-md"
            style="background-color: var(--bg-navbar);"
            x-data="{ mobileOpen: false }">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold tracking-tight text-white">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg font-bold text-white text-sm"
                     style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">ME</div>
                MEGA EVENT {{-- ✅ Fixed --}}
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition hover:bg-white/10 hover:text-white
                          {{ request()->routeIs('home') ? 'bg-white/10 text-white' : 'text-slate-300' }}">
                    {{ __('app.home') }}
                </a>
                <a href="{{ route('events.index') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition hover:bg-white/10 hover:text-white
                          {{ request()->routeIs('events.*') ? 'bg-white/10 text-white' : 'text-slate-300' }}">
                    {{ __('app.events') }}
                </a>

                @guest
                    <button onclick="toggleTheme()" class="theme-toggle-btn" title="Toggle theme" aria-label="Toggle theme">
                        <span class="theme-toggle-icon">☀️</span>
                    </button>
                    <div class="flex items-center gap-1 rounded-xl border border-white/10 p-1"
                         style="background-color: #0f0f1a;">
                        <a href="{{ route('language.switch', 'en') }}"
                           class="px-3 py-1 rounded-lg text-xs font-bold transition-all
                           {{ app()->getLocale() === 'en' ? 'text-white' : 'text-slate-400 hover:text-white' }}"
                           style="{{ app()->getLocale() === 'en' ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);' : '' }}">
                            EN
                        </a>
                        <a href="{{ route('language.switch', 'fr') }}"
                           class="px-3 py-1 rounded-lg text-xs font-bold transition-all
                           {{ app()->getLocale() === 'fr' ? 'text-white' : 'text-slate-400 hover:text-white' }}"
                           style="{{ app()->getLocale() === 'fr' ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);' : '' }}">
                            FR
                        </a>
                    </div>
                    <a href="{{ route('login') }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                        {{ __('app.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="ml-2 rounded-xl px-4 py-2 text-sm font-semibold text-white shadow-md transition hover:scale-105"
                       style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        {{ __('app.register') }}
                    </a>
                @else
                    @php $r = auth()->user()->role; @endphp
                    <a href="{{ $r === 'admin' ? route('admin.dashboard') : ($r === 'organisateur' ? route('organizer.dashboard') : route('participant.dashboard')) }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                        {{ __('app.dashboard') }}
                    </a>
                    <div class="ml-2 flex items-center gap-2 border-l border-white/10 pl-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white"
                             style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <button onclick="toggleTheme()" class="theme-toggle-btn" title="Toggle theme" aria-label="Toggle theme">
                            <span class="theme-toggle-icon">☀️</span>
                        </button>
                        <div class="flex items-center gap-1 rounded-xl border border-white/10 p-1"
                             style="background-color: #0f0f1a;">
                            <a href="{{ route('language.switch', 'en') }}"
                               class="px-3 py-1 rounded-lg text-xs font-bold transition-all
                               {{ app()->getLocale() === 'en' ? 'text-white' : 'text-slate-400 hover:text-white' }}"
                               style="{{ app()->getLocale() === 'en' ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);' : '' }}">
                                EN
                            </a>
                            <a href="{{ route('language.switch', 'fr') }}"
                               class="px-3 py-1 rounded-lg text-xs font-bold transition-all
                               {{ app()->getLocale() === 'fr' ? 'text-white' : 'text-slate-400 hover:text-white' }}"
                               style="{{ app()->getLocale() === 'fr' ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);' : '' }}">
                                FR
                            </a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-[#29ABE2] hover:text-white transition">
                                {{ __('app.logout') }}
                            </button>
                        </form>
                    </div>
                @endguest
            </nav>

            {{-- Mobile hamburger --}}
            <button type="button"
                    class="inline-flex rounded-lg p-2 text-white md:hidden"
                    @click="mobileOpen = !mobileOpen"
                    aria-label="Menu">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-transition
             class="border-t border-white/10 px-4 py-4 md:hidden"
             style="background-color: var(--bg-secondary);"
             x-cloak>
            <div class="flex flex-col gap-1">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-white/5">{{ __('app.home') }}</a>
                <a href="{{ route('events.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-white/5">{{ __('app.events') }}</a>
                @guest
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-white/5">{{ __('app.login') }}</a>
                    <a href="{{ route('register') }}"
                       class="mt-2 rounded-xl px-4 py-2 text-center text-sm font-semibold text-white"
                       style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        {{ __('app.register') }}
                    </a>
                @else
                    @php $r = auth()->user()->role; @endphp
                    <a href="{{ $r === 'admin' ? route('admin.dashboard') : ($r === 'organisateur' ? route('organizer.dashboard') : route('participant.dashboard')) }}"
                       class="rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-white/5">
                        {{ __('app.dashboard') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-[#29ABE2]">
                            {{ __('app.logout') }}
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-white/10 py-8" style="background-color: var(--bg-secondary);">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <div class="flex items-center justify-center gap-2 mb-3">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg text-xs font-bold text-white"
                     style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">ME</div>
                <span class="font-bold text-white">MEGA EVENT</span>
            </div>
            <p class="text-sm text-slate-400">&copy; {{ date('Y') }} MEGA PIXEL. {{ __('app.footer_rights') }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ __('app.footer_tagline') }}</p>
        </div>
    </footer>
</body>
</html>
