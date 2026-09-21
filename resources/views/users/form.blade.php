@extends('layouts.dashboard')

@section('title', $user->exists ? 'Edit User' : 'Tambah User')

@section('content')
    <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="max-w-3xl rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @if ($user->exists)
            @method('PUT')
        @endif
        <div class="grid gap-5 md:grid-cols-2">
            <div><label class="block text-sm font-medium text-slate-700">Nama</label><input name="name" value="{{ old('name', $user->name) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700">Username</label><input name="username" value="{{ old('username', $user->username) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700">Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700">Password {{ $user->exists ? '(kosongkan jika tidak diganti)' : '' }}</label><input type="password" name="password" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium text-slate-700">Role</label><select name="role" required class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm">@foreach (['admin' => 'Admin', 'pengelola' => 'Laboran / Pengelola', 'pimpinan' => 'Kaprodi'] as $role => $label)<option value="{{ $role }}" @selected(old('role', $user->role ?: 'pengelola') === $role)>{{ $label }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700">Unit</label><select name="unit_id" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"><option value="">Semua unit</option>@foreach ($units as $unit)<option value="{{ $unit->id }}" @selected((int) old('unit_id', $user->unit_id) === $unit->id)>{{ $unit->name }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-slate-700">Status</label><select name="status" class="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm"><option value="active" @selected(old('status', $user->status ?: 'active') === 'active')>Aktif</option><option value="inactive" @selected(old('status', $user->status) === 'inactive')>Nonaktif</option></select></div>
        </div>
        <div class="mt-6 flex gap-3"><button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan</button><a href="{{ route('users.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Batal</a></div>
    </form>
@endsection
