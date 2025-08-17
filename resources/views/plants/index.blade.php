@php
    use App\Enums\PlantCategoryEnum;
    use App\Enums\PlantTypeEnum;
@endphp

<x-layouts.page 
    title="Pflanzen entdecken" 
    subtitle="Durchsuche unsere vielfältige Sammlung von Pflanzen"
>
    <div class="space-y-6">
        <!-- Search and Filters -->
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
            <form method="GET" action="{{ route('plants.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div>
                    <label for="search" class="block text-sm font-medium text-foreground mb-2">
                        Suchen
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="search"
                            name="search" 
                            value="{{ $search }}"
                            placeholder="Nach Name, lateinischem Namen oder Beschreibung suchen..."
                            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Plant Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-foreground mb-2">
                            Pflanzentyp
                        </label>
                        <select 
                            id="type" 
                            name="type" 
                            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                            <option value="">Alle Typen</option>
                            @foreach($plantTypes as $plantType)
                                <option 
                                    value="{{ $plantType->value }}" 
                                    {{ $selectedType === $plantType->value ? 'selected' : '' }}
                                >
                                    {{ $plantType->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Categories Filter -->
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-2">
                            Kategorien
                        </label>
                        <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto bg-background border border-border rounded-lg p-3">
                            @foreach($plantCategories as $category)
                                <label class="flex items-center space-x-2 text-sm hover:bg-muted rounded p-1 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="categories[]" 
                                        value="{{ $category->value }}"
                                        {{ in_array($category->value, $selectedCategories) ? 'checked' : '' }}
                                        class="rounded border-border text-primary focus:ring-primary focus:ring-offset-0"
                                    >
                                    <span class="text-foreground">{{ $category->getLabel() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Filter Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors font-medium"
                    >
                        Filter anwenden
                    </button>
                    <a 
                        href="{{ route('plants.index') }}" 
                        class="px-6 py-2 bg-secondary text-secondary-foreground rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors font-medium text-center"
                    >
                        Filter zurücksetzen
                    </a>
                </div>
            </form>
        </div>

        <!-- Results Info -->
        <div class="flex items-center justify-between">
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
    </div>
</x-layouts.page>