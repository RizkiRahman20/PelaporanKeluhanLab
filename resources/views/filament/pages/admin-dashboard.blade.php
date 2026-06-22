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
            // ... (card lainnya biarkan sama seperti kode Anda) ...
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

    <div class="mb-6 flex flex-col gap-y-1">
        <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
            {{ $welcomeMessage ?? 'Selamat datang, Admin' }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Berikut adalah ringkasan status tugas perbaikan di lab Anda hari ini.
        </p>
    </div>
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