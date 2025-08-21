<!-- Areas in Garden -->
<x-ui.card title="Bereiche in diesem Garten">
    <x-slot:actions>
        @can('update', $garden)
            <x-ui.action-button 
                href="{{ route('areas.create', ['garden_id' => $garden->id]) }}"
                :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4v16m8-8H4\'></path>'"
            >
                Bereich hinzufügen
            </x-ui.action-button>
        @endcan
    </x-slot:actions>

    @if($garden->areas->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($garden->areas as $area)
                <a
                    href="{{ route('areas.show', $area) }}"
                    class="group block relative"
                >
                    <!-- Action Buttons -->
                    <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                        @can('update', $area)
                            <a
                                href="{{ route('areas.edit', $area) }}"
                                class="inline-flex items-center justify-center w-6 h-6 bg-white/90 dark:bg-gray-900/90 rounded-full hover:bg-white dark:hover:bg-gray-900 transition-colors shadow-sm"
                                title="Bereich bearbeiten"
                                onclick="event.stopPropagation()"
                            >
                                <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <button
                                onclick="event.stopPropagation(); if(confirm('Möchtest du den Bereich {{ json_encode($area->name) }} wirklich löschen? Er kann später wiederhergestellt werden.')) { document.getElementById('delete-area-form-garden-{{ $area->id }}').submit(); }"
                                class="inline-flex items-center justify-center w-6 h-6 bg-white/90 dark:bg-gray-900/90 rounded-full hover:bg-white dark:hover:bg-gray-900 transition-colors shadow-sm"
                                title="Bereich löschen"
                            >
                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>

                            <form id="delete-area-form-garden-{{ $area->id }}" method="POST" action="{{ route('areas.destroy', $area) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endcan
                    </div>
                    <div
                        class="flex items-center p-4 bg-secondary/30 hover:bg-secondary/50 rounded-lg transition-colors border border-border/50 hover:border-border">
                        <div class="flex-shrink-0">
                            @if($area->color)
                                <div class="w-4 h-4 rounded-full border border-border/50"
                                     style="background-color: {{ $area->color }}"></div>
                            @else
                                <div
                                    class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H7m0 0H3"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-sm font-medium text-foreground">{{ $area->name }}</h4>
                            <p class="text-xs text-muted-foreground">
                                {{ $area->type->getLabel() }}
                                @if($area->size_sqm)
                                    • {{ $area->formatted_size }}
                                @endif
                                @if($area->description)
                                    • {{ Str::limit($area->description, 50) }}
                                @endif
                            </p>
                            @if($area->plant_quantity > 0)
                                <p class="text-xs text-muted-foreground mt-1">
                                    {{ $area->plant_quantity }} {{ $area->plant_quantity === 1 ? 'Pflanze' : 'Pflanzen' }}
                                </p>
                            @endif
                        </div>
                        <div class="ml-3 flex-shrink-0">
                            <svg
                                class="w-5 h-5 text-muted-foreground group-hover:text-foreground transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>

                        <!-- Status Indicator -->
                        <div class="absolute bottom-2 left-4">
                            <x-ui.status-badge size="sm" :status="$area->is_active ? 'active' : 'inactive'" />
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Areas Summary -->
        @if($areasStats['total'] > 2)
            <div class="mt-4 pt-4 border-t border-border">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-6">
                        <span class="text-muted-foreground">
                            {{ $areasStats['total'] }} Bereiche gesamt
                        </span>
                        <span class="text-muted-foreground">
                            {{ $areasStats['active'] }} aktiv
                        </span>
                        @if($areasStats['planting'] > 0)
                            <span class="text-muted-foreground">
                                {{ $areasStats['planting'] }} Pflanzflächen
                            </span>
                        @endif
                    </div>
                    <a
                        href="{{ route('areas.index', ['garden_id' => $garden->id]) }}"
                        class="text-primary hover:text-primary/80 font-medium"
                    >
                        Alle anzeigen →
                    </a>
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-muted-foreground mx-auto mb-3" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H7m0 0H3"></path>
            </svg>
            <h3 class="text-lg font-medium text-foreground">Noch keine Bereiche</h3>
            <p class="text-muted-foreground mt-1">
                Erstelle den ersten Bereich für deinen Garten.
            </p>
            @can('update', $garden)
                <x-ui.action-button 
                    href="{{ route('areas.create', ['garden_id' => $garden->id]) }}"
                    class="mt-4"
                    :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4v16m8-8H4\'></path>'"
                >
                    Ersten Bereich erstellen
                </x-ui.action-button>
            @endcan
        </div>
    @endif
</x-ui.card>