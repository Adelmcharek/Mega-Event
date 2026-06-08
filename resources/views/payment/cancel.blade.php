@extends('layouts.public')
@section('title', __('app.payment_cancel_title'))
@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
  <div class="text-center p-8 rounded-2xl border border-red-500/20 max-w-md w-full"
       style="background-color: #1a1a2e;">
    <div class="text-6xl mb-4">❌</div>
    <h1 class="text-2xl font-bold text-white mb-2">{{ __('app.payment_cancel_title') }}</h1>
    <p class="text-slate-400 mb-6">
      {{ __('app.payment_cancel_msg') }}
    </p>
    <a href="{{ route('events.index') }}"
       class="inline-block px-6 py-3 rounded-xl font-bold text-white
              transition hover:scale-105"
       style="background: linear-gradient(135deg, #ef4444, #dc2626);">
      {{ __('app.back') }}
    </a>
  </div>
</div>
@endsection
