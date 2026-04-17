<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@yield('title', 'Auth') — {{ config('app.name', 'MEGA EVENT') }}</title>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-hero font-sans antialiased">
    <x-flash-messages />
    <div class="relative flex min-h-screen items-center justify-center px-4 py-12">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="blob absolute -left-20 top-20 h-72 w-72 bg-purple-600"></div>
            <div class="blob absolute bottom-0 right-0 h-96 w-96 bg-blue-500" style="animation-delay: -5s"></div>
        </div>
        <div class="relative w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-2xl font-extrabold tracking-tight text-white">
                    <span class="rounded-lg bg-gradient-to-br from-purple-600 to-blue-500 px-2 py-0.5 text-sm">ME</span>
                    GA EVENT
                </a>
                <p class="mt-2 text-sm text-slate-400">by MEGA PIXEL</p>
            </div>
            @yield('content')
        </div>
    </div>
</body>
</html>
