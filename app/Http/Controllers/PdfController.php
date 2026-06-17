<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\RiwayatPerbaikan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfController extends Controller
{
    public function cetakRiwayat(Request $request)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $request->validate([
            'id_lab' => ['nullable', 'exists:labs,id_lab'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'mode' => ['nullable', 'in:preview,download'],
        ]);

        $query = RiwayatPerbaikan::with([
            'perbaikan.laporan.lab',
            'user',
        ]);

        if ($request->filled('id_lab')) {
            $query->whereHas('perbaikan.laporan', fn ($q) =>
                $q->where('laporan_keluhans.id_lab', $request->id_lab)
            );
        }

        if ($request->filled('dari')) {
            $query->whereDate('tgl_ubah', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tgl_ubah', '<=', $request->sampai);
        }

        $riwayats = $query
            ->orderBy('tgl_ubah', 'desc')
            ->get();

        $lab = $request->filled('id_lab')
            ? Lab::find($request->id_lab)
            : null;

        $pdf = Pdf::loadView('pdf.riwayat-perbaikan', [
            'riwayats' => $riwayats,
            'lab' => $lab,
            'dari' => $request->dari,
            'sampai' => $request->sampai,
            'dicetakOleh' => Auth::user()?->nm_user ?? Auth::user()?->email ?? '-',
        ])->setPaper('a4', 'portrait');

        $filename = 'riwayat-perbaikan-' . now()->format('Ymd') . '.pdf';

        if ($request->mode === 'download') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}