@extends('layouts.dashboard')

@section('title', 'Kategori')

@section('content')
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-600">Klasifikasi aset.</p>
        @if (auth()->user()->canManageData())
            <a href="{{ route('categories.create') }}" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 sm:w-auto">Tambah Kategori</a>
        @endif
    </div>

    <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-600"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Unit</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3">{{ $category->name }}</td>
                        <td class="px-4 py-3">{{ $category->unit?->name ?? 'Global' }}</td>
                        <td class="px-4 py-3 capitalize">{{ $category->status }}</td>
                        <td class="px-4 py-3">
                            @if (auth()->user()->isAdmin() || (auth()->user()->isPengelola() && $category->unit_id === auth()->user()->unit_id))
                                @include('partials.actions', ['edit' => route('categories.edit', $category), 'delete' => route('categories.destroy', $category)])
                            @else
                                <span class="text-xs text-slate-400">{{ $category->unit_id ? 'Read-only' : 'Global' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $categories->links() }}</div>
@endsection
