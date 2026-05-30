<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Total Laporan</p>
            <p class="mt-2 text-3xl font-bold">{{ $totalLaporan }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Menunggu Approval</p>
            <p class="mt-2 text-3xl font-bold">{{ $laporanMenunggu }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Laporan Disetujui</p>
            <p class="mt-2 text-3xl font-bold">{{ $laporanDisetujui }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Laporan Ditolak</p>
            <p class="mt-2 text-3xl font-bold">{{ $laporanDitolak }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Antrean</p>
            <p class="mt-2 text-3xl font-bold">{{ $perbaikanAntrean }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Dikerjakan</p>
            <p class="mt-2 text-3xl font-bold">{{ $perbaikanDikerjakan }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Menunggu Sparepart</p>
            <p class="mt-2 text-3xl font-bold">{{ $menungguSparepart }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Selesai</p>
            <p class="mt-2 text-3xl font-bold">{{ $perbaikanSelesai }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Menunggu Validasi</p>
            <p class="mt-2 text-3xl font-bold">{{ $menungguValidasi }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Divalidasi</p>
            <p class="mt-2 text-3xl font-bold">{{ $perbaikanDivalidasi }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500">Dikembalikan</p>
            <p class="mt-2 text-3xl font-bold">{{ $perbaikanDikembalikan }}</p>
        </div>
    </div>
</x-filament-panels::page>