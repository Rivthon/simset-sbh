<div class="flex flex-wrap items-center gap-2">
    @isset($show)
        <a href="{{ $show }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:border-slate-300 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
            <x-nav-icon name="view" />
            Detail
        </a>
    @endisset
    @isset($edit)
        <a href="{{ $edit }}" class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm hover:bg-blue-100">
            <x-nav-icon name="edit" />
            Edit
        </a>
    @endisset
    @isset($delete)
        <form method="POST" action="{{ $delete }}" onsubmit="return confirm('Yakin hapus data ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 shadow-sm hover:bg-red-100">
                <x-nav-icon name="delete" />
                Hapus
            </button>
        </form>
    @endisset
</div>
