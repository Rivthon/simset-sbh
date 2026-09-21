<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetQrController extends Controller
{
    public function show(Request $request, Asset $asset): View
    {
        if (! $request->user()->canAccessUnit($asset->unit_id)) {
            abort(403);
        }

        $qrCode = $asset->qr_code;
        abort_if(blank($qrCode), 404);

        return view('assets.qr', [
            'asset' => $asset->load(['unit', 'category', 'location', 'container']),
            'qrData' => route('qr.assets.show', $qrCode),
        ]);
    }

    public function generate(Request $request, Asset $asset): RedirectResponse
    {
        abort_unless($request->user()->hasRole(['admin', 'pengelola']), 403);

        if (! $request->user()->canAccessUnit($asset->unit_id)) {
            abort(403);
        }

        if (blank($asset->qr_code)) {
            $asset->forceFill(['qr_code' => Asset::generateQrCode()])->save();
        }

        return redirect()->route('assets.show', $asset)->with('success', 'QR Code aset berhasil dibuat.');
    }
}
