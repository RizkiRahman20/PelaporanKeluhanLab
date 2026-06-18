<div
    class="kanban-wrapper"
    x-data="{
        draggedId: null,
        toast: {
            show: false,
            type: 'success',
            message: '',
        },
        showToast(type, message) {
            this.toast.type = type;
            this.toast.message = message;
            this.toast.show = true;

            setTimeout(() => {
                this.toast.show = false;
            }, 2500);
        }
    }"
    x-on:kanban-notify.window="showToast($event.detail.type, $event.detail.message)"
>
    <style>
        .kanban-wrapper {
            width: 100%;
        }

        .kanban-shell {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 22px;
            border-radius: 22px;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: #ffffff;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
        }

        html.dark .kanban-shell {
            background: #0b1020;
            border-color: rgba(255, 255, 255, 0.10);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.30);
        }

        .kanban-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            min-width: 260px;
            max-width: 360px;
            border-radius: 14px;
            padding: 14px 16px;
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.25);
        }

        .kanban-toast.success {
            background: #16a34a;
        }

        .kanban-toast.warning {
            background: #f59e0b;
        }

        .kanban-toast.info {
            background: #2563eb;
        }

        .kanban-toast.error {
            background: #dc2626;
        }

        .kanban-subtitle {
            margin-bottom: 18px;
            font-size: 14px;
            color: #64748b;
        }

        html.dark .kanban-subtitle {
            color: #cbd5e1;
        }

        .kanban-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .kanban-column {
            min-height: 430px;
            border-radius: 16px;
            padding: 16px;
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
        }

        .kanban-column.antrean {
            background: #fff15a;
            border-color: #e4d500;
            color: #1f2937;
        }

        .kanban-column.menunggu_sparepart {
            background: #ff3f6c;
            border-color: #fb2c5d;
            color: #ffffff;
        }

        .kanban-column.dikerjakan {
            background: #64b7ee;
            border-color: #38a3e8;
            color: #ffffff;
        }

        .kanban-column.selesai {
            background: #00f545;
            border-color: #00d83d;
            color: #052e16;
        }

        html.dark .kanban-column.antrean {
            background: #4b430b;
            border-color: rgba(255, 241, 90, 0.45);
            color: #fff7b0;
        }

        html.dark .kanban-column.menunggu_sparepart {
            background: #641329;
            border-color: rgba(255, 99, 132, 0.45);
            color: #ffd6df;
        }

        html.dark .kanban-column.dikerjakan {
            background: #123a5c;
            border-color: rgba(100, 183, 238, 0.45);
            color: #d8efff;
        }

        html.dark .kanban-column.selesai {
            background: #064e22;
            border-color: rgba(74, 222, 128, 0.45);
            color: #d8ffe5;
        }

        .kanban-column-header {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 15px;
        }

        .kanban-column-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        .kanban-divider {
            margin-top: 15px;
            margin-bottom: 14px;
            border-top: 2px solid rgba(255, 255, 255, 0.60);
        }

        .kanban-column.antrean .kanban-divider,
        .kanban-column.selesai .kanban-divider {
            border-color: rgba(15, 23, 42, 0.25);
        }

        html.dark .kanban-divider,
        html.dark .kanban-column.antrean .kanban-divider,
        html.dark .kanban-column.selesai .kanban-divider {
            border-color: rgba(255, 255, 255, 0.18);
        }

        .kanban-items {
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex: 1;
        }

        .kanban-card {
            min-height: 128px;
            border-radius: 14px;
            padding: 14px;
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, 0.35);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            cursor: grab;
            display: flex;
            flex-direction: column;
            transition: 0.2s ease;
        }

        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.14);
        }

        html.dark .kanban-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.10);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
        }

        .kanban-card-title {
            color: #111827;
            font-size: 14px;
            line-height: 1.5;
            font-weight: 700;
            word-break: break-word;
        }

        html.dark .kanban-card-title {
            color: #f8fafc;
        }

        .kanban-card-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .kanban-badge {
            max-width: 150px;
            padding: 5px 10px;
            border-radius: 999px;
            background: #2563eb;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        html.dark .kanban-badge {
            background: #3b82f6;
        }

        .kanban-detail-btn {
            border: none;
            background: transparent;
            color: #312e81;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
        }

        html.dark .kanban-detail-btn {
            color: #c4b5fd;
        }

        .kanban-finish-btn {
            margin-top: 12px;
            width: 100%;
            border: none;
            border-radius: 10px;
            background: #16a34a;
            color: #ffffff;
            padding: 9px 12px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .kanban-finish-btn:hover {
            background: #15803d;
        }

        .kanban-empty {
            min-height: 128px;
            flex: 1;
            border-radius: 14px;
            border: 2px dashed rgba(255, 255, 255, 0.45);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 14px;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            opacity: 0.75;
        }

        .kanban-column.antrean .kanban-empty,
        .kanban-column.selesai .kanban-empty {
            border-color: rgba(15, 23, 42, 0.25);
        }

        html.dark .kanban-empty,
        html.dark .kanban-column.antrean .kanban-empty,
        html.dark .kanban-column.selesai .kanban-empty {
            border-color: rgba(255, 255, 255, 0.22);
        }

        .kanban-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 50;
            background: rgba(0, 0, 0, 0.70);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .kanban-modal {
            width: 100%;
            max-width: 680px;
            max-height: calc(100vh - 32px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 18px;
            background: #ffffff;
            color: #111827;
            border: 1px solid rgba(148, 163, 184, 0.35);
            padding: 24px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
        }

        html.dark .kanban-modal {
            background: #0f172a;
            color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.10);
        }

        .kanban-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .kanban-modal-title {
            font-size: 20px;
            font-weight: 800;
        }

        .kanban-close-btn {
            border: none;
            background: transparent;
            color: inherit;
            font-size: 28px;
            font-weight: 800;
            cursor: pointer;
            line-height: 1;
        }

        .kanban-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .kanban-detail-box {
            border-radius: 12px;
            background: #f8fafc;
            padding: 12px;
        }

        html.dark .kanban-detail-box {
            background: rgba(255, 255, 255, 0.06);
        }

        .kanban-detail-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
        }

        html.dark .kanban-detail-label {
            color: #cbd5e1;
        }

        .kanban-detail-value {
            font-weight: 800;
        }

        .kanban-detail-wide {
            grid-column: span 2;
        }

        .kanban-detail-image {
            width: 100%;
            max-height: 360px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: #f8fafc;
        }

        html.dark .kanban-detail-image {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .kanban-form-group {
            margin-bottom: 14px;
        }

        .kanban-label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 800;
        }

        .kanban-input,
        .kanban-textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #111827;
            padding: 10px 12px;
            font-size: 14px;
        }

        html.dark .kanban-input,
        html.dark .kanban-textarea {
            background: rgba(255, 255, 255, 0.06);
            color: #f8fafc;
            border-color: rgba(255, 255, 255, 0.12);
        }

        .kanban-error {
            margin-top: 5px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
        }

        html.dark .kanban-error {
            color: #fca5a5;
        }

        .kanban-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
        }

        .kanban-btn-secondary,
        .kanban-btn-primary {
            border: none;
            border-radius: 12px;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
        }

        .kanban-btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        html.dark .kanban-btn-secondary {
            background: rgba(255, 255, 255, 0.10);
            color: #f8fafc;
        }

        .kanban-btn-primary {
            background: #16a34a;
            color: #ffffff;
        }

        .kanban-btn-primary:hover {
            background: #15803d;
        }

        @media (max-width: 1200px) {
            .kanban-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .kanban-shell {
                padding: 16px;
            }

            .kanban-modal-backdrop {
                align-items: flex-start;
                overflow-y: auto;
                padding: 12px;
            }

            .kanban-modal {
                max-height: calc(100dvh - 24px);
                padding: 20px;
            }

            .kanban-grid,
            .kanban-detail-grid {
                grid-template-columns: 1fr;
            }

            .kanban-detail-wide {
                grid-column: span 1;
            }
        }
    </style>

    <div
        x-show="toast.show"
        x-transition
        class="kanban-toast"
        x-bind:class="toast.type"
        style="display: none;"
    >
        <span x-text="toast.message"></span>
    </div>

    <div class="kanban-shell">
        <p class="kanban-subtitle">
            Geser kartu ke kolom lain untuk mengubah status perbaikan.
        </p>

        <div class="kanban-grid">
            @foreach ($columns as $status => $column)
                <section
                    class="kanban-column {{ $status }}"
                    x-on:dragover.prevent
                    x-on:drop="
                        if (draggedId) {
                            $wire.updateStatus(draggedId, '{{ $status }}');
                            draggedId = null;
                        }
                    "
                >
                    <div>
                        <div class="kanban-column-header">
                            @if ($column['icon'] === 'clock')
                                <x-heroicon-o-clock class="kanban-column-icon" />
                            @elseif ($column['icon'] === 'exclamation')
                                <x-heroicon-o-exclamation-circle class="kanban-column-icon" />
                            @elseif ($column['icon'] === 'wrench')
                                <x-heroicon-o-wrench-screwdriver class="kanban-column-icon" />
                            @elseif ($column['icon'] === 'check')
                                <x-heroicon-o-check-circle class="kanban-column-icon" />
                            @endif

                            <span>{{ $column['label'] }}</span>
                        </div>

                        <div class="kanban-divider"></div>
                    </div>

                    <div class="kanban-items">
                        @forelse ($perbaikans[$status] as $item)
    @php
        $isLocked = $item->status_perbaikan === 'selesai'
            || $item->app_validasi === 'divalidasi';
    @endphp

    <article
        class="kanban-card"
        draggable="{{ $isLocked ? 'false' : 'true' }}"
        x-on:dragstart="
            if (! {{ $isLocked ? 'true' : 'false' }}) {
                draggedId = {{ $item->id_perbaikan }};
            }
        "
        x-on:dragend="draggedId = null"
        wire:key="perbaikan-{{ $item->id_perbaikan }}"
    >
        <p class="kanban-card-title">
            {{ $item->laporan?->catatan_lpr ?? 'Tidak ada catatan keluhan.' }}
        </p>

        <div class="kanban-card-footer">
            <span class="kanban-badge">
                {{ $item->laporan?->lab?->nm_lab ?? 'Lab -' }}
            </span>

            <button
                type="button"
                wire:click="openDetail({{ $item->id_perbaikan }})"
                class="kanban-detail-btn"
            >
                Detail
            </button>
        </div>

        @if (! $isLocked)
            <button
                type="button"
                wire:click="openSelesaikan({{ $item->id_perbaikan }})"
                class="kanban-finish-btn"
            >
                Selesaikan
            </button>
        @endif
    </article>
