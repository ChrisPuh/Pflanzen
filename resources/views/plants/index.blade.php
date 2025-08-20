<x-layouts.index
    title="Pflanzen entdecken"
    subtitle="Durchsuche unsere vielfältige Sammlung von Pflanzen"
>
    <x-slot:stats>
        <x-stats-grid :stats="[
            [
                'label' => 'Gesamt Pflanzen',
                'value' => $stats['total'],
                'iconComponent' => 'heroicon-o-sparkles',
                'iconClass' => 'text-green-600 dark:text-green-400',
                'iconBg' => 'bg-green-100 dark:bg-green-900/20'
            ],
            [
                'label' => 'Pflanztypen',
                'value' => count($stats['by_type']),
                'iconComponent' => 'heroicon-o-squares-2x2',
                'iconClass' => 'text-blue-600 dark:text-blue-400',
                'iconBg' => 'bg-blue-100 dark:bg-blue-900/20'
            ],
            [
                'label' => 'Kategorien',
                'value' => count($stats['by_category']),
                'iconComponent' => 'heroicon-o-tag',
                'iconClass' => 'text-purple-600 dark:text-purple-400',
                'iconBg' => 'bg-purple-100 dark:bg-purple-900/20'
            ]
        ]" />
    </x-slot:stats>

    <x-slot:filters>
        <x-filter-card action="{{ route('plants.index') }}">
            <!-- Search Bar -->
            <x-forms.input
                label="Suchen"
                name="search"
                type="search"
                :value="$search"
                placeholder="Nach Name, lateinischem Namen oder Beschreibung suchen..."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Plant Type Filter -->
                <x-forms.select
                    label="Pflanzentyp"
                    name="type"
                    placeholder="Alle Typen"
                    :selected="$selectedType"
                    :options="$plantTypesOptions"
                />

                <!-- Categories Filter -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">
                        Kategorien
                    </label>
                    <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto bg-background border border-border rounded-lg p-3">
                        @foreach($plantCategoriesOptions as $value => $label)
                            <label class="flex items-center space-x-2 text-sm hover:bg-muted rounded p-1 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="categories[]" 
                                    value="{{ $value }}"
                                    {{ in_array($value, $selectedCategories) ? 'checked' : '' }}
                                    class="rounded border-border text-primary focus:ring-primary focus:ring-offset-0"
                                >
                                <span class="text-foreground">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-filter-card>
    </x-slot:filters>

    <!-- Results Info -->
    <div class="flex items-center justify-between mb-6">
        <p class="text-muted-foreground">
            {{ $plants->total() }} {{ $plants->total() === 1 ? 'Pflanze' : 'Pflanzen' }} gefunden
            @if($search)
                für "<span class="font-medium text-foreground">{{ $search }}</span>"
            @endif
        </p>
        @if($plants->hasPages())
            <p class="text-sm text-muted-foreground">
                Seite {{ $plants->currentPage() }} von {{ $plants->lastPage() }}
            </p>
        @endif
    </div>

        <!-- Plants Grid -->
        @if($plants->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($plants as $plant)
                    <a 
                        href="{{ route('plants.show', $plant) }}" 
                        class="bg-card rounded-xl border border-border overflow-hidden shadow-sm hover:shadow-md transition-all group cursor-pointer transform hover:scale-[1.02]"
                    >
                        <!-- Plant Image Placeholder -->
                        <div class="aspect-square bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center">
                            <svg class="h-16 w-16 text-primary/60 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                            </svg>
                        </div>

                        <div class="p-4 space-y-3">
                            <!-- Plant Name -->
                            <div>
                                <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">
                                    {{ $plant->name }}
                                </h3>
                                @if($plant->latin_name)
                                    <p class="text-sm text-muted-foreground italic">
                                        {{ $plant->latin_name }}
                                    </p>
                                @endif
                            </div>

                            <!-- Plant Type Badge -->
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ $plant->plantType->name->getLabel() }}
                                </span>
                            </div>

                            <!-- Categories -->
                            @if($plant->categories->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($plant->categories->take(3) as $category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-secondary text-secondary-foreground">
                                            {{ $category->name->getLabel() }}
                                        </span>
                                    @endforeach
                                    @if($plant->categories->count() > 3)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-muted text-muted-foreground">
                                            +{{ $plant->categories->count() - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Description -->
                            @if($plant->description)
                                <p class="text-sm text-muted-foreground line-clamp-2">
                                    {{ $plant->description }}
                                </p>
                            @endif

                            <!-- View Details Link -->
                            <div class="pt-2">
                                <span class="text-sm font-medium text-primary group-hover:underline">
                                    Details anzeigen →
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($plants->hasPages())
                <div class="flex justify-center">
                    {{ $plants->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                </svg>
                <h3 class="text-lg font-medium text-foreground mt-4">Keine Pflanzen gefunden</h3>
                <p class="text-muted-foreground mt-2">
                    @if($search || $selectedType || count($selectedCategories) > 0)
                        Versuche deine Suchkriterien zu ändern oder die Filter zurückzusetzen.
                    @else
                        Es wurden noch keine Pflanzen hinzugefügt.
                    @endif
                </p>
                @if($search || $selectedType || count($selectedCategories) > 0)
                    <a 
                        href="{{ route('plants.index') }}" 
                        class="inline-flex items-center px-4 py-2 mt-4 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                    >
                        Filter zurücksetzen
                    </a>
                @endif
            </div>
        @endif
</x-layouts.index>