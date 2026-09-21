<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\ToolReplacementRequest;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ToolReplacementRequestController extends Controller
{
    public function create(Asset $asset): View
    {
        $asset->load(['unit', 'category', 'location', 'container']);

        return view('tool_replacements.public-form', [
            'asset' => $asset,
        ]);
    }

    public function store(Request $request, Asset $asset): RedirectResponse
    {
        $asset->load('unit');
        $maxQuantity = max(1, (int) ($asset->quantity ?? 1));

        $data = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'student_nim' => ['required', 'string', 'max:100'],
            'student_semester' => ['required', 'string', 'max:50'],
            'prodi_kelas' => ['nullable', 'string', 'max:150'],
            'practicum_name' => ['required', 'string', 'max:255'],
            'incident_date' => ['required', 'date'],
            'replacement_quantity' => ['required', 'integer', 'min:1', 'max:'.$maxQuantity],
            'damage_description' => ['nullable', 'string'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'damage_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [], [
            'student_name' => 'nama mahasiswa',
            'student_nim' => 'NIM',
            'student_semester' => 'Semester',
            'prodi_kelas' => 'prodi/kelas',
            'practicum_name' => 'nama praktikum',
            'incident_date' => 'tanggal kejadian',
            'replacement_quantity' => 'jumlah rusak/diganti',
            'damage_description' => 'keterangan kerusakan',
            'whatsapp_number' => 'nomor WhatsApp',
            'damage_photo' => 'foto kerusakan',
        ]);

        if ($request->hasFile('damage_photo')) {
            $data['damage_photo'] = $request->file('damage_photo')->store('tool-replacements', 'public');
        }

        $replacement = ToolReplacementRequest::create(array_merge($data, [
            'replacement_code' => ToolReplacementRequest::generateReplacementCode($asset->unit, $data['incident_date']),
            'asset_id' => $asset->id,
            'unit_id' => $asset->unit_id,
            'status' => 'menunggu_verifikasi',
        ]));

        return redirect()->route('tool-replacements.public.success', $replacement);
    }

    public function statusForm(): View
    {
        return view('tool_replacements.status-check');
    }

    public function statusCheck(Request $request): View
    {
        $data = $request->validate([
            'replacement_code' => ['required', 'string', 'max:100'],
            'student_nim' => ['required', 'string', 'max:100'],
        ], [], [
            'replacement_code' => 'kode penggantian',
            'student_nim' => 'NIM',
        ]);

        $code = strtoupper(trim($data['replacement_code']));
        $nim = trim($data['student_nim']);

        $replacement = ToolReplacementRequest::with(['asset', 'unit', 'verifier'])
            ->where('replacement_code', $code)
            ->where('student_nim', $nim)
            ->first();

        return view('tool_replacements.status-check', [
            'replacement' => $replacement,
            'searched' => true,
            'replacementCode' => $code,
            'studentNim' => $nim,
        ]);
    }

    public function success(ToolReplacementRequest $toolReplacement): View
    {
        return view('tool_replacements.success', [
            'replacement' => $toolReplacement->load(['asset', 'unit']),
        ]);
    }

    public function index(Request $request): View
    {
        $query = $this->filteredQuery($request)
            ->with(['asset.category', 'unit', 'verifier'])
            ->orderByDesc('id');

        return view('tool_replacements.index', [
            'replacements' => $query->paginate(12)->withQueryString(),
            'units' => Unit::where('status', 'active')
                ->when($request->user()->isUnitScoped(), fn (Builder $query) => $query->whereKey($request->user()->unit_id))
                ->orderBy('name')
                ->get(),
            'statusLabels' => ToolReplacementRequest::STATUS_OPTIONS,
        ]);
    }

    public function show(Request $request, ToolReplacementRequest $toolReplacement): View
    {
        $this->authorizeReplacement($request, $toolReplacement);

        return view('tool_replacements.show', [
            'replacement' => $toolReplacement->load(['asset.category', 'asset.location', 'asset.container', 'unit', 'verifier']),
            'statusLabels' => ToolReplacementRequest::STATUS_OPTIONS,
        ]);
    }

    public function updateStatus(Request $request, ToolReplacementRequest $toolReplacement): RedirectResponse
    {
        $this->authorizeReplacement($request, $toolReplacement);
        abort_if($request->user()->isPimpinan(), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ToolReplacementRequest::STATUS_OPTIONS))],
            'laboran_note' => ['nullable', 'string'],
            'rejection_reason' => ['nullable', 'required_if:status,ditolak', 'string'],
        ], [], [
            'status' => 'status',
            'laboran_note' => 'catatan laboran',
            'rejection_reason' => 'alasan penolakan',
        ]);

        $data['verified_by'] = $request->user()->id;
        $data['verified_at'] = now();

        if ($data['status'] === 'sudah_diganti') {
            $data['received_at'] = now();
            $data['rejection_reason'] = null;
        } elseif ($data['status'] === 'menunggu_verifikasi') {
            $data['received_at'] = null;
            $data['rejection_reason'] = null;
        } elseif ($data['status'] === 'menunggu_penggantian') {
            $data['received_at'] = null;
            $data['rejection_reason'] = null;
        } else {
            $data['received_at'] = null;
        }

        $toolReplacement->update($data);

        return back()->with('success', 'Status surat penggantian alat berhasil diperbarui.');
    }

    private function filteredQuery(Request $request): Builder
    {
        return ToolReplacementRequest::query()
            ->visibleFor($request->user())
            ->when($request->filled('unit_id') && $request->user()->canFilterUnits(), fn (Builder $query) => $query->where('unit_id', $request->integer('unit_id')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->when($request->filled('incident_date'), fn (Builder $query) => $query->whereDate('incident_date', $request->date('incident_date')))
            ->when($request->filled('student_name'), fn (Builder $query) => $query->where('student_name', 'like', '%'.$request->string('student_name').'%'))
            ->when($request->filled('practicum_name'), fn (Builder $query) => $query->where('practicum_name', 'like', '%'.$request->string('practicum_name').'%'))
            ->when($request->filled('asset_name'), function (Builder $query) use ($request): void {
                $query->whereHas('asset', fn (Builder $assetQuery) => $assetQuery->where('name', 'like', '%'.$request->string('asset_name').'%'));
            });
    }

    private function authorizeReplacement(Request $request, ToolReplacementRequest $toolReplacement): void
    {
        abort_unless($request->user()->canAccessUnit($toolReplacement->unit_id), 403);
    }
}