@empty
                            <div class="kanban-empty">
                                Belum ada perbaikan
                            </div>
                        @endforelse
                    </div>
                </section>
            @endforeach
        </div>
    </div>

    {{-- Modal Detail --}}
    @if ($showDetailModal)
        <div class="kanban-modal-backdrop">
            <div class="kanban-modal">
                <div class="kanban-modal-header">
                    <h2 class="kanban-modal-title">Detail Keluhan</h2>

                    <button type="button" wire:click="closeDetail" class="kanban-close-btn">
                        ×
                    </button>
                </div>

                <div class="kanban-detail-grid">
                    <div class="kanban-detail-box">
                        <p class="kanban-detail-label">No. Laporan</p>
                        <p class="kanban-detail-value">{{ $detailData['no_laporan'] ?? '-' }}</p>
                    </div>

                    <div class="kanban-detail-box">
                        <p class="kanban-detail-label">Lab</p>
                        <p class="kanban-detail-value">{{ $detailData['lab'] ?? '-' }}</p>
                    </div>

                    <div class="kanban-detail-box">
                        <p class="kanban-detail-label">Pelapor</p>
                        <p class="kanban-detail-value">{{ $detailData['pelapor'] ?? '-' }}</p>
                    </div>

                    <div class="kanban-detail-box">
                        <p class="kanban-detail-label">NIM</p>
                        <p class="kanban-detail-value">{{ $detailData['nim'] ?? '-' }}</p>
                    </div>

                    <div class="kanban-detail-box">
                        <p class="kanban-detail-label">Fakultas</p>
                        <p class="kanban-detail-value">{{ $detailData['fakultas'] ?? '-' }}</p>
                    </div>

                    <div class="kanban-detail-box">
                        <p class="kanban-detail-label">Kategori</p>
                        <p class="kanban-detail-value">{{ $detailData['kategori'] ?? '-' }}</p>
                    </div>

                    <div class="kanban-detail-box kanban-detail-wide">
                        <p class="kanban-detail-label">Catatan Keluhan</p>
                        <p class="kanban-detail-value">{{ $detailData['catatan_lpr'] ?? '-' }}</p>
                    </div>

                    <div class="kanban-detail-box kanban-detail-wide">
                        <p class="kanban-detail-label">Foto Kerusakan</p>

                        @if (!empty($detailData['foto_url']))
                            <div style="margin-top: 10px;">
                                <img
                                    src="{{ $detailData['foto_url'] }}"
                                    alt="Foto kerusakan"
                                    class="kanban-detail-image"
                                >
                            </div>
                        @else
                            <p class="kanban-detail-value">
                                Tidak ada foto yang diupload.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="kanban-actions">
                    <button type="button" wire:click="closeDetail" class="kanban-btn-secondary">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Selesaikan --}}
    @if ($showSelesaiModal)
        <div class="kanban-modal-backdrop">
            <div class="kanban-modal">
                <div class="kanban-modal-header">
                    <h2 class="kanban-modal-title">Selesaikan Perbaikan</h2>

                    <button type="button" wire:click="closeSelesai" class="kanban-close-btn">
                        ×
                    </button>
                </div>

                <form wire:submit.prevent="selesaikanPerbaikan">
                    <div class="kanban-form-group">
                        <label class="kanban-label">
                            Bukti Perbaikan <span style="color: #ef4444;">*</span>
                        </label>

                        <input
                            type="file"
                            wire:model="ft_perbaikan"
                            accept="image/*"
                            class="kanban-input"
                        >

                        @error('ft_perbaikan')
                            <p class="kanban-error">{{ $message }}</p>
                        @enderror

                        <div wire:loading wire:target="ft_perbaikan" style="margin-top: 6px; font-size: 13px; color: #64748b;">
                            Mengupload...
                        </div>
                    </div>

                    <div class="kanban-form-group">
                        <label class="kanban-label">
                            Catatan Perbaikan <span style="color: #ef4444;">*</span>
                        </label>

                        <textarea
                            wire:model="catatan_pbk"
                            rows="4"
                            class="kanban-textarea"
                            placeholder="Contoh: PC sudah diperbaiki dan sudah bisa digunakan kembali."
                        ></textarea>

                        @error('catatan_pbk')
                            <p class="kanban-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="kanban-actions">
                        <button type="button" wire:click="closeSelesai" class="kanban-btn-secondary">
                            Batal
                        </button>

                        <button type="submit" class="kanban-btn-primary">
                            Simpan Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>