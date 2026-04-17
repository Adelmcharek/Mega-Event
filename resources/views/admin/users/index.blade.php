@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <h1 class="text-2xl font-bold text-slate-900">Users Management</h1>
    <p class="mt-1 text-slate-600">Roles, access, and account status.</p>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="-mx-4 overflow-x-auto sm:mx-0">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Joined</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users ?? [] as $u)
                        @php $row = is_object($u) ? $u : (object) $u; @endphp
                        <tr>
                            <td class="px-6 py-3 font-medium text-slate-900">{{ $row->name }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ $row->email }}</td>
                            <td class="px-6 py-3">
                                @php $roleLabel = match ($row->role ?? '') {
                                    'admin' => 'ADMIN',
                                    'organizer' => 'ORGANISATEUR',
                                    default => 'PARTICIPANT',
                                }; @endphp
                                <x-status-badge :status="$roleLabel" />
                            </td>
                            <td class="px-6 py-3 text-slate-600">{{ isset($row->created_at) ? \Carbon\Carbon::parse($row->created_at)->format('M d, Y') : '—' }}</td>
                            <td class="px-6 py-3">
                                <x-status-badge :status="($row->is_active ?? true) ? 'ACTIVE' : 'INACTIVE'" />
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <button type="button" class="font-semibold text-brand-500 hover:text-brand-600" @click="alert('Edit role — connect modal')">Edit Role</button>
                                <span class="mx-2 text-slate-300">|</span>
                                <button type="button" class="font-semibold text-red-600 hover:text-red-700" @click="alert('Deactivate — connect backend')">Deactivate</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
