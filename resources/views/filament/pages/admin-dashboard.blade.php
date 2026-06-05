<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Tugas</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $totalTugas }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Antrean</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $totalAntrean }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Dikerjakan</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $totalDikerjakan }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Sparepart</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $totalMenungguSparepart }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $totalSelesai }}</p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Validasi SPV</p>
            <p class="mt-2 text-3xl font-bold text-gray-950 dark:text-white">{{ $totalMenungguValidasi }}</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
        @livewire(\App\Filament\Widgets\AdminPerbaikanStatusChart::class)

        @livewire(\App\Filament\Widgets\AdminValidasiStatusChart::class)
    </div>

    <div class="mt-4">
        @livewire(\App\Filament\Widgets\AdminTugasMingguanChart::class)
    </div>
</x-filament-panels::page>