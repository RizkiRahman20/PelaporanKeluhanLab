@extends('layouts.mahasiswa')

@section('title', 'Status Laporan — Pelaporan Keluhan Lab')

@push('styles')
<style>
    .page-wrap {
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
        padding: 40px 24px 64px;
    }

    /* ─── HEADER ─── */
    .page-header {
        margin-bottom: 32px;
        animation: fadeUp 0.4s ease both;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .page-title {
        font-family: 'Sora', sans-serif;
        font-size: clamp(22px, 4vw, 26px);
        font-weight: 800;
        color: var(--navy);
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .page-sub { 
        font-size: 13px; 
        color: var(--muted); 
        line-height: 1.6;
    }

    /* ─── SEARCH MY REPORT ─── */
    .search-card {
        background: var(--navy);
        border-radius: 20px;
        padding: 28px 32px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        animation: fadeUp 0.4s 0.1s ease both;
    }

    .search-card::before {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
        top: -80px;
        right: -60px;
        pointer-events: none;
    }

    .search-card-title {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 15px;
        color: white;
        margin-bottom: 4px;
        position: relative;
        z-index: 1;
    }

    .search-card-sub {
        font-size: 12px;
        color: rgba(255,255,255,0.55);
        margin-bottom: 16px;
        line-height: 1.5;
        position: relative;
        z-index: 1;
    }

    .search-row {
        display: flex;
        gap: 10px;
        position: relative;
        z-index: 1;
    }

    .search-input {
        flex: 1;
        min-width: 0;
        border: 1.5px solid rgba(255,255,255,0.2);
        border-radius: 10px;
        padding: 11px 16px;
        font-size: 13px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: rgba(255,255,255,0.1);
        color: white;
        outline: none;
        transition: border-color 0.18s;
    }

    .search-input::placeholder { 
        color: rgba(255,255,255,0.4); 
    }

    .search-input:focus { 
        border-color: rgba(255,255,255,0.5); 
        background: rgba(255,255,255,0.15); 
    }

    .search-btn {
        background: white;
        color: var(--navy);
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 13px;
        padding: 11px 22px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.18s;
    }

    .search-btn:hover { 
        background: var(--accent-lt); 
    }

    /* ─── RESULT CARD ─── */
    .result-card {
        background: white;
        border-radius: 16px;
        border: 1.5px solid var(--border);
        padding: 24px 28px;
        margin-top: 16px;
        animation: fadeUp 0.3s ease both;
        box-shadow: 0 4px 20px rgba(30,45,90,0.07);
    }

    .result-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .result-no {
        font-family: 'Sora', sans-serif;
        font-size: 17px;
        font-weight: 800;
        color: var(--navy);
        word-break: break-word;
    }

    .result-date {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
        line-height: 1.5;
    }

    .result-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .result-item p,
    .timeline-text,
    .td-no,
    .td-lab,
    .td-catatan {
        word-break: break-word;
    }

    .result-item label {
        font-size: 10px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 4px;
    }

    .result-item p {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.5;
    }

    /* Status badge */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 99px;
        letter-spacing: 0.2px;
        line-height: 1.4;
        white-space: nowrap;
    }

    .badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
    }

    .badge-menunggu    { background: var(--warning-bg); color: var(--warning); }
    .badge-disetujui   { background: #dbeafe; color: #1e40af; }
    .badge-ditolak     { background: var(--danger-bg);  color: var(--danger); }
    .badge-antrean     { background: #f1f5f9; color: #475569; }
    .badge-dikerjakan  { background: #fffbeb; color: #b45309; }
    .badge-sparepart   { background: #eff6ff; color: #1d4ed8; }
    .badge-selesai     { background: var(--success-bg); color: var(--success); }
    .badge-divalidasi  { background: #dcfce7; color: #166534; }
    .badge-dikembalikan{ background: var(--danger-bg);  color: var(--danger); }

    /* Timeline riwayat */
    .timeline { 
        border-left: 2px solid var(--border); 
        padding-left: 20px; 
        margin-top: 4px; 
    }

    .timeline-item {
        position: relative;
        padding-bottom: 14px;
    }

    .timeline-item:last-child { 
        padding-bottom: 0; 
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -25px;
        top: 5px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--accent);
        border: 2px solid white;
        box-shadow: 0 0 0 2px var(--accent);
    }

    .timeline-date {
        font-size: 10px;
        color: var(--muted);
        font-weight: 600;
        margin-bottom: 2px;
    }

    .timeline-text {
        font-size: 12px;
        color: var(--text);
        line-height: 1.5;
    }

    .not-found-msg {
        background: #fef9c3;
        border: 1px solid #fde047;
        color: #854d0e;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 500;
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.5;
        word-break: break-word;
    }

    /* ─── SEMUA LAPORAN ─── */
    .section-divider {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 36px 0 24px;
        animation: fadeUp 0.4s 0.15s ease both;
    }

    .section-divider-line { 
        flex: 1; 
        height: 1px; 
        background: var(--border); 
    }

    .section-divider-text {
        font-family: 'Sora', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: var(--muted);
        white-space: nowrap;
    }

    /* Filter bar */
    .filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        animation: fadeUp 0.4s 0.2s ease both;
    }

    .filter-select {
        border: 1.5px solid var(--border);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text);
        background: white;
        cursor: pointer;
        outline: none;
        transition: border-color 0.18s;
        min-width: 140px;
    }

    .filter-select:focus { 
        border-color: var(--accent); 
    }

    .filter-count {
        font-size: 12px;
        color: var(--muted);
        padding: 8px 0;
        margin-left: auto;
        font-weight: 500;
        white-space: nowrap;
    }

    /* TABLE */
    .table-wrap {
        background: white;
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(30,45,90,0.05);
        animation: fadeUp 0.4s 0.25s ease both;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead th {
        background: #f8fafc;
        padding: 12px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }

    tbody tr:last-child { 
        border-bottom: none; 
    }

    tbody tr:hover { 
        background: #f8fafc; 
    }

    tbody td {
        padding: 13px 16px;
        font-size: 13px;
        color: var(--text);
        vertical-align: middle;
    }

    .td-no {
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 12px;
        color: var(--accent);
    }

    .td-lab { 
        font-weight: 600; 
    }

    .td-catatan { 
        color: var(--muted); 
        max-width: 200px; 
    }

    .td-catatan span {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Pagination */
    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 24px;
        animation: fadeUp 0.4s 0.3s ease both;
        flex-wrap: wrap;
    }

    .pagination-wrap .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        color: var(--muted);
        border: 1.5px solid var(--border);
        transition: all 0.15s;
        background: white;
    }

    .pagination-wrap .page-link:hover { 
        border-color: var(--accent); 
        color: var(--accent); 
    }

    .pagination-wrap .page-link.active { 
        background: var(--navy); 
        color: white; 
        border-color: var(--navy); 
    }

    .pagination-wrap .page-link.disabled { 
        opacity: 0.4; 
        pointer-events: none; 
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--muted);
    }

    .empty-state-icon { 
        font-size: 40px; 
        margin-bottom: 12px; 
    }

    .empty-state-text { 
        font-size: 14px; 
        font-weight: 500; 
    }

    /* ─── RESPONSIVE TABLET ─── */
    @media (max-width: 768px) {
        .page-wrap {
            padding: 32px 18px 56px;
        }

        .search-card {
            padding: 24px;
            border-radius: 18px;
        }

        .search-row { 
            flex-direction: column; 
        }

        .search-btn { 
            width: 100%; 
        }

        .result-grid { 
            grid-template-columns: 1fr 1fr; 
        }

        .filter-count {
            width: 100%;
            margin-left: 0;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            min-width: 760px;
        }
    }

    /* ─── RESPONSIVE MOBILE ─── */
    @media (max-width: 480px) {
        .page-wrap {
            padding: 28px 14px 48px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .search-card {
            padding: 22px 18px;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .search-input,
        .search-btn {
            font-size: 12px;
            padding: 12px 14px;
        }

        .result-card {
            padding: 20px 16px;
            border-radius: 14px;
        }

        .result-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .result-grid { 
            grid-template-columns: 1fr; 
            gap: 14px;
        }

        .badge {
            white-space: normal;
        }

        .not-found-msg {
            align-items: flex-start;
            font-size: 12px;
        }

        .section-divider {
            gap: 10px;
            margin: 30px 0 20px;
        }

        .section-divider-text {
            font-size: 12px;
        }

        .filter-bar form {
            display: grid !important;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .filter-select {
            width: 100%;
            min-width: 0;
        }

        .filter-count {
            padding: 4px 0 0;
            white-space: normal;
        }

        .table-wrap {
            border-radius: 14px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 720px;
        }

        thead th {
            padding: 11px 12px;
            font-size: 10px;
        }

        tbody td {
            padding: 12px;
            font-size: 12px;
        }

        .pagination-wrap {
            gap: 5px;
        }

        .pagination-wrap .page-link {
            min-width: 32px;
            height: 32px;
            font-size: 12px;
        }

        .empty-state {
            padding: 36px 18px;
        }
    }

    @media (max-width: 360px) {
        .search-card-title {
            font-size: 14px;
        }

        .result-no {
            font-size: 15px;
        }

        table {
            min-width: 680px;
        }
    }
</style>
@endpush

@section('content')
<div class="page-wrap">

    {{-- HEADER --}}
    <div class="page-header">
        <h1 class="page-title">Status Laporan</h1>
        <p class="page-sub">Cari status laporan Anda, atau lihat seluruh laporan yang masuk dari mahasiswa.</p>
    </div>

    {{-- SEARCH LAPORAN SENDIRI --}}
    <div class="search-card">
        <p class="search-card-title">🔍 Cek Laporan Saya</p>
        <p class="search-card-sub">Masukkan nomor laporan yang diberikan saat submit</p>
        <form method="GET" action="{{ route('mahasiswa.status') }}">
            <div class="search-row">
                <input type="text"
                       name="no_laporan"
                       value="{{ $noLaporan ?? '' }}"
                       class="search-input"
                       placeholder="Contoh: LPR-20260508-0001">
                <button type="submit" class="search-btn">Cari Status</button>
            </div>
        </form>
    </div>

    {{-- RESULT LAPORAN SENDIRI --}}
    @if($noLaporan && !$laporan)
        <div class="not-found-msg">
            ⚠ Laporan dengan nomor <strong>{{ $noLaporan }}</strong> tidak ditemukan. Periksa kembali nomor laporan Anda.
        </div>
    @endif

    @if($laporan)
    <div class="result-card">
        <div class="result-card-header">
            <div>
                <div class="result-no">{{ $laporan->no_laporan }}</div>
                <div class="result-date">Dilaporkan pada {{ $laporan->tgl_lapor->format('d F Y') }}</div>
            </div>
            <span class="badge badge-{{ $laporan->approval }}">
                <span class="badge-dot"></span>
                {{ ucfirst($laporan->approval) }}
            </span>
        </div>

        <div class="result-grid">
            <div class="result-item">
                <label>Laboratorium</label>
                <p>{{ $laporan->penugasan?->lab?->nm_lab ?? ($laporan->lab?->nm_lab ?? '—') }}</p>
            </div>
            <div class="result-item">
                <label>Kategori</label>
                <p>{{ $laporan->kategori === 'non_PC' ? 'Non PC' : $laporan->kategori }}</p>
            </div>
            @if($laporan->perbaikan)
            <div class="result-item">
                <label>Status Perbaikan</label>
                <p>
                    <span class="badge badge-{{ str_replace('_', '', $laporan->perbaikan->status_perbaikan) }}">
                        <span class="badge-dot"></span>
                        {{ str_replace('_', ' ', ucfirst($laporan->perbaikan->status_perbaikan)) }}
                    </span>
                </p>
            </div>
            <div class="result-item">
                <label>Validasi SPV</label>
                <p>
                    <span class="badge badge-{{ $laporan->perbaikan->app_validasi }}">
                        <span class="badge-dot"></span>
                        {{ ucfirst($laporan->perbaikan->app_validasi) }}
                    </span>
                </p>
            </div>
            @endif
            <div class="result-item">
                <label>Deskripsi</label>
                <p style="font-weight:400;color:var(--muted);font-size:12px;">{{ $laporan->catatan_lpr }}</p>
            </div>
        </div>

        {{-- Riwayat perbaikan --}}
        @if($laporan->perbaikan && $laporan->perbaikan->riwayatPerbaikans->count() > 0)
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:4px;">
            <p style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">Riwayat Perbaikan</p>
            <div class="timeline">
                @foreach($laporan->perbaikan->riwayatPerbaikans->sortByDesc('tgl_ubah') as $rw)
                <div class="timeline-item">
                    <div class="timeline-date">{{ $rw->tgl_ubah->format('d/m/Y') }}</div>
                    <div class="timeline-text">{{ $rw->catatan_rw }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- DIVIDER --}}
    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-text">📋 Semua Laporan Masuk</div>
        <div class="section-divider-line"></div>
    </div>

    {{-- FILTER --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('mahasiswa.status') }}" id="filterForm"
              style="display:flex;gap:10px;flex-wrap:wrap;width:100%;align-items:center;">

            @if($noLaporan)
                <input type="hidden" name="no_laporan" value="{{ $noLaporan }}">
            @endif

            <select name="filter_lab" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Lab</option>
                @foreach($labs as $lab)
                    <option value="{{ $lab->id_lab }}" {{ request('filter_lab') == $lab->id_lab ? 'selected' : '' }}>
                        {{ $lab->nm_lab }}
                    </option>
                @endforeach
            </select>

            <select name="filter_kategori" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Kategori</option>
                <option value="PC"     {{ request('filter_kategori') === 'PC'     ? 'selected' : '' }}>PC</option>
                <option value="non_PC" {{ request('filter_kategori') === 'non_PC' ? 'selected' : '' }}>Non PC</option>
            </select>

            <select name="filter_status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Status</option>
                <option value="menunggu"  {{ request('filter_status') === 'menunggu'  ? 'selected' : '' }}>Menunggu</option>
                <option value="disetujui" {{ request('filter_status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak"   {{ request('filter_status') === 'ditolak'   ? 'selected' : '' }}>Ditolak</option>
            </select>

            <span class="filter-count">{{ $semuaLaporan->total() }} laporan ditemukan</span>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="table-wrap">
        @if($semuaLaporan->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>No. Laporan</th>
                    <th>Tanggal</th>
                    <th>Lab</th>
                    <th>Kategori</th>
                    <th>Keluhan</th>
                    <th>Approval</th>
                    <th>Perbaikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($semuaLaporan as $lpr)
                <tr>
                    <td class="td-no">{{ $lpr->no_laporan }}</td>
                    <td style="color:var(--muted);font-size:12px;">{{ $lpr->tgl_lapor->format('d/m/Y') }}</td>
                    <td class="td-lab">{{ $lpr->penugasan?->lab?->nm_lab ?? ($lpr->lab?->nm_lab ?? '—') }}</td>
                    <td>
                        <span class="badge {{ $lpr->kategori === 'PC' ? 'badge-dikerjakan' : 'badge-sparepart' }}">
                            {{ $lpr->kategori === 'non_PC' ? 'Non PC' : $lpr->kategori }}
                        </span>
                    </td>
                    <td class="td-catatan">
                        <span>{{ $lpr->catatan_lpr }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $lpr->approval }}">
                            <span class="badge-dot"></span>
                            {{ ucfirst($lpr->approval) }}
                        </span>
                    </td>
                    <td>
                        @if($lpr->perbaikan)
                            <span class="badge badge-{{ str_replace('_', '', $lpr->perbaikan->status_perbaikan) }}">
                                <span class="badge-dot"></span>
                                {{ str_replace('_', ' ', ucfirst($lpr->perbaikan->status_perbaikan)) }}
                            </span>
                        @else
                            <span style="color:var(--muted);font-size:12px;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <div class="empty-state-text">Belum ada laporan yang sesuai filter.</div>
        </div>
        @endif
    </div>

    {{-- PAGINATION --}}
    @if($semuaLaporan->hasPages())
    <div class="pagination-wrap">
        {{-- Prev --}}
        @if($semuaLaporan->onFirstPage())
            <span class="page-link disabled">‹</span>
        @else
            <a href="{{ $semuaLaporan->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="page-link">‹</a>
        @endif

        {{-- Page numbers --}}
        @foreach($semuaLaporan->getUrlRange(1, $semuaLaporan->lastPage()) as $page => $url)
            @if($page == $semuaLaporan->currentPage())
                <span class="page-link active">{{ $page }}</span>
            @else
                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" class="page-link">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($semuaLaporan->hasMorePages())
            <a href="{{ $semuaLaporan->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="page-link">›</a>
        @else
            <span class="page-link disabled">›</span>
        @endif
    </div>
    @endif

</div>
@endsection