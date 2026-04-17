@extends('layouts.admin')

@section('title', 'Statistics')

@section('content')
    <h1 class="text-2xl font-bold text-white">Statistics</h1>
    <p class="mt-1 text-slate-400">KPIs and registration trends.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stats-card label="Total events"       :value="$kpis['events'] ?? '—'" />
        <x-stats-card label="Total registrations" :value="$kpis['registrations'] ?? '—'" />
        <x-stats-card label="Avg. fill rate"     :value="$kpis['avg_fill_rate'] ?? '—'" />
        <x-stats-card label="Top category"       :value="$kpis['top_category'] ?? '—'" />
    </div>

    <div class="mt-10 rounded-2xl border border-white/10 bg-[#1a1a2e] p-6 shadow-sm">
        <h2 class="text-lg font-bold text-white">Registrations over time</h2>
        <div class="mt-4 h-72">
            <canvas id="regLineChart" aria-label="Line chart registrations over time"></canvas>
        </div>
    </div>

    <div class="mt-10 rounded-2xl border border-white/10 bg-[#1a1a2e] shadow-sm">
        <div class="border-b border-white/10 px-6 py-4">
            <h2 class="text-lg font-bold text-white">Top 5 most popular events</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-white/5 text-xs font-semibold uppercase text-slate-400">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Registrations</th>
                        <th class="px-6 py-3">Capacity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($topEvents ?? [] as $i => $ev)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-3 text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-3 font-medium text-white">{{ $ev->title }}</td>
                            <td class="px-6 py-3 font-semibold text-[#29ABE2]">{{ $ev->registrations_count ?? 0 }}</td>
                            <td class="px-6 py-3 text-slate-400">{{ $ev->capacity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">No data yet.</td>
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
    if (typeof Chart === 'undefined') return;

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';

    const el = document.getElementById('regLineChart');
    if (!el) return;
    new Chart(el, {
        type: 'line',
        data: {
            labels: @json($lineLabels ?? []),
            datasets: [{
                label: 'Registrations',
                data: @json($lineValues ?? []),
                borderColor: 'rgba(41, 171, 226, 0.8)',
                backgroundColor: 'rgba(41, 171, 226, 0.15)',
                fill: true,
                tension: 0.35,
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
});
</script>
@endpush
