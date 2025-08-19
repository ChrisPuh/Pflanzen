<x-layouts.page
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
                    :options="$userGardens->mapWithKeys(function($garden) use ($isAdmin) {
                        $label = $garden->name;
                        if ($isAdmin) {
                            $label .= ' (' . $garden->type->getLabel() . ')';
                        }
                        return [$garden->id => $label];
                    })"
                />

                <!-- Type Filter -->
                <x-forms.select
                    label="Typ"
                    name="type"
                    placeholder="Alle Typen"
                    :selected="$filters['type']"
                    :options="collect($areaTypes)->mapWithKeys(function($type) {
                        return [$type['value'] => $type['label']];
                    })"
                />

                <!-- Category Filter -->
                <x-forms.select
                    label="Kategorie"
                    name="category"
                    placeholder="Alle Kategorien"
                    :selected="$filters['category']"
                    :options="$areaCategories->mapWithKeys(function($category) {
                        return [$category => $category];
                    })"
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
                <a
                    href="{{ route('areas.show', $area) }}"
                    class="bg-card rounded-xl border border-border overflow-hidden shadow-sm hover:shadow-md transition-all group cursor-pointer transform hover:scale-[1.02] relative"
                >
                    <!-- Area Header with Color -->
                    <div
                        class="aspect-video bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center relative"
                        style="background: linear-gradient(135deg, {{ $area->getDisplayColor() }}20, {{ $area->getDisplayColor() }}40)">

                        <!-- Status Indicator -->
                        <div class="absolute top-3 left-3">
                            <div class="flex items-center gap-1">
                                <div
                                    class="w-2.5 h-2.5 rounded-full {{ $area->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                <span
                                    class="text-xs font-medium text-gray-700 dark:text-gray-300 bg-white/90 dark:bg-gray-900/90 px-2 py-1 rounded">
                                        {{ $area->is_active ? 'Aktiv' : 'Inaktiv' }}
                                    </span>
                            </div>
                        </div>

                        <!-- Position Indicator -->
                        @if($area->hasCoordinates())
                            <div class="absolute top-3 right-3">
                                <div class="bg-white/90 dark:bg-gray-900/90 rounded-full p-1.5">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        @endif

                        <div class="text-center">
                            <svg class="h-12 w-12 text-primary/60 group-hover:text-primary transition-colors mx-auto"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        <!-- Area Info -->
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">
                                    {{ $area->name }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    {{ $area->type->getLabel() }}
                                    <span class="mx-1">•</span>
                                    <span>{{ $area->type->category() }}</span>
                                </p>
                            </div>

                            @if($isAdmin && $area->garden->user_id !== auth()->id())
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300">
                                        {{ $area->garden->user->name ?? 'Unbekannt' }}
                                    </span>
                            @endif
                        </div>

                        <!-- Garden Link -->
                        <div class="flex items-center gap-1 text-sm text-muted-foreground">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span class="truncate hover:text-primary transition-colors">{{ $area->garden->name }}</span>
                        </div>

                        <!-- Description -->
                        @if($area->description)
                            <p class="text-sm text-muted-foreground line-clamp-2">
                                {{ $area->description }}
                            </p>
                        @endif

                        <!-- Area Stats -->
                        <div class="flex items-center gap-4 text-sm text-muted-foreground">
                            @if($area->size_sqm)
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                    </svg>
                                    <span>{{ $area->formatted_size }}</span>
                                </div>
                            @endif

                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                                </svg>
                                <span>{{ $area->plants->count() }} Pflanzen</span>
                            </div>
                        </div>

                        <!-- Coordinates -->
                        @if($area->hasCoordinates())
                            <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Position: X{{ $area->getXCoordinate() }}, Y{{ $area->getYCoordinate() }}</span>
                            </div>
                        @endif

                        <!-- View Details Link -->
                        <div class="pt-2">
                                <span class="text-sm font-medium text-primary group-hover:underline">
                                    Bereich anzeigen →
                                </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($areas->hasPages())
            <div class="flex justify-center">
                {{ $areas->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <svg class="mx-auto h-24 w-24 text-muted-foreground/50" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-foreground mt-4">Keine Bereiche gefunden</h3>
            <p class="text-muted-foreground mt-2">
                @if(request()->hasAny(['search', 'garden_id', 'type', 'category', 'active']))
                    Keine Bereiche entsprechen den gewählten Filtern. Versuche deine Suchkriterien zu ändern oder die Filter zurückzusetzen.
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
        </div>


</x-layouts.page>
