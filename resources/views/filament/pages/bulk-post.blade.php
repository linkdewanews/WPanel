<x-filament-panels::page>
    {{-- Form ini akan memanggil method `publishBulk` di class BulkPost.php saat disubmit --}}
    <form wire:submit="publishBulk">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                {{-- Tampilkan spinner saat proses berjalan --}}
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="publishBulk" />

                {{-- Teks tombol --}}
                <span>Publikasikan ke Situs Terpilih</span>
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>