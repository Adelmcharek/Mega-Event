@extends('layouts.admin')

@section('title', __('app.users_management'))

@section('content')
    <h1 class="text-2xl font-bold text-white">{{ __('app.users_management') }}</h1>
    <p class="mt-1 text-slate-400">{{ __('app.users_subtitle') }}</p>

    @php
        $roleLabels = [
            'admin'        => __('app.role_admin'),
            'organisateur' => __('app.role_organizer'),
            'participant'  => __('app.role_participant'),
        ];
    @endphp

    <div class="mt-8">
        <div class="rounded-2xl border border-white/10 overflow-hidden" style="background-color: #1a1a2e;">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background-color: #0f0f1a; border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('app.name') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('app.email') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('app.role') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('app.joined') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('app.status') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($users as $user)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-white text-sm flex-shrink-0"
                                     style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-medium text-white">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @php
                                $roleColors = [
                                    'admin'        => 'background-color:#ef444420; color:#ef4444; border:1px solid #ef444440;',
                                    'organisateur' => 'background-color:#8b5cf620; color:#8b5cf6; border:1px solid #8b5cf640;',
                                    'participant'  => 'background-color:#29ABE220; color:#29ABE2; border:1px solid #29ABE240;',
                                ];
                                $style = $roleColors[$user->role] ?? '';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                                  style="{{ $style }}">
                                {{ $roleLabels[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($user->status === 'active')
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                                      style="background-color:#10b98120; color:#10b981; border:1px solid #10b98140;">
                                    {{ __('app.active') }}
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                                      style="background-color:#ef444420; color:#ef4444; border:1px solid #ef444440;">
                                    {{ __('app.inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                          <div class="flex items-center justify-end gap-3"
                               x-data="{ open: false, role: '{{ $user->role }}' }">

                            {{-- Edit Role Button --}}
                            <button @click="open = true"
                                    class="text-sm font-semibold text-[#29ABE2] hover:text-white transition-colors">
                              {{ __('app.edit_role') }}
                            </button>

                            {{-- Role Edit Modal --}}
                            <div x-show="open" x-cloak
                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
                              <div class="rounded-2xl border border-white/10 p-6 w-80 shadow-2xl"
                                   style="background-color: #1a1a2e;">
                                <h3 class="text-lg font-bold text-white mb-4">
                                  {{ __('app.edit_role_of', ['name' => $user->name]) }}
                                </h3>
                                <form method="POST" action="{{ route('admin.users.updateRole', $user) }}">
                                  @csrf @method('PATCH')
                                  <select name="role"
                                          class="w-full rounded-xl border border-white/10 px-3 py-2.5 text-white mb-4"
                                          style="background-color: #0f0f1a;">
                                    <option value="participant"   @selected($user->role === 'participant')>{{ __('app.role_participant') }}</option>
                                    <option value="organisateur"  @selected($user->role === 'organisateur')>{{ __('app.role_organizer') }}</option>
                                    <option value="admin"         @selected($user->role === 'admin')>{{ __('app.role_admin') }}</option>
                                  </select>
                                  <div class="flex gap-3">
                                    <button type="button" @click="open = false"
                                            class="flex-1 rounded-xl border border-white/10 py-2 text-sm text-slate-300 hover:bg-white/5 transition">
                                      {{ __('app.cancel') }}
                                    </button>
                                    <button type="submit"
                                            class="flex-1 rounded-xl py-2 text-sm font-bold text-white transition hover:scale-105"
                                            style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                      {{ __('app.save') }}
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>

                            <span class="text-white/20">|</span>

                            {{-- Toggle Status --}}
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                              @csrf @method('PATCH')
                              <button type="submit"
                                      class="text-sm font-semibold transition-colors
                                      {{ $user->status === 'active' ? 'text-red-400 hover:text-red-300' : 'text-emerald-400 hover:text-emerald-300' }}">
                                {{ $user->status === 'active' ? __('app.deactivate') : __('app.activate') }}
                              </button>
                            </form>

                            @if($user->id !== auth()->id())
                            <span class="text-white/20">|</span>

                            <div x-data="{ open: false }">
                              <button @click="open = true"
                                      class="text-sm font-semibold text-red-400 hover:text-red-300 transition">
                                {{ __('app.delete') }}
                              </button>

                              <div x-show="open" x-cloak
                                   class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
                                <div class="w-full max-w-sm rounded-2xl border border-red-500/30 p-6 shadow-2xl"
                                     style="background-color: #1a1a2e;">

                                  <div class="text-center mb-6">
                                    <div class="text-4xl mb-3">🗑️</div>
                                    <h3 class="text-lg font-bold text-white mb-2">{{ __('app.delete') }}?</h3>
                                    <p class="text-slate-400 text-sm">
                                      {{ __('app.cannot_undo') }}
                                      <span class="text-white font-semibold">{{ $user->name }}</span>
                                    </p>
                                  </div>

                                  <div class="flex gap-3">
                                    <button @click="open = false"
                                            class="flex-1 rounded-xl border border-white/10 py-2.5
                                                   text-sm font-semibold text-slate-300 hover:bg-white/5 transition">
                                      {{ __('app.cancel') }}
                                    </button>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="flex-1">
                                      @csrf @method('DELETE')
                                      <button type="submit"
                                              class="w-full rounded-xl py-2.5 text-sm font-bold text-white transition"
                                              style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                        {{ __('app.delete') }}
                                      </button>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </div>
                            @endif
                          </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
