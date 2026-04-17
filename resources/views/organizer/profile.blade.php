@extends('layouts.organizer')

@section('title', 'My Profile')

@section('content')
    <h1 class="text-2xl font-bold text-white">My Profile</h1>
    {{-- Success messages --}}
@if(session('success'))
    <div class="mt-4 max-w-4xl rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('success_password'))
    <div class="mt-4 max-w-4xl rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-400">
        ✅ {{ session('success_password') }}
    </div>
@endif

{{-- Validation errors --}}
@if($errors->any())
    <div class="mt-4 max-w-4xl rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
        ⚠️ {{ $errors->first() }}
    </div>
@endif
    <p class="mt-2 text-slate-400">Update your organizer account details.</p>

    <div class="mt-8 grid gap-6 lg:grid-cols-2 max-w-4xl">

        {{-- Profile Info Form --}}
        <form method="POST" action="{{ route('organizer.profile.update') }}" class="space-y-5 rounded-2xl border border-white/10 p-6 shadow-sm h-fit" style="background-color: #1a1a2e;">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div class="flex items-center gap-4 pb-4 border-b border-white/10">
                <div class="flex h-16 w-16 items-center justify-center rounded-full text-2xl font-bold text-white"
                     style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-slate-400">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Full name</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white"
                       style="background-color: #111111;">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                       class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white"
                       style="background-color: #111111;">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">
                    Phone <span class="text-slate-500">(optional)</span>
                </label>
                <input type="text" name="phone"
                       value="{{ old('phone', auth()->user()->phone ?? '') }}"
                       placeholder="+216 XX XXX XXX"
                       class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500"
                       style="background-color: #111111;">
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
                        style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                    💾 Save changes
                </button>
            </div>
        </form>

        {{-- Change Password Form --}}
        <form method="POST" action="{{ route('organizer.profile.password') }}"
              class="space-y-5 rounded-2xl border border-white/10 p-6 shadow-sm h-fit"
              style="background-color: #1a1a2e;"
              x-data="{
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
                      return ['', 'Weak', 'Fair', 'Good', 'Strong'][this.strength];
                  },
                  get color() {
                      return ['', 'red', 'amber', 'blue', 'emerald'][this.strength];
                  }
              }">
            @csrf
            @method('PUT')

            <div class="pb-4 border-b border-white/10">
                <h2 class="text-lg font-bold text-white">🔐 Change Password</h2>
                <p class="text-sm text-slate-400 mt-1">Make sure to use a strong password.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Current password</label>
                <input type="password" name="current_password"
                       placeholder="••••••••"
                       class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500"
                       style="background-color: #111111;">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">New password</label>
                <input type="password" name="password"
                       placeholder="••••••••"
                       x-model="password"
                       class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500"
                       style="background-color: #111111;">

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
                       x-text="'Strength: ' + label">
                    </p>
                </div>

                {{-- Checklist --}}
                <ul class="mt-2 space-y-1" x-show="password.length > 0">
                    <li class="flex items-center gap-2 text-xs"
                        :class="password.length >= 8 ? 'text-emerald-400' : 'text-slate-500'">
                        <span x-text="password.length >= 8 ? '✓' : '✗'"></span>
                        At least 8 characters
                    </li>
                    <li class="flex items-center gap-2 text-xs"
                        :class="/[A-Z]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">
                        <span x-text="/[A-Z]/.test(password) ? '✓' : '✗'"></span>
                        One uppercase letter
                    </li>
                    <li class="flex items-center gap-2 text-xs"
                        :class="/[0-9]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">
                        <span x-text="/[0-9]/.test(password) ? '✓' : '✗'"></span>
                        One number
                    </li>
                    <li class="flex items-center gap-2 text-xs"
                        :class="/[^A-Za-z0-9]/.test(password) ? 'text-emerald-400' : 'text-slate-500'">
                        <span x-text="/[^A-Za-z0-9]/.test(password) ? '✓' : '✗'"></span>
                        One special character (!@#$...)
                    </li>
                </ul>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300">Confirm new password</label>
                <input type="password" name="password_confirmation"
                       placeholder="••••••••"
                       class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500"
                       style="background-color: #111111;">
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
                        style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                    🔑 Update Password
                </button>
            </div>
        </form>

    </div>
@endsection