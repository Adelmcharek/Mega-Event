@extends('layouts.public')
@section('title', __('app.payment_success_title'))
@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
  <div class="text-center p-8 rounded-2xl border border-emerald-500/20 max-w-md w-full"
       style="background-color: #1a1a2e;">
    <div class="text-6xl mb-4">🎉</div>
    <h1 class="text-2xl font-bold text-white mb-2">{{ __('app.payment_success_title') }}</h1>
    <p class="text-slate-400 mb-2">{{ __('app.payment_success_msg') }}</p>
    <p class="text-emerald-400 font-semibold mb-6">
      {{ str_replace(':event', $event->title ?? __('app.event'), __('app.payment_success_welcome')) }}
    </p>
    <div class="flex gap-3 justify-center">
      <a href="{{ route('events.index') }}"
         class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:scale-105"
         style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
        {{ __('app.browse_events') }}
      </a>
      <a href="{{ route('participant.dashboard') }}"
         class="px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:scale-105"
         style="background: linear-gradient(135deg, #10b981, #059669);">
        {{ __('app.my_dashboard') }}
      </a>
    </div>
  </div>
</div>
@endsection
