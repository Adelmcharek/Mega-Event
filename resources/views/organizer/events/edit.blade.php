@extends('layouts.organizer')

@section('title', 'Edit Event')

@section('content')
    @php
        $e = is_object($event ?? null) ? $event : (object) ($event ?? []);
    @endphp
    <h1 class="text-2xl font-bold text-slate-900">Edit Event</h1>
    <p class="mt-1 text-slate-600">Update details and resubmit if needed.</p>

    <form method="POST" action="{{ route('organizer.events.update', $e->id ?? 1) }}" enctype="multipart/form-data" class="mt-8 space-y-8" x-data="{ preview: null }">
        @csrf
        @method('PUT')
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $e->title ?? '') }}" required class="mt-1 w-full rounded-xl border px-3 py-2.5 {{ $errors->has('title') ? 'border-red-500 ring-2 ring-red-100' : 'border-slate-200' }}">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea id="description" name="description" rows="6" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">{{ old('description', $e->description ?? '') }}</textarea>
                </div>
                <div>
                    <label for="category" class="block text-sm font-medium text-slate-700">Category</label>
                    <select id="category" name="category" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                        @foreach (['Professional', 'Cultural', 'Corporate'] as $c)
                            <option value="{{ $c }}" @selected(old('category', $e->category ?? '') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-slate-700">Date &amp; time</label>
                    <input id="date" name="date" type="datetime-local" value="{{ old('date', isset($e->date) ? \Carbon\Carbon::parse($e->date)->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-slate-700">Location</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">📍</span>
                        <input id="location" name="location" type="text" value="{{ old('location', $e->location ?? '') }}" class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-3">
                    </div>
                </div>
                <div>
                    <label for="capacity" class="block text-sm font-medium text-slate-700">Capacity</label>
                    <input id="capacity" name="capacity" type="number" min="1" value="{{ old('capacity', $e->capacity ?? 50) }}" title="Max number of participants" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                    <p class="mt-1 text-xs text-slate-500">Max number of participants</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <span class="block text-sm font-medium text-slate-700">Cover image</span>
                    <label class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 transition hover:border-brand-500/50 hover:bg-brand-500/5">
                        <input type="file" name="cover" accept="image/*" class="sr-only" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        <span class="text-center text-sm text-slate-500" x-show="!preview">Drag &amp; drop or click to replace</span>
                        <img x-show="preview" x-bind:src="preview" alt="" class="mt-2 max-h-48 rounded-lg object-cover shadow" x-cloak>
                    </label>
                </div>
                <div>
                    <label for="partners" class="block text-sm font-medium text-slate-700">Partner association</label>
                    <input id="partners" name="partners" type="text" value="{{ old('partners', $e->partners ?? '') }}" placeholder="Comma separated" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5">
                </div>
                <div>
                    <span class="block text-sm font-medium text-slate-700">Visibility</span>
                    <div class="mt-2 flex gap-4">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="visibility" value="public" class="text-brand-500 focus:ring-brand-500" @checked(old('visibility', $e->visibility ?? 'public') === 'public')>
                            Public
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="visibility" value="private" class="text-brand-500 focus:ring-brand-500" @checked(old('visibility', $e->visibility ?? '') === 'private')>
                            Private
                        </label>
                    </div>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    Your event will be reviewed by an admin before publication.
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('organizer.events.index') }}" class="inline-flex justify-center rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="btn-gradient px-6 py-2.5 text-sm">Save changes</button>
        </div>
    </form>
@endsection
