<x-filament-panels::page>
    @php
        $cards = [
            [
                'label' => 'Total Tugas',
                'value' => $totalTugas,
                'desc' => 'Semua tugas perbaikan',
                'icon' => 'heroicon-o-clipboard-document-list',
                'iconBg' => 'bg-sky-100 text-sky-600 dark:bg-sky-500/15 dark:text-sky-300',
                'accent' => 'from-sky-500/10 to-transparent',
                'ring' => 'ring-sky-200/70 dark:ring-sky-400/20',
            ],
            [
                'label' => 'Antrean',
                'value' => $totalAntrean,
                'desc' => 'Menunggu dikerjakan',
                'icon' => 'heroicon-o-clock',
                'iconBg' => 'bg-amber-100 text-amber-600 dark:bg-amber-500/15 dark:text-amber-300',
                'accent' => 'from-amber-500/10 to-transparent',
                'ring' => 'ring-amber-200/70 dark:ring-amber-400/20',
            ],
            [
                'label' => 'Dikerjakan',
                'value' => $totalDikerjakan,
                'desc' => 'Sedang dalam proses',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'iconBg' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/15 dark:text-indigo-300',
                'accent' => 'from-indigo-500/10 to-transparent',
                'ring' => 'ring-indigo-200/70 dark:ring-indigo-400/20',
            ],
            [
                'label' => 'Menunggu Sparepart',
                'value' => $totalMenungguSparepart,
                'desc' => 'Menunggu ketersediaan barang',
                'icon' => 'heroicon-o-exclamation-triangle',
                'iconBg' => 'bg-orange-100 text-orange-600 dark:bg-orange-500/15 dark:text-orange-300',
                'accent' => 'from-orange-500/10 to-transparent',
                'ring' => 'ring-orange-200/70 dark:ring-orange-400/20',
            ],
            [
                'label' => 'Selesai',
                'value' => $totalSelesai,
                'desc' => 'Perbaikan telah selesai',
                'icon' => 'heroicon-o-check-circle',
                'iconBg' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/15 dark:text-emerald-300',
                'accent' => 'from-emerald-500/10 to-transparent',
                'ring' => 'ring-emerald-200/70 dark:ring-emerald-400/20',
            ],
            [
                'label' => 'Menunggu Validasi SPV',
                'value' => $totalMenungguValidasi,
                'desc' => 'Belum divalidasi SPV',
                'icon' => 'heroicon-o-shield-check',
                'iconBg' => 'bg-purple-100 text-purple-600 dark:bg-purple-500/15 dark:text-purple-300',
                'accent' => 'from-purple-500/10 to-transparent',
                'ring' => 'ring-purple-200/70 dark:ring-purple-400/20',
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
            <div
                class="group relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 {{ $card['ring'] }} transition duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-gray-900"
            >
                <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b {{ $card['accent'] }}"></div>

                <div class="relative flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $card['label'] }}
                        </p>

                        <p class="mt-3 text-4xl font-bold tracking-tight text-gray-950 dark:text-white">
                            {{ $card['value'] }}
                        </p>

                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $card['desc'] }}
                        </p>
                    </div>

                    <div
                        class="flex h-13 w-13 items-center justify-center rounded-2xl {{ $card['iconBg'] }} shadow-sm transition duration-300 group-hover:scale-110"
                    >
                        <x-filament::icon
                            :icon="$card['icon']"
                            class="h-7 w-7"
                        />
                    </div>
                </div>

                <div class="pointer-events-none absolute -right-8 -bottom-8 h-24 w-24 rounded-full bg-gray-950/[0.03] dark:bg-white/[0.04]"></div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            @livewire(\App\Filament\Widgets\AdminPerbaikanStatusChart::class)
        </div>

        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            @livewire(\App\Filament\Widgets\AdminValidasiStatusChart::class)
        </div>
    </div>

    <div class="mt-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        @livewire(\App\Filament\Widgets\AdminTugasMingguanChart::class)
    </div>
</x-filament-panels::page>