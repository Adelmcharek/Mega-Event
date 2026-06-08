@extends('layouts.admin')

@section('title', __('app.dashboard'))

@section('content')
    <div>
        <h1 class="text-2xl font-bold text-white">{{ __('app.admin_dashboard') }}</h1>
        <p class="mt-1 text-slate-400">{{ __('app.platform_overview') }}</p>
    </div>

    {{-- Stats Cards --}}
    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stats-card :label="__('app.total_events')" :value="$stats['events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">📅</span></x-slot>
        </x-stats-card>
        <x-stats-card :label="__('app.total_users')" :value="$stats['users'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">👥</span></x-slot>
        </x-stats-card>
        <x-stats-card :label="__('app.pending_validations')" :value="$stats['pending'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">⏳</span></x-slot>
        </x-stats-card>
        <x-stats-card :label="__('app.total_partners')" :value="$stats['partners'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">🤝</span></x-slot>
        </x-stats-card>
    </div>

    {{-- Charts --}}
    <div class="mt-10 grid gap-8 lg:grid-cols-2">
        <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] p-6 shadow-sm">
            <h2 class="text-lg font-bold text-white">{{ __('app.events_per_month') }}</h2>
            <div class="mt-4 h-64">
                <canvas id="adminBarChart" aria-label="Bar chart events per month"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-[#1a1a2e] p-6 shadow-sm">
            <h2 class="text-lg font-bold text-white">{{ __('app.events_by_category') }}</h2>
            <div class="mx-auto mt-4 h-64 max-w-sm">
                <canvas id="adminPieChart" aria-label="Pie chart by category"></canvas>
            </div>
        </div>
    </div>

    {{-- Pending Events --}}
    <div class="mt-10 rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-lg font-bold text-white">{{ __('app.pending_events_action') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('app.title') }}</th>
                        <th class="px-6 py-3">{{ __('app.organizer') }}</th>
                        <th class="px-6 py-3">{{ __('app.created') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($pendingEvents ?? [] as $e)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-3 font-medium text-white">{{ $e->title }}</td>
                            <td class="px-6 py-3 text-slate-400">{{ optional($e->organiser)->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-400">
                                {{ $e->created_at ? $e->created_at->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.events.approve', $e->id) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30 transition-all">
                                        {{ __('app.approve') }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.events.show', $e->id) }}"
                                    class="ml-2 rounded-lg bg-[#29ABE2]/20 px-3 py-1 text-xs font-semibold text-[#29ABE2] border border-[#29ABE2]/30 hover:bg-[#29ABE2]/30 transition-all">
                                    {{ __('app.review') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                {{ __('app.no_pending_events') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Registrations --}}
    <div class="mt-8 rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-lg font-bold text-white">{{ __('app.recent_registrations') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('app.user') }}</th>
                        <th class="px-6 py-3">{{ __('app.event') }}</th>
                        <th class="px-6 py-3">{{ __('app.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recentRegistrations ?? [] as $r)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-3 text-white">{{ optional($r->user)->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-400">{{ optional($r->event)->title ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-400">
                                {{ $r->registered_at ? $r->registered_at->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500">
                                {{ __('app.no_recent_registrations') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recommendations --}}
    <div class="mt-6 rounded-2xl border border-white/10 p-6"
         style="background-color: #1a1a2e;">
      <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
        🤖 {{ __('app.recommendations') }}
        <span class="text-xs font-normal text-slate-400 ml-2">
          {{ __('app.rec_powered_by') }}
        </span>
      </h2>
      @if(isset($recommendedEvents) && $recommendedEvents->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @foreach($recommendedEvents as $event)
            <a href="{{ route('events.show', $event) }}"
               class="block rounded-xl border border-white/10 p-4
                      hover:border-[#29ABE2]/40 hover:bg-white/5 transition-all">
              <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold mb-2"
                    style="background: #29ABE220; color: #29ABE2;">
                {{ ucfirst($event->category) }}
              </span>
              <h3 class="text-white font-semibold text-sm mb-1">{{ $event->title }}</h3>
              <p class="text-slate-400 text-xs mb-1">
                📅 {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
              </p>
              <p class="text-slate-400 text-xs mb-3">📍 {{ $event->location }}</p>
              <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500">
                  @if(isset($event->recommendation_reason) && $event->recommendation_reason === 'collaborative_filtering')
                    🤖 {{ __('app.rec_similar_users') }}
                  @else
                    🔥 {{ __('app.rec_popular') }}
                  @endif
                </span>
                @if($event->price > 0)
                  <span class="text-xs font-bold text-[#29ABE2]">{{ $event->price }} TND</span>
                @else
                  <span class="text-xs font-bold text-emerald-400">{{ __('app.event_free') }}</span>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      @else
        <p class="text-slate-400 text-sm">{{ __('app.no_recommendations') }}</p>
      @endif
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';

    const barCtx = document.getElementById('adminBarChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($chartMonths),
                datasets: [{
                    label: '{{ __('app.events') }}',
                    data: @json($chartMonthValues),
                    backgroundColor: 'rgba(41, 171, 226, 0.75)',
                    borderRadius: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { display: false } },
                },
            },
        });
    }

    const pieCtx = document.getElementById('adminPieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: @json($pieLabels),
                datasets: [{
                    data: @json($pieValues),
                    backgroundColor: ['#29ABE2', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#f97316'],
                    borderColor: ['#29ABE2', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#f97316'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
            },
        });
    }
});
</script>
@endpush