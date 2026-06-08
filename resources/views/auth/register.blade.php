@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="rounded-3xl border border-white/10 p-8 shadow-2xl backdrop-blur-xl"
         style="background-color: rgba(26, 26, 46, 0.95);">

        {{-- Logo --}}
        <div class="mb-6 flex flex-col items-center gap-2">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl font-bold text-white text-lg"
                 style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                ME
            </div>
            <h1 class="text-center text-2xl font-bold text-white">{{ __('app.create_account') }}</h1>
            <p class="text-center text-sm text-slate-400">{{ __('app.sign_up') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- Full Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300">{{ __('app.full_name') }}</label>
                <input id="name" name="name" type="text"
                       value="{{ old('name') }}" required
                       placeholder="Your Name Here"
                       class="mt-1 w-full rounded-xl border px-3 py-2.5 text-white placeholder-slate-500
                              {{ $errors->has('name') ? 'border-red-500 ring-2 ring-red-500/20' : 'border-white/10' }}"
                       style="background-color: #111111;">
                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div x-data="{ email: '{{ old('email') }}',
                           get valid() { return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(this.email); } }">
                <label for="email" class="block text-sm font-medium text-slate-300">{{ __('app.email') }}</label>
                <input id="email" name="email" type="email"
                       x-model="email"
                       required
                       placeholder="yourname@example.com"
                       class="mt-1 w-full rounded-xl border px-3 py-2.5 text-white placeholder-slate-500
                              {{ $errors->has('email') ? 'border-red-500 ring-2 ring-red-500/20' : 'border-white/10' }}"
                       style="background-color: #111111;">
                <p x-show="email.length > 0" x-cloak class="mt-1 text-sm"
                   :class="valid ? 'text-emerald-400' : 'text-red-400'">
                    <span x-show="valid">✓ {{ __('app.email_valid') }}</span>
                    <span x-show="!valid">✗ {{ __('app.email_invalid') }}</span>
                </p>
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div x-data="{ phone: '{{ old('phone') }}',
                           get valid() { return /^[+]?[\d\s]{8,}$/.test(this.phone); } }">
                <label for="phone" class="block text-sm font-medium text-slate-300">{{ __('app.phone') }}</label>
                <input id="phone" name="phone" type="tel"
                       x-model="phone"
                       placeholder="+216 XX XXX XXX"
                       class="mt-1 w-full rounded-xl border px-3 py-2.5 text-white placeholder-slate-500
                              {{ $errors->has('phone') ? 'border-red-500 ring-2 ring-red-500/20' : 'border-white/10' }}"
                       style="background-color: #111111;">
                <p x-show="phone.length > 0" x-cloak class="mt-1 text-sm"
                   :class="valid ? 'text-emerald-400' : 'text-red-400'">
                    <span x-show="valid">✓ {{ __('app.phone_valid') }}</span>
                    <span x-show="!valid">✗ {{ __('app.phone_invalid') }}</span>
                </p>
                @error('phone')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password with strength indicator --}}
            <div x-data="{
                show: false,
                showConfirm: false,
                password: '',
                get strength() {
                    let score = 0;
                    if (this.password.length >= 8) score++;
                    if (/[A-Z]/.test(this.password)) score++;
                    if (/[0-9]/.test(this.password)) score++;
                    if (/[^A-Za-z0-9]/.test(this.password)) score++;
                    return score;
                },
                get label() {
                    return ['', '{{ __("app.pwd_weak") }}', '{{ __("app.pwd_fair") }}', '{{ __("app.pwd_good") }}', '{{ __("app.pwd_strong") }}'][this.strength];
                },
                get color() {
                    return ['', 'red', 'amber', 'blue', 'emerald'][this.strength];
                }
            }">
                {{-- Password field --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300">{{ __('app.password') }}</label>
                    <div class="relative mt-1">
                        <input id="password" name="password"
                               :type="show ? 'text' : 'password'"
                               x-model="password"
                               required placeholder="••••••••"
                               class="w-full rounded-xl border px-3 py-2.5 text-white placeholder-slate-500 pr-10
                                      {{ $errors->has('password') ? 'border-red-500 ring-2 ring-red-500/20' : 'border-white/10' }}"
                               style="background-color: #111111;">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-white transition-colors">
                            <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Strength bars --}}
                    <div class="mt-2 flex gap-1" x-show="password.length > 0">
                        <template x-for="i in 4">
                            <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                 :style="i <= strength
                                     ? `background-color: ${color === 'red' ? '#ef4444' : color === 'amber' ? '#f59e0b' : color === 'blue' ? '#29ABE2' : '#10b981'}`
                                     : 'background-color: rgba(255,255,255,0.1)'">
                            </div>
                        </template>
                    </div>

                    {{-- Strength label --}}
                    <div class="mt-1" x-show="password.length > 0">
                        <p class="text-xs"
                           :class="{
                               'text-red-400':     color === 'red',
                               'text-amber-400':   color === 'amber',
                               'text-[#29ABE2]':   color === 'blue',
                               'text-emerald-400': color === 'emerald'
                           }"
                           x-text="'{{ __("app.pwd_strength") }}' + label">
                        </p>
                    </div>

                    {{-- Checklist --}}
                    <ul class="mt-2 space-y-1" x-show="password.length > 0">
                        <li class="flex items-center gap-2 text-xs"
                            :class="password.length >= 8 ? 'text-emerald-400' : 'text-slate-500'">
                            <span x-text="password.length >= 8 ? '✓' : '✗'"></span>
                            {{ __('app.pwd_min_chars') }}
                        </li>
                        <li class="flex items-center gap-2 text-xs"
                            :class="/[A-Z]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">
                            <span x-text="/[A-Z]/.test(password) ? '✓' : '✗'"></span>
                            {{ __('app.pwd_uppercase') }}
                        </li>
                        <li class="flex items-center gap-2 text-xs"
                            :class="/[0-9]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">
                            <span x-text="/[0-9]/.test(password) ? '✓' : '✗'"></span>
                            {{ __('app.pwd_number') }}
                        </li>
                        <li class="flex items-center gap-2 text-xs"
                            :class="/[^A-Za-z0-9]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">
                            <span x-text="/[^A-Za-z0-9]/.test(password) ? '✓' : '✗'"></span>
                            {{ __('app.pwd_special') }}
                        </li>
                    </ul>

                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300">
                        {{ __('app.confirm_password') }}
                    </label>
                    <div class="relative mt-1">
                        <input id="password_confirmation" name="password_confirmation"
                               :type="showConfirm ? 'text' : 'password'"
                               required placeholder="••••••••"
                               class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500 pr-10"
                               style="background-color: #111111;">
                        <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-white transition-colors">
                            <svg x-show="!showConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>{{-- end x-data --}}

            {{-- Role Selector --}}
            <div x-data="{ role: '{{ old('role', 'participant') }}' }">
                <span class="block text-sm font-medium text-slate-300">{{ __('app.role') }}</span>
                <div class="mt-3 grid grid-cols-2 gap-3">

                    {{-- Participant --}}
                    <label @click="role = 'participant'"
                           :class="role === 'participant'
                               ? 'border-[#29ABE2] shadow-lg shadow-[#29ABE2]/20'
                               : 'border-white/10 hover:border-white/20'"
                           class="cursor-pointer flex flex-col items-center justify-center gap-2 rounded-2xl border-2 p-5 transition-all duration-300 text-center"
                           style="background-color: #111111;">
                        <span class="text-3xl">👥</span>
                        <p class="text-sm font-semibold text-white">{{ __('app.role_participant') }}</p>
                        <p class="text-xs text-slate-400">{{ __('app.join_events') }}</p>
                        <input type="radio" name="role" value="participant" class="hidden"
                               x-bind:checked="role === 'participant'">
                    </label>

                    {{-- Organizer --}}
                    <label @click="role = 'organisateur'"
                           :class="role === 'organisateur'
                               ? 'border-[#29ABE2] shadow-lg shadow-[#29ABE2]/20'
                               : 'border-white/10 hover:border-white/20'"
                           class="cursor-pointer flex flex-col items-center justify-center gap-2 rounded-2xl border-2 p-5 transition-all duration-300 text-center"
                           style="background-color: #111111;">
                        <span class="text-3xl">📅</span>
                        <p class="text-sm font-semibold text-white">{{ __('app.role_organizer') }}</p>
                        <p class="text-xs text-slate-400">{{ __('app.create_events') }}</p>
                        <input type="radio" name="role" value="organisateur" class="hidden"
                               x-bind:checked="role === 'organisateur'">
                    </label>

                </div>
                @error('role')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Avatar Upload --}}
            <div x-data="{ preview: null }">
                <label class="block text-sm font-medium text-slate-300 mb-2">
                    {{ __('app.profile_photo') }} <span class="text-slate-500">({{ __('app.optional') }})</span>
                </label>
                <div class="flex items-center gap-4">
                    <label class="cursor-pointer">
                        <div class="w-16 h-16 rounded-full border-2 border-dashed border-white/20
                                    flex items-center justify-center hover:border-[#29ABE2]/50 transition overflow-hidden"
                             style="background-color: #0f0f1a;">
                            <img x-show="preview" x-bind:src="preview" class="w-full h-full object-cover">
                            <span x-show="!preview" class="text-2xl">📷</span>
                        </div>
                        <input type="file" name="avatar" accept="image/*" class="sr-only"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                    </label>
                    <p class="text-xs text-slate-500">{{ __('app.profile_photo') }}</p>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
                    class="mt-2 w-full rounded-xl py-3 text-base font-semibold text-white
                           transition-all duration-300 ease-in-out
                           hover:scale-[1.02] hover:shadow-lg hover:shadow-[#29ABE2]/25"
                    style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                {{ __('app.create_account') }}
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            {{ __('app.already_account') }}
            <a href="{{ route('login') }}" class="font-semibold text-[#29ABE2] hover:text-[#4CBDE8]">
                {{ __('app.login') }}
            </a>
        </p>
    </div>
@endsection