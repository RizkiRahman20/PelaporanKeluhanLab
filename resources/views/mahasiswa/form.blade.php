@extends('layouts.mahasiswa')

@section('title', 'Beranda — Pelaporan Keluhan Lab')

@push('styles')
<style>
    /* ─── HERO ─── */
    .hero {
        max-width: 1100px;
        margin: 48px auto 0;
        padding: 0 24px 48px;
    }

    .hero-card {
        background: linear-gradient(135deg, #c8d3ee 0%, #b8c4e0 40%, #a8b6d4 100%);
        border-radius: 24px;
        padding: 72px 48px;
        text-align: center;
        position: relative;
        overflow: hidden;
        animation: heroIn 0.6s cubic-bezier(0.22,1,0.36,1);
    }

    @keyframes heroIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Decorative circles */
    .hero-card::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        top: -100px;
        left: -80px;
        pointer-events: none;
    }

    .hero-card::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: rgba(30,45,90,0.08);
        border-radius: 50%;
        bottom: -60px;
        right: -40px;
        pointer-events: none;
    }

    .hero-title {
        font-family: 'Sora', sans-serif;
        font-size: clamp(32px, 5vw, 52px);
        font-weight: 800;
        color: var(--navy-dark);
        line-height: 1.15;
        letter-spacing: -1px;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }

    .hero-subtitle {
        font-size: 15px;
        color: rgba(21,32,64,0.65);
        margin-bottom: 36px;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }

    .hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--navy);
        color: white;
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 14px;
        padding: 14px 32px;
        border-radius: 12px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        z-index: 1;
        box-shadow: 0 4px 20px rgba(30,45,90,0.3);
    }

    .hero-btn:hover {
        background: var(--navy-mid);
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(30,45,90,0.4);
    }

    .hero-btn:active { transform: translateY(0); }

    /* ─── FORM SECTION ─── */
    .form-section {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 24px 64px;
    }

    .form-section-title {
        font-family: 'Sora', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--navy);
        margin-bottom: 6px;
    }

    .form-section-sub {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 28px;
    }

    .form-card {
        background: var(--white);
        border-radius: 20px;
        padding: 36px 40px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 24px rgba(30,45,90,0.05);
        animation: formIn 0.5s 0.15s cubic-bezier(0.22,1,0.36,1) both;
    }

    @keyframes formIn {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group.full { grid-column: 1 / -1; }

    .form-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--navy);
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .form-label span { color: #e53e3e; margin-left: 2px; }

    .form-input,
    .form-select,
    .form-textarea {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text);
        background: var(--white);
        transition: border-color 0.18s, box-shadow 0.18s;
        width: 100%;
        outline: none;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(61,95,196,0.1);
    }

    .form-input.is-error,
    .form-select.is-error,
    .form-textarea.is-error {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
    }

    .form-textarea { resize: vertical; min-height: 110px; }

    .form-hint {
        font-size: 11px;
        color: var(--muted);
        margin-top: 2px;
    }

    .form-error {
        font-size: 11px;
        color: var(--danger);
        margin-top: 2px;
        font-weight: 500;
    }

    .form-file-wrapper {
        border: 1.5px dashed var(--border);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.18s, background 0.18s;
        position: relative;
    }

    .form-file-wrapper:hover { border-color: var(--accent); background: var(--accent-lt); }

    .form-file-wrapper input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }

    .form-file-icon { font-size: 24px; margin-bottom: 4px; }
    .form-file-text { font-size: 12px; color: var(--muted); }
    .form-file-text strong { color: var(--accent); }

    #file-name {
        font-size: 11px;
        color: var(--success);
        font-weight: 600;
        margin-top: 4px;
    }

    .form-submit-row {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 28px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--navy);
        color: white;
        font-family: 'Sora', sans-serif;
        font-weight: 700;
        font-size: 14px;
        padding: 12px 28px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 12px rgba(30,45,90,0.25);
    }

    .btn-submit:hover {
        background: var(--navy-mid);
        transform: translateY(-1px);
        box-shadow: 0 4px 18px rgba(30,45,90,0.35);
    }

    .btn-reset {
        font-size: 13px;
        color: var(--muted);
        background: none;
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 12px 20px;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 500;
        transition: all 0.18s;
    }

    .btn-reset:hover { border-color: var(--accent); color: var(--accent); }

    /* ─── FORM SCROLL TARGET ─── */
    #form-laporan { scroll-margin-top: 80px; }

    @media (max-width: 640px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-card { padding: 24px 20px; }
        .hero-card { padding: 48px 24px; }
    }
