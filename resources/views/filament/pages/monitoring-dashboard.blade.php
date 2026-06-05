<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Laporan</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $totalLaporan }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Approval</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $laporanMenunggu }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Laporan Disetujui</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $laporanDisetujui }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Laporan Ditolak</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $laporanDitolak }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Antrean</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $perbaikanAntrean }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Dikerjakan</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $perbaikanDikerjakan }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Sparepart</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $menungguSparepart }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $perbaikanSelesai }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Validasi</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $menungguValidasi }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Divalidasi</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $perbaikanDivalidasi }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Dikembalikan</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $perbaikanDikembalikan }}</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
        @livewire(\App\Filament\Widgets\LaporanApprovalChart::class)

        @livewire(\App\Filament\Widgets\PerbaikanStatusChart::class)
    </div>

    <div class="mt-4">
        @livewire(\App\Filament\Widgets\LaporanBulananChart::class)
    </div>
</x-filament-panels::page>