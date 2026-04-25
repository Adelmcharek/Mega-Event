<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endif Admin — {{ config('app.name', 'MEGA EVENT') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-content font-sans text-slate-900 antialiased" x-data="{ sidebarOpen: false }">
    <x-flash-messages />

    <div class="flex min-h-screen">
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col transform border-r border-slate-800 bg-hero transition-transform duration-200 lg:static lg:translate-x-0"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
        >
            <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold text-white">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg font-bold text-white text-sm"
                         style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">ME</div>
                    MEGA EVENT
                </a>
                <button type="button" class="rounded-lg p-1 text-slate-400 hover:bg-white/10 lg:hidden" @click="sidebarOpen = false">✕</button>
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
                    <div class="flex-1 text-left min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
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
            <nav class="flex-1 space-y-1 p-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('admin.dashboard')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.dashboard') }}</span>
                </a>
                <a href="{{ route('admin.events.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('admin.events.*')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.all_events') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('admin.users.*')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.users_management') }}</span>
                </a>
                <a href="{{ route('admin.statistics.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all
                   {{ request()->routeIs('admin.statistics.*')
                      ? 'bg-[#29ABE2]/20 border-l-2 border-[#29ABE2] text-white'
                      : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span class="text-sm font-medium">{{ __('app.statistics') }}</span>
                </a>
            </nav>
        </aside>

        <div class="flex flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-700/50 px-4 backdrop-blur md:px-6"
                    style="background-color: var(--bg-secondary);">
                <button type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-800 lg:hidden" @click="sidebarOpen = true">☰</button>
                <div class="flex flex-1 items-center justify-end gap-3">
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
                    <span class="flex h-9 w-9 items-center justify-center rounded-full overflow-hidden text-sm font-bold text-white flex-shrink-0"
                          style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}"
                                 class="w-full h-full object-cover" alt="avatar">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </span>
                    <span class="hidden text-sm font-semibold text-white sm:inline">{{ auth()->user()->name }}</span>
                    <span class="rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold uppercase text-white">{{ __('app.admin_label') }}</span>
                    <button onclick="toggleTheme()" class="theme-toggle-btn" title="Toggle theme" aria-label="Toggle theme">
                        <span class="theme-toggle-icon">☀️</span>
                    </button>
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

    <div x-show="sidebarOpen" class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>
    @stack('scripts')
</body>
</html>
