@extends('layouts.organizer')

@section('title', 'Edit Event')

@section('content')
    @php
        $e = is_object($event ?? null) ? $event : (object) ($event ?? []);
    @endphp

    <h1 class="text-2xl font-bold text-white">{{ __('app.edit') }} {{ __('app.event') }}</h1>
    <p class="mt-1 text-slate-400">{{ __('app.approval_warning') }}</p>

    <form method="POST" action="{{ route('organizer.events.update', $e->id ?? 1) }}"
          enctype="multipart/form-data" class="mt-8"
          x-data="{
            preview: null,
            partners: {{ json_encode(($event->partners ?? collect())->map(fn($p) => [
                'id'      => $p->id,
                'name'    => $p->name,
                'website' => $p->website ?? '',
                'email'   => $p->email ?? '',
                'phone'   => $p->phone ?? '',
            ])->values()->toArray()) }},
            addPartner() { this.partners.push({ id: null, name: '', website: '', email: '', phone: '' }) },
            removePartner(index) { this.partners.splice(index, 1) }
          }">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: 2/3 width --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- SECTION 1: Event Details --}}
                <div class="rounded-2xl border border-white/10 p-6" style="background-color: #1a1a2e;">
                    <h3 class="text-base font-bold text-white mb-4 pb-3 border-b border-white/10 flex items-center gap-2">
                        📋 {{ __('app.event_details') }}
                    </h3>
                    <div class="space-y-4">

                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.event_title') }}</label>
                            <input id="title" name="title" type="text"
                                   value="{{ old('title', $e->title ?? '') }}" required
                                   class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition
                                          {{ $errors->has('title') ? 'border-red-500 ring-1 ring-red-500' : '' }}"
                                   style="background-color: #0f0f1a;">
                            @error('title')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.event_desc') }}</label>
                            <textarea id="description" name="description" rows="5"
                                      class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                      style="background-color: #0f0f1a;">{{ old('description', $e->description ?? '') }}</textarea>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.event_category') }}</label>
                            <select id="category" name="category"
                                    class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                    style="background-color: #0f0f1a;">
                                @foreach ([
                                    'professional' => __('app.cat_professional'),
                                    'cultural'     => __('app.cat_cultural'),
                                    'corporate'    => __('app.cat_corporate'),
                                    'educational'  => __('app.cat_educational'),
                                    'social'       => __('app.cat_social'),
                                    'sports'       => __('app.cat_sports'),
                                ] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('category', $e->category ?? '') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.event_date') }}</label>
                            <input id="date" name="date" type="datetime-local"
                                   value="{{ old('date', isset($e->date) ? \Carbon\Carbon::parse($e->date)->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                   style="background-color: #0f0f1a;">
                        </div>

                    </div>
                </div>

                {{-- SECTION 2: Location & Capacity --}}
                <div class="rounded-2xl border border-white/10 p-6" style="background-color: #1a1a2e;">
                    <h3 class="text-base font-bold text-white mb-4 pb-3 border-b border-white/10 flex items-center gap-2">
                        📍 {{ __('app.event_location') }} &amp; {{ __('app.event_capacity') }}
                    </h3>
                    <div class="space-y-4">

                        <div x-data="{
                          address: '{{ old('location', $e->location ?? '') }}',
                          lat: {{ old('lat', $e->lat ?? 36.8665) }},
                          lng: {{ old('lng', $e->lng ?? 10.1647) }},
                          initMap() {
                            const map = L.map('location-map').setView([this.lat, this.lng], 13);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                              attribution: '© OpenStreetMap'
                            }).addTo(map);
                            let marker = L.marker([this.lat, this.lng], {draggable: true}).addTo(map);
                            const self = this;
                            map.on('click', function(e) {
                              marker.setLatLng(e.latlng);
                              self.lat = e.latlng.lat;
                              self.lng = e.latlng.lng;
                              fetch('https://nominatim.openstreetmap.org/reverse?lat='+e.latlng.lat+'&lon='+e.latlng.lng+'&format=json')
                                .then(r => r.json())
                                .then(data => { self.address = data.display_name; });
                            });
                            marker.on('dragend', function(e) {
                              const pos = marker.getLatLng();
                              self.lat = pos.lat;
                              self.lng = pos.lng;
                              fetch('https://nominatim.openstreetmap.org/reverse?lat='+pos.lat+'&lon='+pos.lng+'&format=json')
                                .then(r => r.json())
                                .then(data => { self.address = data.display_name; });
                            });
                          }
                        }" x-init="initMap()">
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.event_location') }}</label>
                            <input type="text" name="location" x-model="address"
                                   placeholder="Click on map or type address..."
                                   class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition mb-2"
                                   style="background-color: #0f0f1a;">
                            <input type="hidden" name="lat" x-model="lat">
                            <input type="hidden" name="lng" x-model="lng">
                            <div id="location-map"
                                 class="w-full rounded-xl border border-white/10 overflow-hidden"
                                 style="height: 300px; z-index: 1;"></div>
                            <p class="text-xs text-slate-500 mt-1">📍 {{ __('app.click_map') }}</p>
                        </div>

                        <div>
                            <label for="capacity" class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.event_capacity') }}</label>
                            <input id="capacity" name="capacity" type="number" min="1"
                                   value="{{ old('capacity', $e->capacity ?? 50) }}"
                                   class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-white focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                   style="background-color: #0f0f1a;">
                            <p class="text-xs text-slate-500 mt-1">{{ __('app.max_participants') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">
                                {{ __('app.event_price') }} <span class="text-slate-500 font-normal">({{ __('app.leave_zero_free') }})</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 font-semibold text-sm">TND</span>
                                <input type="number" name="price" min="0" step="0.01"
                                       value="{{ old('price', $e->price ?? 0) }}"
                                       class="w-full rounded-xl border border-white/10 pl-14 pr-4 py-2.5 text-white focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                       style="background-color: #0f0f1a;">
                            </div>
                            <p class="text-xs text-slate-500 mt-1">{{ __('app.set_zero_free') }}</p>
                        </div>

                    </div>
                </div>

            </div>

            {{-- RIGHT: 1/3 width --}}
            <div class="space-y-6">

                {{-- SECTION 3: Media & Settings --}}
                <div class="rounded-2xl border border-white/10 p-6" style="background-color: #1a1a2e;">
                    <h3 class="text-base font-bold text-white mb-4 pb-3 border-b border-white/10 flex items-center gap-2">
                        🖼️ {{ __('app.profile_photo') }} &amp; {{ __('app.event_visibility') }}
                    </h3>
                    <div class="space-y-4">

                        <div>
                            <span class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.profile_photo') }}</span>
                            <label class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-[#29ABE2]/30 px-6 py-8 transition hover:border-[#29ABE2]/60 hover:bg-[#29ABE2]/5"
                                   style="background-color: #0f0f1a;">
                                <input type="file" name="cover" accept="image/*" class="sr-only"
                                       @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                                <div x-show="!preview" class="flex flex-col items-center gap-2 text-center">
                                    <span class="text-3xl">🖼️</span>
                                    <span class="text-sm text-slate-400">{{ __('app.profile_photo') }}</span>
                                    <span class="text-xs text-slate-500">PNG, JPG up to 5MB</span>
                                </div>
                                <img x-show="preview" x-bind:src="preview" alt=""
                                     class="mt-2 max-h-48 rounded-lg object-cover shadow" x-cloak>
                            </label>
                        </div>

                        <div>
                            <span class="block text-sm font-medium text-slate-300 mb-1.5">{{ __('app.event_visibility') }}</span>
                            <div class="flex flex-col gap-3">
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-white/10 px-4 py-3 transition hover:border-[#29ABE2]/50"
                                       style="background-color: #0f0f1a;">
                                    <input type="radio" name="visibility" value="public"
                                           class="accent-[#29ABE2]"
                                           @checked(old('visibility', $e->visibility ?? 'public') === 'public')>
                                    <div>
                                        <p class="text-sm font-semibold text-white">🌐 {{ __('app.event_public') }}</p>
                                        <p class="text-xs text-slate-400">Visible to everyone</p>
                                    </div>
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-white/10 px-4 py-3 transition hover:border-[#29ABE2]/50"
                                       style="background-color: #0f0f1a;">
                                    <input type="radio" name="visibility" value="private"
                                           class="accent-[#29ABE2]"
                                           @checked(old('visibility', $e->visibility ?? '') === 'private')>
                                    <div>
                                        <p class="text-sm font-semibold text-white">🔒 {{ __('app.event_private') }}</p>
                                        <p class="text-xs text-slate-400">{{ __('app.event_private') }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-400">
                            ⚠️ {{ __('app.approval_warning') }}
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- SECTION 4: Partners — full width --}}
        <div class="mt-6 rounded-2xl border border-white/10 p-6" style="background-color: #1a1a2e;">
            <h3 class="text-base font-bold text-white mb-4 pb-3 border-b border-white/10 flex items-center gap-2">
                🤝 {{ __('app.event_partners') }}
            </h3>
            <p class="text-xs text-slate-500 mb-4">ℹ️ {{ __('app.partners_note') }}</p>

            <div x-show="partners.length === 0" class="rounded-xl border border-dashed border-white/10 px-4 py-6 text-center text-sm text-slate-500">
                {{ __('app.no_partners') }}
            </div>

            <template x-for="(partner, index) in partners" :key="index">
                <div class="border border-white/10 rounded-xl p-4 mb-3" style="background-color: #0f0f23;">
                    <input type="hidden" x-bind:name="`partners[${index}][id]`" x-bind:value="partner.id ?? ''">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">{{ __('app.partner_name') }} <span class="text-red-400">*</span></label>
                            <input type="text" x-bind:name="`partners[${index}][name]`" x-model="partner.name" required
                                   class="w-full rounded-xl border border-white/10 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                   style="background-color: #1a1a2e;" placeholder="Ex: TechCorp">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">{{ __('app.website') }}</label>
                            <input type="text" x-bind:name="`partners[${index}][website]`" x-model="partner.website"
                                   class="w-full rounded-xl border border-white/10 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                   style="background-color: #1a1a2e;" placeholder="https://...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">{{ __('app.email') }}</label>
                            <input type="email" x-bind:name="`partners[${index}][email]`" x-model="partner.email"
                                   class="w-full rounded-xl border border-white/10 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                   style="background-color: #1a1a2e;" placeholder="contact@...">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">{{ __('app.phone') }}</label>
                            <input type="text" x-bind:name="`partners[${index}][phone]`" x-model="partner.phone"
                                   class="w-full rounded-xl border border-white/10 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-[#29ABE2] focus:ring-1 focus:ring-[#29ABE2] transition"
                                   style="background-color: #1a1a2e;" placeholder="+216...">
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="button" @click="removePartner(index)"
                                class="inline-flex items-center gap-1 rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-400 transition-all hover:bg-red-500/20">
                            🗑️ {{ __('app.remove') }}
                        </button>
                    </div>
                </div>
            </template>

            <button type="button" @click="addPartner()"
                    class="mt-2 inline-flex items-center gap-2 rounded-xl border border-[#29ABE2]/40 bg-[#29ABE2]/10 px-4 py-2 text-sm font-semibold text-[#29ABE2] transition-all hover:bg-[#29ABE2]/20">
                + {{ __('app.add_partner') }}
            </button>
        </div>

        {{-- SUBMIT BAR — full width --}}
        <div class="mt-6 flex justify-between items-center rounded-2xl border border-white/10 px-6 py-4"
             style="background-color: #1a1a2e;">
            <a href="{{ route('organizer.events.index') }}"
               class="text-slate-400 hover:text-white transition text-sm font-semibold">
                ← {{ __('app.cancel') }}
            </a>
            <button type="submit"
                    class="rounded-xl px-8 py-3 font-bold text-white hover:scale-105 transition"
                    style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                {{ __('app.save_changes') }} →
            </button>
        </div>

    </form>
@endsection
