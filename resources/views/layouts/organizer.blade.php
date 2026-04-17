<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endifOrganizer — {{ config('app.name', 'MEGA EVENT') }}</title>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-content font-sans text-slate-900 antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
    <x-flash-messages />

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-800 bg-hero transition-transform duration-200 lg:static lg:translate-x-0"
            :class="{
                '-translate-x-full': !sidebarOpen,
                'translate-x-0': sidebarOpen,
                'lg:w-20': sidebarCollapsed,
                'lg:w-64': !sidebarCollapsed
            }"
        >
            <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
                <a href="{{ route('organizer.dashboard') }}" class="flex items-center gap-2 font-extrabold text-white" :class="{ 'lg:justify-center lg:gap-0': sidebarCollapsed }">
                    <span class="rounded-lg bg-gradient-to-br from-purple-600 to-blue-500 px-2 py-0.5 text-xs">ME</span>
                    <span class="lg:hidden" x-show="!sidebarCollapsed || window.innerWidth < 1024">Organizer</span>
                    <span class="hidden font-semibold lg:inline" x-show="!sidebarCollapsed">Organizer</span>
                </a>
                <button type="button" class="rounded-lg p-1 text-slate-400 hover:bg-white/10 hover:text-white lg:hidden" @click="sidebarOpen = false" aria-label="Close menu">✕</button>
            </div>
            <nav class="space-y-1 p-3">
                <x-sidebar-link :href="route('organizer.dashboard')" :active="request()->routeIs('organizer.dashboard')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </x-slot>
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link :href="route('organizer.events.index')" :active="request()->routeIs('organizer.events.index')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </x-slot>
                    My Events
                </x-sidebar-link>
                <x-sidebar-link :href="route('organizer.events.create')" :active="request()->routeIs('organizer.events.create')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </x-slot>
                    Create Event
                </x-sidebar-link>
                <x-sidebar-link :href="route('organizer.participants')" :active="request()->routeIs('organizer.participants')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </x-slot>
                    Participants
                </x-sidebar-link>
                <x-sidebar-link :href="route('organizer.partners')" :active="request()->routeIs('organizer.partners')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </x-slot>
                    Partners
                </x-sidebar-link>
                <x-sidebar-link :href="route('organizer.profile')" :active="request()->routeIs('organizer.profile')">
                    <x-slot name="icon">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </x-slot>
                    My Profile
                </x-sidebar-link>
            </nav>
            <div class="absolute bottom-4 left-0 hidden w-full px-3 lg:block">
                <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" class="flex w-full items-center justify-center gap-2 rounded-xl border border-white/10 py-2 text-xs font-medium text-slate-300 hover:bg-white/10">
                    <span x-text="sidebarCollapsed ? 'Expand' : 'Collapse'"></span>
                </button>
            </div>
        </aside>

        <div class="flex flex-1 flex-col lg:ml-0">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur md:px-6">
                <button type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true" aria-label="Open menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex flex-1 items-center justify-end gap-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-purple-600 to-blue-500 text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden text-sm font-semibold text-slate-800 sm:inline">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Logout</button>
                    </form>
                </div>
            </header>

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
</body>
</html>
