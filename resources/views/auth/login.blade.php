@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="rounded-3xl border border-white/10 p-8 shadow-2xl backdrop-blur-xl"
         style="background-color: rgba(26, 26, 46, 0.95);">

        {{-- Logo --}}
        <div class="mb-6 flex flex-col items-center gap-2">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl font-bold text-white text-lg"
                 style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                ME
            </div>
            <h1 class="text-center text-2xl font-bold text-white">{{ __('app.welcome_back') }}</h1>
            <p class="text-center text-sm text-slate-400">{{ __('app.sign_in') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300">{{ __('app.email') }}</label>
                <div class="relative mt-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">✉️</span>
                    <input id="email" name="email" type="email"
                           value="{{ old('email') }}" required autocomplete="username"
                           placeholder="you@example.com"
                           class="w-full rounded-xl border pl-10 pr-3 py-2.5 text-white placeholder-slate-500
                                  focus:border-[#29ABE2] focus:outline-none focus:ring-2 focus:ring-[#29ABE2]/20
                                  {{ $errors->has('email') ? 'border-red-500 ring-2 ring-red-500/20' : 'border-white/10' }}"
                           style="background-color: #111111;">
                </div>
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300">{{ __('app.password') }}</label>
                <div x-data="{ show: false }" class="relative mt-1">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">🔒</span>
                    <input id="password" name="password"
                           :type="show ? 'text' : 'password'"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full rounded-xl border pl-10 pr-12 py-2.5 text-white placeholder-slate-500
                                  focus:border-[#29ABE2] focus:outline-none focus:ring-2 focus:ring-[#29ABE2]/20
                                  {{ $errors->has('password') ? 'border-red-500 ring-2 ring-red-500/20' : 'border-white/10' }}"
                           style="background-color: #111111;">
                    <button
                        type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-white transition-colors"
                        tabindex="-1"
                    >
                        <!-- Œil ouvert : mot de passe visible -->
                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                     -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Œil barré : mot de passe masqué -->
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
                                     a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243
                                     M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532
                                     l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5
                                     c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411
                                     m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center gap-2">
                <input id="remember" name="remember" type="checkbox" value="1"
                       class="h-4 w-4 rounded border-white/20 accent-[#29ABE2]"
                       {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-sm text-slate-400">{{ __('app.remember_me') }}</label>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="w-full rounded-xl py-3 text-base font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
                    style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                {{ __('app.sign_in') }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            {{ __('app.no_account') }}
            <a href="{{ route('register') }}" class="font-semibold text-[#29ABE2] hover:text-[#4CBDE8]">{{ __('app.register') }}</a>
        </p>
    </div>
@endsection