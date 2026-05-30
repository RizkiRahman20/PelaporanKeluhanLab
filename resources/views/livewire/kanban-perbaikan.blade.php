<div class="flex justify-center">
    <div
        class="w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-xl"
        x-data="{ draggedId: null }"
    >
        <div class="flex min-h-[560px]">
            {{-- Content --}}
            <main class="flex-1 bg-[#f4f4f4] p-5">
                <h1 class="mb-5 text-3xl font-bold text-gray-900">
                    Status Perbaikan
                </h1>

                {{-- Kanban board --}}
                <div
                    class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4"
                    style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px;"
                >
                    @foreach ($columns as $status => $column)
                        <section
                            class="flex min-h-[390px] flex-col rounded-xl p-4 shadow-sm"
                            style="background-color: {{ $column['color'] }}; min-height: 390px;"
                            @if ($status !== 'selesai')
                                x-on:dragover.prevent
                                x-on:drop="
                                    if (draggedId) {
                                        $wire.updateStatus(draggedId, '{{ $status }}');
                                        draggedId = null;
                                    }
                                "
                            @endif
                        >
                            {{-- Header tiap status --}}
                            <div class="mb-4 flex min-h-10 items-start gap-3">
                                @if ($column['icon'] === 'clock')
                                    <x-heroicon-o-clock class="h-7 w-7 text-blue-500" />
                                @elseif ($column['icon'] === 'exclamation')
                                    <x-heroicon-o-exclamation-circle class="h-7 w-7 text-white" />
                                @elseif ($column['icon'] === 'wrench')
                                    <x-heroicon-o-wrench-screwdriver class="h-7 w-7 text-blue-700" />
                                @elseif ($column['icon'] === 'check')
                                    <x-heroicon-o-check-circle class="h-7 w-7 text-green-700" />
                                @endif

                                <h2 class="text-base font-bold leading-tight text-gray-900">
                                    {{ $column['label'] }}
                                </h2>
                            </div>

                            {{-- Card vertikal, bukan grid horizontal --}}
                            <div class="flex flex-col gap-3">
                                @forelse ($perbaikans[$status] as $item)
                                    <article
                                        class="flex min-h-28 w-full flex-col rounded-md bg-white p-3 shadow-md"
                                        draggable="{{ $status !== 'selesai' ? 'true' : 'false' }}"
                                        x-on:dragstart="draggedId = {{ $item->id_perbaikan }}"
                                        wire:key="perbaikan-{{ $item->id_perbaikan }}"
                                    >
                                        <p class="text-base font-semibold leading-snug text-gray-900">
                                            {{ $item->laporan?->catatan_lpr ?? '-' }}
                                        </p>

                                        <div class="mt-auto flex items-center justify-between gap-3">
                                            <span class="rounded bg-blue-500 px-3 py-1 text-xs font-medium text-white">
                                                {{ $item->laporan?->lab?->nm_lab ?? 'Lab -' }}
                                            </span>

                                            <button
                                                type="button"
                                                wire:click="openDetail({{ $item->id_perbaikan }})"
                                                class="text-sm font-medium hover:underline"
                                                style="color: #11135f;"
                                            >
                                                Detail
                                            </button>
                                        </div>

                                        @if ($item->status_perbaikan !== 'selesai')
                                            <button
                                                type="button"
                                                wire:click="openSelesaikan({{ $item->id_perbaikan }})"
                                                class="mt-3 w-full rounded-md bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700"
                                            >
                                                Selesaikan
                                            </button>
                                        @endif
                                    </article>
                                @empty
                                    <div class="min-h-28 rounded-md border border-white/30"></div>
                                @endforelse
                            </div>
                        </section>
                    @endforeach
                </div>
            </main>
        </div>
    </div>

    {{-- Modal Detail --}}
    @if ($showDetailModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">
                        Detail Keluhan
                    </h2>

                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="text-xl font-bold text-gray-500 hover:text-gray-800"
                    >
                        ×
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs text-gray-500">No. Laporan</p>
                        <p class="font-semibold text-gray-900">{{ $detailData['no_laporan'] ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Lab</p>
                        <p class="font-semibold text-gray-900">{{ $detailData['lab'] ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Pelapor</p>
                        <p class="font-semibold text-gray-900">{{ $detailData['pelapor'] ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">NIM</p>
                        <p class="font-semibold text-gray-900">{{ $detailData['nim'] ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Fakultas</p>
                        <p class="font-semibold text-gray-900">{{ $detailData['fakultas'] ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Kategori</p>
                        <p class="font-semibold text-gray-900">{{ $detailData['kategori'] ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500">Catatan Keluhan</p>
                        <p class="mt-1 rounded-lg bg-gray-100 p-3 text-sm text-gray-900">
                            {{ $detailData['catatan_lpr'] ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="rounded-lg bg-gray-700 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Selesaikan --}}
    @if ($showSelesaiModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">
                        Selesaikan Perbaikan
                    </h2>

                    <button
                        type="button"
                        wire:click="closeSelesai"
                        class="text-xl font-bold text-gray-500 hover:text-gray-800"
                    >
                        ×
                    </button>
                </div>

                <form wire:submit.prevent="selesaikanPerbaikan" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">
                            Bukti Perbaikan <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="file"
                            wire:model="ft_perbaikan"
                            accept="image/*"
                            class="w-full rounded-lg border border-gray-300 p-2 text-sm text-gray-900"
                        >

                        @error('ft_perbaikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">
                            Catatan Perbaikan <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            wire:model="catatan_pbk"
                            rows="4"
                            class="w-full rounded-lg border border-gray-300 p-2 text-sm text-gray-900"
                            placeholder="Contoh: PC sudah diperbaiki dan sudah bisa digunakan kembali."
                        ></textarea>

                        @error('catatan_pbk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="closeSelesai"
                            class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700"
                        >
                            Simpan Selesai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
