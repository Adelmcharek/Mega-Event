<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@hasSection('title')@yield('title') — @endifAdmin — {{ config('app.name', 'MEGA EVENT') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-content font-sans text-slate-900 antialiased" x-data="{ sidebarOpen: false }">
    <x-flash-messages />

    <div class="flex min-h-screen">
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 transform border-r border-slate-800 bg-hero transition-transform duration-200 lg:static lg:translate-x-0"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
        >
            <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
                <a href="{{ route('admin.dashboard') }}" class="font-extrabold text-white">MEGA EVENT</a>
                <button type="button" class="rounded-lg p-1 text-slate-400 hover:bg-white/10 lg:hidden" @click="sidebarOpen = false">✕</button>
            </div>
            <nav class="space-y-1 p-3">
                <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <x-slot name="icon"><span class="text-lg">📊</span></x-slot>
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                    <x-slot name="icon"><span class="text-lg">📅</span></x-slot>
                    All Events
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    <x-slot name="icon"><span class="text-lg">👤</span></x-slot>
                    Users Management
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.statistics.index')" :active="request()->routeIs('admin.statistics.*')">
                    <x-slot name="icon"><span class="text-lg">📈</span></x-slot>
                    Statistics
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.partners')" :active="request()->routeIs('admin.partners')">
                    <x-slot name="icon"><span class="text-lg">🤝</span></x-slot>
                    Partners
                </x-sidebar-link>
            </nav>
        </aside>

        <div class="flex flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur md:px-6">
                <button type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true">☰</button>
                <div class="flex flex-1 items-center justify-end gap-3">
                    <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-bold uppercase text-white">Admin</span>
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

    <div x-show="sidebarOpen" class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-transition.opacity @click="sidebarOpen = false" x-cloak></div>
    @stack('scripts')
</body>
</html>
