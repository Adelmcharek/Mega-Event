@extends('layouts.guest')
@section('content')

<div class="min-h-screen flex items-center justify-center px-4"
     style="background: linear-gradient(135deg, #0a0a1a 0%, #1a1a2e 50%, #0f0f23 100%);">

  <div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
      <div class="inline-flex items-center gap-3 mb-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-black text-white text-lg"
             style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">ME</div>
        <span class="text-2xl font-black text-white">MEGA EVENT</span>
      </div>
    </div>

    {{-- Card --}}
    <div class="rounded-3xl border border-white/10 p-8 shadow-2xl"
         style="background: rgba(26,26,46,0.95); backdrop-filter: blur(20px);">

      {{-- Email icon --}}
      <div class="text-center mb-6">
        <div class="w-20 h-20 rounded-full flex items-center justify-center text-4xl mx-auto mb-4"
             style="background: linear-gradient(135deg, #29ABE220, #29ABE210); border: 2px solid #29ABE240;">
          📧
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">{{ __('app.verify_email') }}</h1>
        <p class="text-slate-400 text-sm leading-relaxed">
          {{ __('app.verify_email_msg') }}
        </p>
      </div>

      {{-- Success message --}}
      @if(session('success'))
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 mb-4 text-sm text-emerald-400">
          ✅ {{ session('success') }}
        </div>
      @endif

      {{-- Info box --}}
      <div class="rounded-xl border border-[#29ABE2]/20 bg-[#29ABE2]/5 px-4 py-3 mb-6 text-sm text-slate-300">
        <p>📬 {{ __('app.check_inbox_at') }}</p>
        <p class="font-bold text-[#29ABE2] mt-1">{{ auth()->user()->email }}</p>
      </div>

      {{-- Resend button --}}
      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
                class="w-full rounded-xl py-3 font-bold text-white transition hover:scale-105 mb-4"
                style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
          📤 {{ __('app.resend_email') }}
        </button>
      </form>

      {{-- Logout --}}
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="w-full rounded-xl border border-white/10 py-3 text-sm font-semibold text-slate-400 hover:text-white hover:bg-white/5 transition">
          {{ __('app.logout') }}
        </button>
      </form>

    </div>

  </div>
</div>
@endsection
