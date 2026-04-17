@extends('layouts.organizer')

@section('title', 'Edit Event')

@section('content')
    @php
        $e = is_object($event ?? null) ? $event : (object) ($event ?? []);
    @endphp

    <h1 class="text-2xl font-bold text-white">Edit Event</h1>
    <p class="mt-1 text-slate-400">Update details and resubmit if needed.</p>

    <form method="POST" action="{{ route('organizer.events.update', $e->id ?? 1) }}"
          enctype="multipart/form-data" class="mt-8 space-y-8"
          x-data="{ preview: null }">
        @csrf
        @method('PUT')

        <div class="grid gap-8 lg:grid-cols-2">

            {{-- LEFT COLUMN --}}
            <div class="space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-300">Title</label>
                    <input id="title" name="title" type="text"
                           value="{{ old('title', $e->title ?? '') }}" required
                           class="mt-1 w-full rounded-xl border px-3 py-2.5 text-white
                                  {{ $errors->has('title') ? 'border-red-500 ring-2 ring-red-500/20' : 'border-white/10' }}"
                           style="background-color: #1a1a2e;">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300">Description</label>
                    <textarea id="description" name="description" rows="6"
                              class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white"
                              style="background-color: #1a1a2e;">{{ old('description', $e->description ?? '') }}</textarea>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-slate-300">Category</label>
                    <select id="category" name="category"
                            class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white"
                            style="background-color: #1a1a2e;">
                        @foreach (['professional' => 'Professional', 'cultural' => 'Cultural', 'corporate' => 'Corporate'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('category', $e->category ?? '') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-slate-300">Date &amp; time</label>
                    <input id="date" name="date" type="datetime-local"
                           value="{{ old('date', isset($e->date) ? \Carbon\Carbon::parse($e->date)->format('Y-m-d\TH:i') : '') }}"
                           class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white"
                           style="background-color: #1a1a2e;">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-slate-300">Location</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">📍</span>
                        <input id="location" name="location" type="text"
                               value="{{ old('location', $e->location ?? '') }}"
                               class="w-full rounded-xl border border-white/10 py-2.5 pl-10 pr-3 text-white"
                               style="background-color: #1a1a2e;">
                    </div>
                </div>

                <div>
                    <label for="capacity" class="block text-sm font-medium text-slate-300">Capacity</label>
                    <input id="capacity" name="capacity" type="number" min="1"
                           value="{{ old('capacity', $e->capacity ?? 50) }}"
                           class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white"
                           style="background-color: #1a1a2e;">
                    <p class="mt-1 text-xs text-slate-500">Max number of participants</p>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="space-y-5">
                <div>
                    <span class="block text-sm font-medium text-slate-300">Cover image</span>
                    <label class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-[#29ABE2]/30 px-6 py-10 transition hover:border-[#29ABE2]/60 hover:bg-[#29ABE2]/5"
                           style="background-color: #1a1a2e;">
                        <input type="file" name="cover" accept="image/*" class="sr-only"
                               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                        <div x-show="!preview" class="flex flex-col items-center gap-2 text-center">
                            <span class="text-3xl">🖼️</span>
                            <span class="text-sm text-slate-400">Drag &amp; drop or click to replace</span>
                            <span class="text-xs text-slate-500">PNG, JPG up to 5MB</span>
                        </div>
                        <img x-show="preview" x-bind:src="preview" alt=""
                             class="mt-2 max-h-48 rounded-lg object-cover shadow" x-cloak>
                    </label>
                </div>

                <div>
                    <label for="partners" class="block text-sm font-medium text-slate-300">Partner association</label>
                    <input id="partners" name="partners" type="text"
                           value="{{ old('partners', $e->partners ?? '') }}"
                           placeholder="Comma separated"
                           class="mt-1 w-full rounded-xl border border-white/10 px-3 py-2.5 text-white placeholder-slate-500"
                           style="background-color: #1a1a2e;">
                </div>

                <div>
                    <span class="block text-sm font-medium text-slate-300">Visibility</span>
                    <div class="mt-3 flex gap-4">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-white/10 px-4 py-3 transition hover:border-[#29ABE2]/50"
                               style="background-color: #1a1a2e;">
                            <input type="radio" name="visibility" value="public"
                                   class="accent-[#29ABE2]"
                                   @checked(old('visibility', $e->visibility ?? 'public') === 'public')>
                            <div>
                                <p class="text-sm font-semibold text-white">🌐 Public</p>
                                <p class="text-xs text-slate-400">Visible to everyone</p>
                            </div>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-white/10 px-4 py-3 transition hover:border-[#29ABE2]/50"
                               style="background-color: #1a1a2e;">
                            <input type="radio" name="visibility" value="private"
                                   class="accent-[#29ABE2]"
                                   @checked(old('visibility', $e->visibility ?? '') === 'private')>
                            <div>
                                <p class="text-sm font-semibold text-white">🔒 Private</p>
                                <p class="text-xs text-slate-400">Invite only</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="rounded-xl border border-[#29ABE2]/30 bg-[#29ABE2]/10 px-4 py-3 text-sm text-[#29ABE2]">
                    ⚠️ Your event will be reviewed by an admin before publication.
                </div>
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="flex flex-col-reverse gap-3 border-t border-white/10 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('organizer.events.index') }}"
               class="inline-flex justify-center rounded-xl border border-white/10 px-5 py-2.5 text-sm font-semibold text-slate-300 hover:bg-white/5 transition-all">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex justify-center rounded-xl px-6 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
                    style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                Save changes
            </button>
        </div>
    </form>
@endsection