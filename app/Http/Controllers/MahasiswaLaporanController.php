<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Models\LaporanKeluhan;
use App\Models\PenugasanUserLab;
use Illuminate\Http\Request;

class MahasiswaLaporanController extends Controller
{
    // GET / — Halaman beranda dengan form laporan
    public function index()
    {
        $labs = Lab::where('status_lab', 'aktif')->orderBy('nm_lab')->get();
        return view('mahasiswa.form', compact('labs'));
    }

    // POST /laporan — Simpan laporan baru
    public function store(Request $request)
    {
        $request->validate([
            'nim_pelapor'      => 'required|string|max:20',
            'nm_pelapor'       => 'required|string|max:100',
            'fakultas_pelapor' => 'required|string|max:100',
            'id_lab'           => 'required|exists:labs,id_lab',
            'kategori'         => 'required|in:PC,non_PC',
            'catatan_lpr'      => 'required|string|min:10',
            'file_foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nim_pelapor.required'      => 'NIM wajib diisi.',
            'nm_pelapor.required'       => 'Nama lengkap wajib diisi.',
            'fakultas_pelapor.required' => 'Fakultas wajib diisi.',
            'id_lab.required'           => 'Pilih laboratorium terlebih dahulu.',
            'id_lab.exists'             => 'Laboratorium tidak valid.',
            'kategori.required'         => 'Kategori kerusakan wajib dipilih.',
            'catatan_lpr.required'      => 'Deskripsi keluhan wajib diisi.',
            'catatan_lpr.min'           => 'Deskripsi keluhan minimal 10 karakter.',
            'file_foto.image'           => 'File harus berupa gambar.',
            'file_foto.max'             => 'Ukuran foto maksimal 2MB.',
        ]);

        // Cari penugasan SPV aktif di lab yang dilaporkan
        $penugasan = PenugasanUserLab::where('id_lab', $request->id_lab)
            ->where('status_aktif', 'aktif')
            ->whereHas('user', fn ($q) => $q->where('role_user', 'like', 'spv_%'))
            ->first();

        // Fallback: jika tidak ada SPV, ambil penugasan siapapun di lab itu
        if (!$penugasan) {
            $penugasan = PenugasanUserLab::where('id_lab', $request->id_lab)
                ->where('status_aktif', 'aktif')
                ->first();
        }

        $filePath = null;
        if ($request->hasFile('file_foto')) {
            $filePath = $request->file('file_foto')->store('laporan', 'public');
        }

        $noLaporan = LaporanKeluhan::generateNomorLaporan();

        LaporanKeluhan::create([
            'no_laporan'       => $noLaporan,
            'tgl_lapor'        => now()->toDateString(),
            'approval'         => 'menunggu',
            'nim_pelapor'      => $request->nim_pelapor,
            'nm_pelapor'       => $request->nm_pelapor,
            'fakultas_pelapor' => $request->fakultas_pelapor,
            'kategori'         => $request->kategori,
            'catatan_lpr'      => $request->catatan_lpr,
            'file_foto'        => $filePath,
            'id_lab'           => $request->id_lab,        // simpan langsung
            'id_penugasan'     => $penugasan?->id_penugasan,
        ]);

        return redirect()
            ->route('mahasiswa.status', ['no_laporan' => $noLaporan])
            ->with('success', 'Laporan berhasil dikirim! Catat nomor laporan Anda: ' . $noLaporan);
    }

    // GET /status — Halaman status laporan (publik + cari laporan sendiri)
    public function status(Request $request)
    {
        $noLaporan = $request->query('no_laporan');
        $laporan   = null;

        // Cari laporan spesifik berdasarkan nomor (milik mahasiswa sendiri)
        if ($noLaporan) {
            $laporan = LaporanKeluhan::with([
                'perbaikan.riwayatPerbaikans',
                'penugasan.lab',
                'lab',
            ])->where('no_laporan', $noLaporan)->first();
        }

        // Ambil SEMUA laporan (publik) dengan filter & pagination
        $query = LaporanKeluhan::with(['perbaikan', 'penugasan.lab', 'lab'])
            ->orderBy('tgl_lapor', 'desc');

        // Filter per lab
        if ($request->filled('filter_lab')) {
            $query->where(function ($q) use ($request) {
                $q->where('id_lab', $request->filter_lab)
                  ->orWhereHas('penugasan', fn ($q2) =>
                      $q2->where('id_lab', $request->filter_lab)
                  );
            });
        }

        // Filter per kategori
        if ($request->filled('filter_kategori')) {
            $query->where('kategori', $request->filter_kategori);
        }

        // Filter per status approval
        if ($request->filled('filter_status')) {
            $query->where('approval', $request->filter_status);
        }

        $semuaLaporan = $query->paginate(10)->withQueryString();

        // List lab untuk dropdown filter
        $labs = Lab::where('status_lab', 'aktif')->orderBy('nm_lab')->get();

        return view('mahasiswa.status', compact(
            'laporan',
            'noLaporan',
            'semuaLaporan',
            'labs'
        ));
    }

    // GET /status/{no_laporan} — Cek status satu laporan (support JSON & redirect)
    public function showStatus(string $noLaporan)
    {
        $laporan = LaporanKeluhan::with([
            'perbaikan',
            'penugasan.lab',
            'lab',
        ])->where('no_laporan', $noLaporan)->first();

        if (!$laporan) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Laporan tidak ditemukan.'], 404);
            }
            return redirect()->route('mahasiswa.status')
                ->withErrors(['Nomor laporan tidak ditemukan.']);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'no_laporan'       => $laporan->no_laporan,
                'tgl_lapor'        => $laporan->tgl_lapor->format('d/m/Y'),
                'lab'              => $laporan->penugasan?->lab?->nm_lab ?? $laporan->lab?->nm_lab,
                'kategori'         => $laporan->kategori,
                'approval'         => $laporan->approval,
                'status_perbaikan' => $laporan->perbaikan?->status_perbaikan,
                'app_validasi'     => $laporan->perbaikan?->app_validasi,
            ]);
        }

        return redirect()->route('mahasiswa.status', ['no_laporan' => $noLaporan]);
    }

    // GET /home — Halaman beranda umum 
    public function home()
    {
        return view('mahasiswa.home');
    }
}