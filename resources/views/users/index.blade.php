@extends('layouts.dashboard')

@section('title', 'User')

@section('content')
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><p class="text-sm text-slate-600">Akun pengguna sistem.</p><a href="{{ route('users.create') }}" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 sm:w-auto">Tambah User</a></div>
    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-600"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Unit</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr><td class="px-4 py-3 font-medium">{{ $user->name }}</td><td class="px-4 py-3">{{ $user->username }}</td><td class="px-4 py-3">{{ $user->email }}</td><td class="px-4 py-3">{{ match ($user->role) { 'pengelola' => 'Laboran / Pengelola', 'pimpinan' => 'Kaprodi', default => ucfirst($user->role) } }}</td><td class="px-4 py-3">{{ $user->unit?->name ?? '-' }}</td><td class="px-4 py-3 capitalize">{{ $user->status }}</td><td class="px-4 py-3">@include('partials.actions', ['edit' => route('users.edit', $user), 'delete' => route('users.destroy', $user)])</td></tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $users->links() }}</div>
@endsection
