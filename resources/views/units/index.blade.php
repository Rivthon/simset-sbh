@extends('layouts.dashboard')

@section('title', 'Unit')

@section('content')
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-600">Daftar unit kerja pemilik aset.</p>
        <a href="{{ route('units.create') }}" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 sm:w-auto">Tambah Unit</a>
    </div>

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-600">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kode Unit</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($units as $unit)
                    <tr>
                        <td class="px-4 py-3">{{ $unit->name }}</td>
                        <td class="px-4 py-3 font-medium">{{ $unit->code }}</td>
                        <td class="px-4 py-3 capitalize">{{ $unit->status }}</td>
                        <td class="px-4 py-3">@include('partials.actions', ['edit' => route('units.edit', $unit), 'delete' => route('units.destroy', $unit)])</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada unit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $units->links() }}</div>
@endsection
