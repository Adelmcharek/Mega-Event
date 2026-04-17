@extends('layouts.admin')

@section('title', 'All Events')

@section('content')
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">All Events</h1>
            <p class="text-slate-600">Filter, approve, and manage platform events.</p>
        </div>
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <option value="">All statuses</option>
                @foreach (['DRAFT', 'PENDING', 'PUBLISHED', 'REJECTED'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ $s }}</option>
                @endforeach
            </select>
            <input type="text" name="organizer" value="{{ request('organizer') }}" placeholder="Organizer" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Filter</button>
        </form>
    </div>

    <div
        class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        x-data="{ selected: [] }"
    >
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <span class="text-sm text-slate-600" x-show="selected.length" x-text="selected.length + ' selected'" x-cloak></span>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50" @click="alert('Bulk approve — connect backend')">Approve selected</button>
                <button type="button" class="rounded-xl border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50" @click="alert('Bulk delete — connect backend')">Delete selected</button>
            </div>
        </div>
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3"><span class="sr-only">Select</span></th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Organizer</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Participants</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($events ?? [] as $event)
                        @php $e = is_object($event) ? $event : (object) $event; @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <input type="checkbox" :value="{{ $e->id }}" x-model.number="selected" class="rounded border-slate-300 text-brand-500">
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $e->title }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $e->organizer_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($e->date)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $e->registered ?? 0 }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$e->status ?? 'PENDING'" /></td>
                            <td class="px-4 py-3 text-right whitespace-nowrap text-xs font-semibold">
                                <form method="POST" action="{{ route('admin.events.approve', $e->id) }}" class="inline">@csrf<button type="submit" class="text-emerald-600">Approve</button></form>
                                <span class="mx-1 text-slate-300">|</span>
                                <a href="{{ route('admin.events.show', $e->id) }}" class="text-brand-500">View</a>
                                <span class="mx-1 text-slate-300">|</span>
                                <button type="button" class="text-red-600" @click="$dispatch('open-modal', 'del-{{ $e->id }}')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($events ?? [] as $event)
        @php $e = is_object($event) ? $event : (object) $event; @endphp
        <x-confirm-modal id="del-{{ $e->id }}" title="Delete event?">
            Permanently delete {{ $e->title }}?
            <x-slot name="confirm">
                <form method="POST" action="{{ route('admin.events.destroy', $e->id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
                </form>
            </x-slot>
        </x-confirm-modal>
    @endforeach
@endsection
