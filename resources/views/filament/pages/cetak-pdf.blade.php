<x-filament-panels::page>
    <x-filament::card>
        <form wire:submit.prevent="cetak">
            {{ $this->form }}
            <div class="mt-4">
                <x-filament::button type="submit" icon="heroicon-o-printer">
                    Cetak PDF
                </x-filament::button>
            </div>
        </form>
    </x-filament::card>
</x-filament-panels::page>