@php
    $layout = match(auth()->user()->role) {
        'admin'        => 'layouts.admin',
        'organisateur' => 'layouts.organizer',
        default        => 'layouts.participant',
    };
@endphp

@extends($layout)
@section('title', 'My Profile')
@section('content')

<div class="max-w-2xl mx-auto space-y-6">

  <div>
    <h1 class="text-2xl font-bold text-white">{{ __('app.my_profile') }}</h1>
    <p class="text-slate-400 mt-1">{{ __('app.account_info') }}</p>
  </div>

  {{-- SECTION 1: Profile Info --}}
  <div class="rounded-2xl border border-white/10 p-6" style="background-color: #1a1a2e;">
    <h2 class="text-lg font-bold text-white mb-6">{{ __('app.account_info') }}</h2>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
      @csrf @method('PATCH')

      {{-- Avatar --}}
      <div class="flex items-center gap-6 mb-6"
           x-data="{ preview: '{{ $user->avatar ? Storage::url($user->avatar) : '' }}' }">
        <label class="cursor-pointer relative group">
          <div class="w-20 h-20 rounded-full overflow-hidden flex items-center justify-center font-bold text-2xl text-white flex-shrink-0"
               style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
            <img x-show="preview" x-bind:src="preview"
                 class="w-full h-full object-cover" alt="Avatar">
            <span x-show="!preview">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
          </div>
          <div class="absolute inset-0 rounded-full bg-black/50 flex items-center justify-center
                      opacity-0 group-hover:opacity-100 transition-opacity">
            <span class="text-white text-xs font-semibold">Change</span>
          </div>
          <input type="file" name="avatar" accept="image/*" class="sr-only"
                 @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview">
        </label>
        <div>
          <p class="text-white font-semibold text-lg">{{ auth()->user()->name }}</p>
          <p class="text-slate-400 text-sm capitalize">{{ auth()->user()->role }}</p>
          <p class="text-slate-500 text-xs mt-1">{{ __('app.profile_photo') }}</p>
        </div>
      </div>

      {{-- Name --}}
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-300 mb-1">{{ __('app.full_name') }}</label>
        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
               class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white"
               style="background-color: #0f0f1a;">
        @error('name')
          <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Email --}}
      <div class="mb-6">
        <label class="block text-sm font-medium text-slate-300 mb-1">{{ __('app.email') }}</label>
        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
               class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white"
               style="background-color: #0f0f1a;">
        @error('email')
          <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      <button type="submit"
              class="rounded-xl px-6 py-2.5 text-sm font-bold text-white transition hover:scale-105"
              style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
        {{ __('app.save_changes') }}
      </button>
    </form>
  </div>

  {{-- SECTION 2: Change Password --}}
  <div id="password" class="rounded-2xl border border-white/10 p-6" style="background-color: #1a1a2e;">
    <h2 class="text-lg font-bold text-white mb-6">{{ __('app.change_password') }}</h2>

    <form method="POST" action="{{ route('profile.password') }}">
      @csrf @method('PATCH')

      {{-- Current Password --}}
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-300 mb-1">{{ __('app.current_password') }}</label>
        <input type="password" name="current_password"
               class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white"
               style="background-color: #0f0f1a;">
        @error('current_password')
          <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- New Password --}}
      <div class="mb-4">
        <label class="block text-sm font-medium text-slate-300 mb-1">{{ __('app.new_password') }}</label>
        <input type="password" name="password"
               class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white"
               style="background-color: #0f0f1a;">
        @error('password')
          <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Confirm Password --}}
      <div class="mb-6">
        <label class="block text-sm font-medium text-slate-300 mb-1">{{ __('app.confirm_new_password') }}</label>
        <input type="password" name="password_confirmation"
               class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white"
               style="background-color: #0f0f1a;">
      </div>

      <button type="submit"
              class="rounded-xl px-6 py-2.5 text-sm font-bold text-white transition hover:scale-105"
              style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
        {{ __('app.update_password') }}
      </button>
    </form>
  </div>

</div>

  {{-- SECTION 3: Danger Zone --}}
  <div class="mt-6 rounded-2xl border border-red-500/20 p-6"
       style="background-color: #1a1a2e;">
    <h2 class="text-lg font-bold text-red-400 mb-2">⚠️ {{ __('app.danger_zone') }}</h2>
    <p class="text-slate-400 text-sm mb-4">
      {{ __('app.delete_account_msg') }}
      {{ __('app.cannot_undo') }}
    </p>

    <div x-data="{ open: false }">
      <button @click="open = true"
              class="rounded-xl border border-red-500/40 px-5 py-2.5 text-sm
                     font-bold text-red-400 hover:bg-red-500/10 transition">
        🗑️ {{ __('app.delete_account') }}
      </button>

      <div x-show="open" x-cloak
           class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl border border-red-500/30 p-6 shadow-2xl"
             style="background-color: #1a1a2e;">

          <div class="text-center mb-6">
            <div class="text-5xl mb-3">⚠️</div>
            <h3 class="text-xl font-bold text-white mb-2">{{ __('app.delete_account') }}?</h3>
            <p class="text-slate-400 text-sm">
              {{ __('app.delete_account_msg') }}
              {{ __('app.cannot_undo') }}
            </p>
          </div>

          <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('DELETE')

            <div class="mb-4">
              <label class="block text-sm font-medium text-slate-300 mb-1.5">
                {{ __('app.enter_password_confirm') }}
              </label>
              <input type="password" name="password" required
                     placeholder="Your current password"
                     class="w-full rounded-xl border border-red-500/30 px-4 py-2.5 text-white"
                     style="background-color: #0f0f1a;">
              @error('password')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
              @enderror
            </div>

            <div class="flex gap-3">
              <button type="button" @click="open = false"
                      class="flex-1 rounded-xl border border-white/10 py-2.5
                             text-sm font-semibold text-slate-300 hover:bg-white/5 transition">
                {{ __('app.cancel') }}
              </button>
              <button type="submit"
                      class="flex-1 rounded-xl py-2.5 text-sm font-bold text-white transition"
                      style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                {{ __('app.delete_account_confirm') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
