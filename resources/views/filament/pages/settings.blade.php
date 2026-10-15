<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}
        <div class="mt-6">
            <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="save">
                Save changes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
