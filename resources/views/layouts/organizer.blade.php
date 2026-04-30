<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endif MEGA EVENT — {{ config('app.name', 'MEGA EVENT') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-content font-sans text-slate-900 antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
    <x-flash-messages />

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 transform border-r border-white/10 transition-transform duration-200 lg:static lg:translate-x-0"
            style="background-color: #111827;"
            :class="{
                '-translate-x-full': !sidebarOpen,
                'translate-x-0': sidebarOpen,
                'lg:w-20': sidebarCollapsed,
                'lg:w-64': !sidebarCollapsed
            }"
        >
            <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3" :class="{ 'lg:justify-center lg:gap-0': sidebarCollapsed }">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white text-lg flex-shrink-0"
                         style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">ME</div>
                    <span class="text-xl font-black text-white tracking-tight lg:hidden" x-show="!sidebarCollapsed || window.innerWidth < 1024">MEGA EVENT</span>
                    <span class="hidden text-xl font-black text-white tracking-tight lg:inline" x-show="!sidebarCollapsed">MEGA EVENT</span>
                </a>
                <button type="button" class="rounded-lg p-1 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Close menu">✕</button>
            </div>
            {{-- Gmail-style user dropdown --}}
            <div class="relative p-4 border-b border-white/10" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center gap-3 w-full rounded-xl p-2 hover:bg-white/5 transition-all">
                    <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center font-bold text-white text-sm flex-shrink-0"
                         style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                 class="w-full h-full object-cover" alt="avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1 text-left min-w-0" x-show="!sidebarCollapsed">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         x-show="!sidebarCollapsed"
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
                     class="user-dropdown rounded-xl border shadow-2xl overflow-hidden"
                     style="position: fixed; top: 80px; left: 8px; width: 240px; background-color: var(--bg-card); z-index: 99999; border-color: var(--border-color);">
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
            <nav class="space-y-1 p-3">
                <a href="{{ route('organizer.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('organizer.dashboard')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.dashboard') }}</span>
                </a>
                <a href="{{ route('organizer.events.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('organizer.events.index')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.my_events') }}</span>
                </a>
                <a href="{{ route('organizer.events.create') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('organizer.events.create')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.create_event') }}</span>
                </a>
                <a href="{{ route('organizer.participants') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('organizer.participants')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.participants') }}</span>
                </a>
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('profile.edit')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.my_profile') }}</span>
                </a>
            </nav>
            <div class="border-t border-white/10 p-3">
                <button type="button" @click="sidebarCollapsed = !sidebarCollapsed"
                        class="hidden w-full items-center justify-center gap-2 rounded-xl border border-white/10 py-2 text-xs font-medium text-slate-300 hover:bg-white/10 lg:flex">
                    <span x-text="sidebarCollapsed ? '{{ __('app.expand') }}' : '{{ __('app.collapse') }}'"></span>
                </button>
            </div>
        </aside>

        <div class="flex flex-1 flex-col lg:ml-0">
            <header class="sticky top-0 z-40 flex items-center justify-between px-6 py-3 border-b border-white/10"
                    style="background-color: rgba(10,10,26,0.95); backdrop-filter: blur(10px);">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-lg p-2 text-gray-400 hover:bg-white/10 lg:hidden" @click="sidebarOpen = true" aria-label="Open menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-bold text-white">@yield('title')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="toggleTheme()"
                            class="w-9 h-9 rounded-xl flex items-center justify-center border border-white/10 hover:bg-white/10 transition-all"
                            title="Toggle theme" aria-label="Toggle theme">
                        <span id="theme-icon">🌙</span>
                    </button>
                    <div class="flex items-center gap-1 rounded-xl border border-white/10 p-1"
                         style="background-color: rgba(0,0,0,0.2);">
                        <a href="{{ route('language.switch', 'en') }}"
                           class="px-3 py-1 rounded-lg text-xs font-bold transition-all
                           {{ app()->getLocale() === 'en' ? 'text-white' : 'text-slate-400 hover:text-white' }}"
                           style="{{ app()->getLocale() === 'en' ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);' : '' }}">EN</a>
                        <a href="{{ route('language.switch', 'fr') }}"
                           class="px-3 py-1 rounded-lg text-xs font-bold transition-all
                           {{ app()->getLocale() === 'fr' ? 'text-white' : 'text-slate-400 hover:text-white' }}"
                           style="{{ app()->getLocale() === 'fr' ? 'background: linear-gradient(135deg, #29ABE2, #0A7FA8);' : '' }}">FR</a>
                    </div>
                    <div class="w-9 h-9 rounded-full overflow-hidden flex items-center justify-center font-bold text-white text-sm flex-shrink-0"
                         style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                 class="w-full h-full object-cover" alt="avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <span class="hidden text-sm font-semibold text-white sm:inline">{{ auth()->user()->name }}</span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-bold uppercase"
                          style="background: #29ABE220; color: #29ABE2;">{{ __('app.organizer_label') }}</span>
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
            <main class="flex-1 p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <div
        x-show="sidebarOpen"
        class="fixed inset-0 z-30 bg-black/40 lg:hidden"
        x-transition.opacity
        @click="sidebarOpen = false"
        x-cloak
    ></div>
    @stack('scripts')
</body>
</html>
