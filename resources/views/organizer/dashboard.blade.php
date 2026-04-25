@extends('layouts.organizer')

@section('title', __('app.dashboard'))

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('app.welcome_back') }}, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-400">{{ __('app.events_happening') }}</p>
        </div>
        <a href="{{ route('organizer.events.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all hover:scale-105 hover:shadow-lg hover:shadow-[#29ABE2]/25"
           style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
            {{ __('app.create_new_event') }}
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stats-card :label="__('app.total_events_created')" :value="$stats['total_events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">📅</span></x-slot>
        </x-stats-card>
        <x-stats-card :label="__('app.total_participants')" :value="$stats['total_participants'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">👥</span></x-slot>
        </x-stats-card>
        <x-stats-card :label="__('app.pending_approval')" :value="$stats['pending_events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">⏳</span></x-slot>
        </x-stats-card>
        <x-stats-card :label="__('app.published_events')" :value="$stats['published_events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">✅</span></x-slot>
        </x-stats-card>
    </div>

    {{-- Charts --}}
    <div class="mt-10 grid gap-6 lg:grid-cols-2">

        {{-- Chart 1: Inscriptions par mois --}}
        <div class="rounded-2xl border border-white/10 p-6 shadow-sm" style="background-color: #1a1a2e;">
            <h2 class="text-lg font-bold text-white mb-4">{{ __('app.registrations_per_month') }}</h2>
            <div style="height:300px; position:relative;">
                <canvas id="registrationsChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Événements par catégorie --}}
        <div class="rounded-2xl border border-white/10 p-6 shadow-sm" style="background-color: #1a1a2e;">
            <h2 class="text-lg font-bold text-white mb-4">{{ __('app.my_events_category') }}</h2>
            <div style="height:300px; position:relative;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Recent Events Table --}}
    <div class="mt-10 rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-lg font-bold text-white">{{ __('app.recent_events') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">{{ __('app.title') }}</th>
                        <th class="px-6 py-3">{{ __('app.date') }}</th>
                        <th class="px-6 py-3">{{ __('app.status') }}</th>
                        <th class="px-6 py-3">{{ __('app.participants') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recentEvents ?? [] as $row)
                        @php $ev = is_object($row) ? $row : (object) $row; @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-medium text-white">{{ $ev->title ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ isset($ev->date) ? \Carbon\Carbon::parse($ev->date)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$ev->status ?? 'DRAFT'" />
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $ev->registrations_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('organizer.events.edit', $ev->id ?? 1) }}"
                                   class="rounded-lg bg-[#29ABE2]/20 px-3 py-1 text-xs font-semibold text-[#29ABE2] border border-[#29ABE2]/30 hover:bg-[#29ABE2]/30 transition-all">
                                    {{ __('app.edit') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="text-4xl">📭</span>
                                    <p class="font-semibold text-white">{{ __('app.no_events_yet') }}</p>
                                    <p class="text-slate-400 text-sm">{{ __('app.create_first_event_hint') }}</p>
                                    <a href="{{ route('organizer.events.create') }}"
                                       class="mt-2 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-white"
                                       style="background: linear-gradient(135deg, #29ABE2, #0A7FA8);">
                                        + {{ __('app.create_event') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.1)';

    // Chart 1 — Inscriptions par mois (line)
    const ctx1 = document.getElementById('registrationsChart').getContext('2d');
    const gradient = ctx1.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(41,171,226,0.35)');
    gradient.addColorStop(1, 'rgba(41,171,226,0)');

    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: @json($chartLabels ?? []),
            datasets: [{
                label: '{{ __('app.registered') }}',
                data: @json($chartValues ?? []),
                borderColor: '#29ABE2',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#29ABE2',
                pointBorderColor: '#1a1a2e',
                pointBorderWidth: 2,
                pointRadius: 5,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    borderColor: 'rgba(41,171,226,0.3)',
                    borderWidth: 1,
                    titleColor: '#94a3b8',
                    bodyColor: '#29ABE2',
                    bodyFont: { weight: 'bold' },
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8', font: { size: 12 } },
                    border: { color: 'rgba(255,255,255,0.1)' },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: '#94a3b8', stepSize: 1, font: { size: 12 } },
                    border: { color: 'rgba(255,255,255,0.1)' },
                }
            }
        }
    });

    // Chart 2 — Événements par catégorie (doughnut)
    const categoryColors = {
        'professional': '#29ABE2',
        'cultural':     '#10b981',
        'corporate':    '#f59e0b',
        'educational':  '#3b82f6',
        'social':       '#ec4899',
        'sports':       '#ef4444',
        'other':        '#8b5cf6',
    };

    const rawCategories = @json($eventsByCategory ?? []);
    const catLabels = rawCategories.map(function(r) {
        const c = r.category || 'other';
        return c.charAt(0).toUpperCase() + c.slice(1);
    });
    const catValues = rawCategories.map(function(r) { return r.total; });
    const catColors = rawCategories.map(function(r) {
        const key = (r.category || 'other').toLowerCase();
        return categoryColors[key] || '#29ABE2';
    });

    const ctx2 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catValues,
                backgroundColor: catColors,
                borderColor: '#1a1a2e',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#94a3b8',
                        padding: 16,
                        font: { size: 12 },
                        usePointStyle: true,
                    }
                },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    borderColor: 'rgba(41,171,226,0.3)',
                    borderWidth: 1,
                    titleColor: '#94a3b8',
                    bodyColor: '#29ABE2',
                    bodyFont: { weight: 'bold' },
                }
            }
        }
    });
});
</script>
@endpush