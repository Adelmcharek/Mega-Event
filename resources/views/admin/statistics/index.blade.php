@extends('layouts.admin')

@section('title', 'Statistics')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Statistics</h1>
    <p class="mt-1 text-slate-600">KPIs and registration trends.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stats-card label="Total events" :value="$kpis['events'] ?? '—'" />
        <x-stats-card label="Avg. participants / event" :value="$kpis['avg_participants'] ?? '—'" />
        <x-stats-card label="Top location" :value="$kpis['top_location'] ?? '—'" />
        <x-stats-card label="Busiest month" :value="$kpis['busiest_month'] ?? '—'" />
    </div>

    <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Registrations over time</h2>
        <div class="mt-4 h-72">
            <canvas id="regLineChart"></canvas>
        </div>
    </div>

    <div class="mt-10 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-slate-900">Top 5 most popular events</h2>
            <button type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Export report (placeholder)</button>
        </div>
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Registrations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($topEvents ?? [] as $i => $ev)
                        @php $row = is_object($ev) ? $ev : (object) $ev; @endphp
                        <tr>
                            <td class="px-6 py-3 text-slate-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-3 font-medium text-slate-900">{{ $row->title }}</td>
                            <td class="px-6 py-3 text-brand-600 font-semibold">{{ $row->registered ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;
    const el = document.getElementById('regLineChart');
    if (!el) return;
    new Chart(el, {
        type: 'line',
        data: {
            labels: @json($lineLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']),
            datasets: [{
                label: 'Registrations',
                data: @json($lineValues ?? [120, 190, 150, 220, 280, 310]),
                borderColor: '#6C3FC5',
                backgroundColor: 'rgba(108, 63, 197, 0.15)',
                fill: true,
                tension: 0.35,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' } },
                x: { grid: { display: false } },
            },
        },
    });
});
</script>
@endpush
