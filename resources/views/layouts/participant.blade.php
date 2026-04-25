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
            <a href="{{ route('home') }}"
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
                    {{ __('app.browse_events') }}
                </a>
                <a href="{{ route('participant.registrations.index') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition-colors
                          {{ request()->routeIs('participant.registrations.*')
                              ? 'bg-[#29ABE2]/10 text-[#29ABE2]'
                              : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    {{ __('app.my_registrations') }}
                </a>
                {{-- Theme + Language + User dropdown --}}
                <div class="ml-3 flex items-center gap-3 border-l border-white/10 pl-3">
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
                    {{-- Gmail-style user dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 rounded-xl p-1.5 hover:bg-white/5 transition-all">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full overflow-hidden text-xs font-bold text-white flex-shrink-0"
                                 style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                @if(auth()->user()->avatar)
                                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                         class="w-full h-full object-cover" alt="avatar">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @endif
                            </div>
                            <span class="hidden text-sm font-medium text-white lg:block">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="user-dropdown absolute right-0 top-full mt-2 w-64 rounded-xl border shadow-2xl overflow-hidden"
                             style="background-color: var(--bg-card); z-index: 9999; border-color: var(--border-color);">
                            <div class="px-4 py-3 border-b" style="border-color: var(--border-color);">
                                <p class="text-xs" style="color: var(--text-secondary);">{{ __('app.signed_in_as') }}</p>
                                <p class="text-sm font-semibold truncate" style="color: #29ABE2;">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-white/5 hover:text-white transition-colors" style="color: var(--text-secondary);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ __('app.my_profile') }}
                                </a>
                                <a href="{{ route('profile.edit') }}#password"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-white/5 hover:text-white transition-colors" style="color: var(--text-secondary);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    {{ __('app.change_password') }}
                                </a>
                                <div class="border-t my-1" style="border-color: var(--border-color);"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        {{ __('app.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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
                    {{ __('app.browse_events') }}
                </a>
                <a href="{{ route('participant.registrations.index') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">
                    {{ __('app.my_registrations') }}
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">
                    {{ __('app.my_profile') }}
                </a>
                <div class="px-3 py-2">
                    <button onclick="toggleTheme()" class="theme-toggle-btn" title="Toggle theme" aria-label="Toggle theme">
                        <span class="theme-toggle-icon">☀️</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white">
                        {{ __('app.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </header>

    @if(auth()->user() && !auth()->user()->hasVerifiedEmail())
    <div class="mx-6 mt-4 flex items-center justify-between rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-400">
      <div class="flex items-center gap-2">
        <span>⚠️</span>
        <span>{{ __('app.verify_email_banner') }}</span>
      </div>
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="rounded-lg border border-amber-500/40 px-3 py-1 text-xs font-semibold hover:bg-amber-500/20 transition">
          {{ __('app.resend_email_btn') }}
        </button>
      </form>
    </div>
    @endif
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>