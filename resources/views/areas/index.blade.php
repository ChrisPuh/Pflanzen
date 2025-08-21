<x-layouts.index
    title="{{ $isAdmin ? 'Alle Bereiche' : 'Meine Bereiche' }}"
    subtitle="{{ $isAdmin ? 'Übersicht aller Bereiche im System' : 'Verwalte deine Gartenbereiche' }}"
>
    <x-slot:actions>
        <a
            href="{{ route('areas.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Neuen Bereich erstellen
        </a>
    </x-slot:actions>
    <x-slot:stats>
        <x-stats-grid :stats="[
            [
                'label' => 'Gesamt Bereiche',
                'value' => $totalAreas,
                'iconComponent' => 'heroicon-o-square-3-stack-3d',
                'iconClass' => 'text-green-600 dark:text-green-400',
                'iconBg' => 'bg-green-100 dark:bg-green-900/20'
            ],
            [
                'label' => 'Aktive Bereiche',
                'value' => $activeAreas,
                'iconComponent' => 'heroicon-o-check-circle',
                'iconClass' => 'text-blue-600 dark:text-blue-400',
                'iconBg' => 'bg-blue-100 dark:bg-blue-900/20'
            ],
            [
                'label' => 'Pflanzflächen',
                'value' => $plantingAreas,
                'iconComponent' => 'heroicon-o-sparkles',
                'iconClass' => 'text-purple-600 dark:text-purple-400',
                'iconBg' => 'bg-purple-100 dark:bg-purple-900/20'
            ]
        ]" />
    </x-slot:stats>
    <x-slot:filters>
        <x-filter-card action="{{ route('areas.index') }}">
            <!-- Search Bar -->
            <x-forms.input
                label="Suchen"
                name="search"
                type="search"
                :value="$filters['search']"
                placeholder="Nach Name oder Beschreibung suchen..."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Garden Filter -->
                <x-forms.select
                    label="Garten"
                    name="garden_id"
                    placeholder="Alle Gärten"
                    :selected="$filters['garden_id']"
                    :options="$gardenOptions"
                />

                <!-- Type Filter -->
                <x-forms.select
                    label="Typ"
                    name="type"
                    placeholder="Alle Typen"
                    :selected="$filters['type']"
                    :options="$areaTypeOptions"
                />

                <!-- Category Filter -->
                <x-forms.select
                    label="Kategorie"
                    name="category"
                    placeholder="Alle Kategorien"
                    :selected="$filters['category']"
                    :options="$areaCategoryOptions"
                />
            </div>

            <!-- Active Status Filter -->
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-foreground">Status:</span>
                <label class="flex items-center">
                    <input type="radio" name="active" value=""
                           {{ $filters['active'] === null ? 'checked' : '' }}
                           class="h-4 w-4 text-primary focus:ring-primary border-border">
                    <span class="ml-2 text-sm text-foreground">Alle</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="active" value="1"
                           {{ $filters['active'] === true ? 'checked' : '' }}
                           class="h-4 w-4 text-primary focus:ring-primary border-border">
                    <span class="ml-2 text-sm text-foreground">Aktiv</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="active" value="0"
                           {{ $filters['active'] === false ? 'checked' : '' }}
                           class="h-4 w-4 text-primary focus:ring-primary border-border">
                    <span class="ml-2 text-sm text-foreground">Inaktiv</span>
                </label>
            </div>
        </x-filter-card>
    </x-slot:filters>

    <!-- Results Info -->
    <div class="flex items-center justify-between">
        <p class="text-muted-foreground">
            {{ $areas->total() }} {{ $areas->total() === 1 ? 'Bereich' : 'Bereiche' }} gefunden
            @if($isAdmin)
                <span class="text-orange-600 dark:text-orange-400 font-medium">(Admin-Ansicht)</span>
            @endif
        </p>
        @if($areas->hasPages())
            <p class="text-sm text-muted-foreground">
                Seite {{ $areas->currentPage() }} von {{ $areas->lastPage() }}
            </p>
        @endif
    </div>

    <!-- Areas Grid -->
    @if($areas->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($areas as $area)
                <x-area.index-card :area="$area" :isAdmin="$isAdmin" />
            @endforeach
        </div>

        <!-- Pagination -->
        @if($areas->hasPages())
            <div class="flex justify-center">
                {{ $areas->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-16">
            <svg class="mx-auto h-24 w-24 text-muted-foreground/50" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-foreground mt-4">Keine Bereiche gefunden</h3>
            <p class="text-muted-foreground mt-2">
                @if(request()->hasAny(['search', 'garden_id', 'type', 'category', 'active']))
                    Keine Bereiche entsprechen den gewählten Filtern. Versuche deine Suchkriterien zu ändern oder die
                    Filter zurückzusetzen.
                @else
                    Es sind noch keine Bereiche angelegt.
                @endif
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center mt-6">
                @if(request()->hasAny(['search', 'garden_id', 'type', 'category', 'active']))
                    <a
                        href="{{ route('areas.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-secondary text-secondary-foreground rounded-lg hover:bg-secondary/80 transition-colors"
                    >
                        Alle Bereiche anzeigen
                    </a>
                @else
                    <a
                        href="{{ route('areas.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"></path>
                        </svg>
                        Neuen Bereich erstellen
                    </a>
                    <a
                        href="{{ route('gardens.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-secondary text-secondary-foreground rounded-lg hover:bg-secondary/80 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Gärten verwalten
                    </a>
                @endif
            </div>
        </div>
    @endif


</x-layouts.index>
<!-- Empty State -->

