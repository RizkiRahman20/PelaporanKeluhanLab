<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Tugas</p>
            <p class="mt-2 text-3xl font-bold">{{ $totalTugas }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Antrean</p>
            <p class="mt-2 text-3xl font-bold">{{ $totalAntrean }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Dikerjakan</p>
            <p class="mt-2 text-3xl font-bold">{{ $totalDikerjakan }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Sparepart</p>
            <p class="mt-2 text-3xl font-bold">{{ $totalMenungguSparepart }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
            <p class="mt-2 text-3xl font-bold">{{ $totalSelesai }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Validasi SPV</p>
            <p class="mt-2 text-3xl font-bold">{{ $totalMenungguValidasi }}</p>
        </div>
    </div>
</x-filament-panels::page>