</style>
@endpush

@section('content')

{{-- FORM LAPORAN --}}
<section class="form-section" id="form-laporan">
    <h2 class="form-section-title">Form Laporan Keluhan</h2>
    <p class="form-section-sub">Lengkapi data berikut dengan benar. Simpan nomor laporan yang diberikan setelah submit.</p>

    <div class="form-card">
        <form method="POST" action="{{ route('mahasiswa.store') }}" enctype="multipart/form-data" id="laporanForm">
            @csrf
            <div class="form-grid">

                {{-- NIM --}}
                <div class="form-group">
                    <label class="form-label">NIM <span>*</span></label>
                    <input type="text" name="nim_pelapor" value="{{ old('nim_pelapor') }}"
                           class="form-input {{ $errors->has('nim_pelapor') ? 'is-error' : '' }}"
                           placeholder="Contoh: 10231010" required>
                    @error('nim_pelapor')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- NAMA --}}
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span>*</span></label>
                    <input type="text" name="nm_pelapor" value="{{ old('nm_pelapor') }}"
                           class="form-input {{ $errors->has('nm_pelapor') ? 'is-error' : '' }}"
                           placeholder="Nama sesuai KTM" required>
                    @error('nm_pelapor')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- FAKULTAS --}}
                <div class="form-group">
                    <label class="form-label">Fakultas / Program Studi <span>*</span></label>
                    <input type="text" name="fakultas_pelapor" value="{{ old('fakultas_pelapor') }}"
                           class="form-input {{ $errors->has('fakultas_pelapor') ? 'is-error' : '' }}"
                           placeholder="Contoh: Teknik Informatika" required>
                    @error('fakultas_pelapor')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- LAB --}}
                <div class="form-group">
                    <label class="form-label">Laboratorium <span>*</span></label>
                    <select name="id_lab" class="form-select {{ $errors->has('id_lab') ? 'is-error' : '' }}" required>
                        <option value="">— Pilih Laboratorium —</option>
                        @foreach($labs as $lab)
                            <option value="{{ $lab->id_lab }}" {{ old('id_lab') == $lab->id_lab ? 'selected' : '' }}>
                                {{ $lab->nm_lab }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_lab')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- KATEGORI --}}
                <div class="form-group">
                    <label class="form-label">Kategori Kerusakan <span>*</span></label>
                    <select name="kategori" class="form-select {{ $errors->has('kategori') ? 'is-error' : '' }}" required>
                        <option value="">— Pilih Kategori —</option>
                        <option value="PC"     {{ old('kategori') === 'PC'     ? 'selected' : '' }}>PC (Komputer)</option>
                        <option value="non_PC" {{ old('kategori') === 'non_PC' ? 'selected' : '' }}>Non PC (Fasilitas lain)</option>
                    </select>
                    @error('kategori')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- FOTO (opsional) --}}
                <div class="form-group">
                    <label class="form-label">Foto Kerusakan <small style="font-weight:400;text-transform:none;color:var(--muted)">(Opsional)</small></label>
                    <div class="form-file-wrapper">
                        <input type="file" name="file_foto" id="file_foto" accept="image/*"
                               onchange="showFileName(this)">
                        <div class="form-file-icon">📷</div>
                        <div class="form-file-text"><strong>Klik untuk upload</strong> atau seret file ke sini</div>
                        <div id="file-name"></div>
                    </div>
                    <span class="form-hint">Format JPG, PNG. Maks 2MB.</span>
                    @error('file_foto')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- DESKRIPSI --}}
                <div class="form-group full">
                    <label class="form-label">Deskripsi Keluhan <span>*</span></label>
                    <textarea name="catatan_lpr"
                              class="form-textarea {{ $errors->has('catatan_lpr') ? 'is-error' : '' }}"
                              placeholder="Jelaskan detail kerusakan atau keluhan yang dialami. Contoh: PC nomor 5 tidak bisa menyala, monitor berkedip, dll."
                              required>{{ old('catatan_lpr') }}</textarea>
                    @error('catatan_lpr')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <div class="form-submit-row">
                <button type="reset" class="btn-reset">Reset</button>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function showFileName(input) {
        const el = document.getElementById('file-name');
        if (input.files && input.files[0]) {
            el.textContent = '✓ ' + input.files[0].name;
        } else {
            el.textContent = '';
        }
    }

    // Disable tombol submit setelah klik (mencegah double submit)
    document.getElementById('laporanForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="animation:spin 0.8s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Mengirim...';
    });
</script>
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush