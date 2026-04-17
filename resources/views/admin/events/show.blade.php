@extends('layouts.admin')

@section('title', 'Review Event')

@section('content')
    @php $e = is_object($event ?? null) ? $event : (object) ($event ?? []); @endphp
    <a href="{{ route('admin.events.index') }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600">← All events</a>

    <div class="mt-6 grid gap-8 lg:grid-cols-10">
        <div class="lg:col-span-7 space-y-6">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="h-40 bg-gradient-to-br from-brand-500 to-blue-500"></div>
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-slate-900">{{ $e->title ?? 'Event' }}</h1>
                    <p class="mt-2 text-slate-600">{{ \Carbon\Carbon::parse($e->date ?? now())->format('D, M d Y H:i') }} · {{ $e->location ?? '' }}</p>
                    <p class="mt-4 text-slate-700">{{ $e->description ?? '' }}</p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <x-status-badge :status="$e->status ?? 'PENDING'" />
                        <x-status-badge :status="$e->category ?? 'Professional'" />
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-3">
            <div class="sticky top-24 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                <h2 class="text-lg font-bold text-slate-900">Admin actions</h2>
                <form method="POST" action="{{ route('admin.events.approve', $e->id ?? 1) }}" class="space-y-3">
                    @csrf
                    <button type="submit" class="btn-gradient w-full py-3 text-sm">Approve event</button>
                </form>
                <form method="POST" action="{{ route('admin.events.reject', $e->id ?? 1) }}" class="space-y-3">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700">Rejection reason (optional)</label>
                    <textarea name="reason" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Explain why this event is rejected..."></textarea>
                    <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">Reject</button>
                </form>
            </div>
        </div>
    </div>
@endsection
