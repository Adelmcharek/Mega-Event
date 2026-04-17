<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@yield('title', 'Auth') — {{ config('app.name', 'MEGA EVENT') }}</title>
    <style>
        [x-cloak] { display: none !important; }
        .blob {
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: blobMove 8s ease-in-out infinite alternate;
        }
        @keyframes blobMove {
            0%   { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.1) translate(20px, -20px); }
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased" style="background-color: var(--bg-primary);">
    <x-flash-messages />

    <div class="relative flex min-h-screen items-center justify-center px-4 py-12">

        {{-- Background blobs — contained, no overflow --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="blob absolute left-10 top-20 h-64 w-64" style="background-color: #29ABE2;"></div>
            <div class="blob absolute bottom-20 right-10 h-80 w-80" style="background-color: #0A7FA8; animation-delay: -5s;"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">

            {{-- Logo --}}
            <div class="mb-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-2xl font-extrabold tracking-tight text-white">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl font-bold text-white text-sm shadow-lg"
                         style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                        ME
                    </div>
                    MEGA EVENT  {{-- ✅ Fixed name --}}
                </a>
                <p class="mt-2 text-sm text-slate-400">by MEGA PIXEL</p>
            </div>

            @yield('content')
        </div>
    </div>
</body>
</html>
