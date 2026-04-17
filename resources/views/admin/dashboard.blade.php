@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Admin Dashboard</h1>
    <p class="mt-1 text-slate-600">Platform overview and pending validations.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stats-card label="Total events" :value="$stats['events'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">📅</span></x-slot>
        </x-stats-card>
        <x-stats-card label="Total users" :value="$stats['users'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">👥</span></x-slot>
        </x-stats-card>
        <x-stats-card label="Pending validations" :value="$stats['pending'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">⏳</span></x-slot>
        </x-stats-card>
        <x-stats-card label="Total partners" :value="$stats['partners'] ?? '0'">
            <x-slot name="icon"><span class="text-lg">🤝</span></x-slot>
        </x-stats-card>
    </div>

    <div class="mt-10 grid gap-8 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Events per month</h2>
            <div class="mt-4 h-64">
                <canvas id="adminBarChart" aria-label="Bar chart events per month"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Events by category</h2>
            <div class="mx-auto mt-4 h-64 max-w-sm">
                <canvas id="adminPieChart" aria-label="Pie chart by category"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-10 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Pending events (requires action)</h2>
        </div>
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Organizer</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingEvents ?? [] as $e)
                        @php $row = is_object($e) ? $e : (object) $e; @endphp
                        <tr>
                            <td class="px-6 py-3 font-medium text-slate-900">{{ $row->title }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $row->organizer_name ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ isset($row->created_at) ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.events.approve', $row->id ?? 1) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700">Approve ✓</button>
                                </form>
                                <span class="mx-2 text-slate-300">|</span>
                                <a href="{{ route('admin.events.show', $row->id ?? 1) }}" class="font-semibold text-slate-600 hover:text-slate-900">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">No pending events.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-10 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Recent registrations</h2>
        </div>
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentRegistrations ?? [] as $r)
                        @php $row = is_object($r) ? $r : (object) $r; @endphp
                        <tr>
                            <td class="px-6 py-3">{{ $row->user ?? '—' }}</td>
                            <td class="px-6 py-3">{{ $row->event ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $row->date ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500">No recent registrations.</td>
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

    const barCtx = document.getElementById('adminBarChart');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($chartMonths ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']),
                datasets: [{
                    label: 'Events',
                    data: @json($chartMonthValues ?? [4, 7, 5, 12, 9, 14]),
                    backgroundColor: 'rgba(108, 63, 197, 0.75)',
                    borderRadius: 8,
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
    }

    const pieCtx = document.getElementById('adminPieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: @json($pieLabels ?? ['Professional', 'Cultural', 'Corporate']),
                datasets: [{
                    data: @json($pieValues ?? [45, 30, 25]),
                    backgroundColor: ['#6C3FC5', '#8B5CF6', '#3B82F6'],
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
