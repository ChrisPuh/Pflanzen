<div>
    <!-- Trigger Button -->
    <x-ui.action-button
        variant="secondary"
        wire:click="openModal"
        :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4v16m8-8H4\'></path>'"
    >
        Pflanzen hinzufügen
    </x-ui.action-button>

    <!-- Modal -->
    <div
        x-show="$wire.showModal"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex min-h-screen items-center justify-center p-4">
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-surface-2 opacity-75 transition-opacity"
                wire:click="closeModal"
            ></div>

            <!-- Modal Content -->
            <div
                x-show="$wire.showModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative bg-surface rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden z-10"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-default">
                    <div>
                        <h3 class="text-lg font-semibold text-foreground">
                            Pflanzen hinzufügen
                        </h3>
                        <p class="text-sm text-muted mt-1">
                            Wähle Pflanzen für "{{ $area->name }}" aus
                        </p>
                    </div>
                    <x-ui.action-button
                        variant="secondary"
                        size="sm"
                        wire:click="closeModal"
                        :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M6 18L18 6M6 6l12 12\'></path>'"
                        class="p-2"
                    />
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-hidden">
                    @livewire('area.plant-selection', ['area' => $area], key($area->id))
                </div>
            </div>
        </div>
    </div>
</div>
