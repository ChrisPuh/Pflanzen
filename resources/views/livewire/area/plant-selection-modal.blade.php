<div>
    <!-- Trigger Button -->
    <x-ui.action-button
        variant="secondary"
        wire:click="openModal"
        :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4v16m8-8H4\'></path>'"
    >
        Pflanzen hinzufügen
    </x-ui.action-button>

    <!-- Modal Backdrop and Container -->
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
                    >
                    </x-ui.action-button>
                </div>

                <!-- Search and Filter Section -->
                <div class="p-6 border-b border-default bg-surface-2">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Search Input -->
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted z-10"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <x-forms.input
                                    name="search"
                                    type="search"
                                    placeholder="Nach Pflanzen suchen..."
                                    wire:model.live.debounce.300ms="search"
                                    class="pl-10"
                                    :label="null"
                                />
                            </div>
                        </div>

                        <!-- Plant Type Filter -->
                        <div class="md:w-64">
                            <x-forms.select
                                name="plant_type_filter"
                                placeholder="Alle Pflanzentypen"
                                wire:model.live="selectedPlantTypeId"
                                :options="$this->plantTypeOptions"
                            />
                        </div>

                        <!-- Clear Filters -->
                        <x-ui.action-button
                            variant="secondary"
                            size="md"
                            wire:click="clearFilters"
                            class="whitespace-nowrap"
                        >
                            Filter zurücksetzen
                        </x-ui.action-button>
                    </div>

                    <!-- Selected Plants Counter -->
                    @if(count($selectedPlants) > 0)
                        <div class="mt-4 p-3 bg-primary-soft rounded-lg">
                            <p class="text-sm text-primary font-medium">
                                {{ count($selectedPlants) }} {{ count($selectedPlants) === 1 ? 'Pflanze' : 'Pflanzen' }}
                                ausgewählt
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Plants Grid -->
                <div class="flex-1 overflow-y-auto max-h-96 p-6">
                    @if($this->availablePlants->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($this->availablePlants as $plant)
                                <div
                                    class="p-4 rounded-lg border border-default transition-all duration-200 cursor-pointer {{ isset($selectedPlants[$plant->id]) ? 'bg-primary-soft border-primary ring-2 ring-primary' : 'bg-surface hover:border-primary' }}"
                                    wire:click="togglePlant({{ $plant->id }})"
                                >
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-foreground">
                                                {{ $plant->name }}
                                            </h4>
                                            @if($plant->latin_name)
                                                <p class="text-xs text-muted italic">
                                                    {{ $plant->latin_name }}
                                                </p>
                                            @endif
                                            @if($plant->plantType)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-surface-2 text-foreground mt-1">
                                                    {{ $plant->plantType->name->getLabel() }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if(isset($selectedPlants[$plant->id]))
                                                <div
                                                    class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-5 h-5 rounded-full border-2 border-muted"></div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($plant->description)
                                        <p class="text-sm text-muted mb-3 line-clamp-2">
                                            {{ $plant->description }}
                                        </p>
                                    @endif

                                    <!-- Selected Plant Options -->
                                    @if(isset($selectedPlants[$plant->id]))
                                        <div class="space-y-3 pt-3 border-t border-default" wire:click.stop>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <x-forms.input
                                                        label="Anzahl"
                                                        name="quantity_{{ $plant->id }}"
                                                        type="number"
                                                        placeholder="1"
                                                        wire:model.live="selectedPlants.{{ $plant->id }}.quantity"
                                                        wire:change="updateQuantity({{ $plant->id }}, $event.target.value)"
                                                        labelClass="text-xs"
                                                        class="text-sm px-2 py-1"
                                                        min="1"
                                                        max="9999"
                                                    />
                                                </div>
                                                <div></div>
                                            </div>
                                            <div>
                                                <x-forms.input
                                                    label="Notizen (optional)"
                                                    name="notes_{{ $plant->id }}"
                                                    type="text"
                                                    placeholder="z.B. Position, Besonderheiten..."
                                                    wire:model.live="selectedPlants.{{ $plant->id }}.notes"
                                                    wire:change="updateNotes({{ $plant->id }}, $event.target.value)"
                                                    labelClass="text-xs"
                                                    class="text-sm px-2 py-1"
                                                    maxlength="500"
                                                />
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-muted mx-auto mb-3" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-foreground mb-2">Keine Pflanzen gefunden</h3>
                            <p class="text-muted">
                                @if(!empty($search) || $selectedPlantTypeId)
                                    Keine Pflanzen entsprechen den Suchkriterien.
                                @else
                                    Alle verfügbaren Pflanzen sind bereits in diesem Bereich.
                                @endif
                            </p>
                            @if(!empty($search) || $selectedPlantTypeId)
                                <x-ui.action-button
                                    variant="secondary"
                                    size="sm"
                                    wire:click="clearFilters"
                                    class="mt-2"
                                >
                                    Filter zurücksetzen →
                                </x-ui.action-button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-between p-6 border-t border-default bg-surface-2">
                    <x-ui.action-button
                        variant="secondary"
                        wire:click="closeModal"
                    >
                        Abbrechen
                    </x-ui.action-button>

                    <x-button
                        type="primary"
                        wire:click="addSelectedPlants"
                        x-bind:disabled="Object.keys($wire.selectedPlants).length === 0"
                        class="disabled:opacity-50 disabled:cursor-not-allowed"


                    >
                        @if(count($selectedPlants) > 0)
                            {{ count($selectedPlants) }} {{ count($selectedPlants) === 1 ? 'Pflanze' : 'Pflanzen' }}
                            hinzufügen
                        @else
                            Pflanzen hinzufügen
                        @endif
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</div>
