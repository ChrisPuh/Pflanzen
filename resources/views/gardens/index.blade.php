@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.page 
    title="Meine Gärten" 
    subtitle="Verwalte und durchstöbere deine Gärten"
>
    <x-slot:actions>
        <a
            href="{{ route('gardens.create') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Neuen Garten erstellen
        </a>
    </x-slot:actions>

    <div class="space-y-6">
        <!-- Gardens Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-muted-foreground truncate">
                                Gesamte Gärten
                            </dt>
                            <dd class="text-lg font-medium text-foreground">
                                {{ $gardens->total() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-muted-foreground truncate">
                                Gesamte Pflanzen
                            </dt>
                            <dd class="text-lg font-medium text-foreground">
                                {{ $gardens->sum(function($garden) { return $garden->plants->count(); }) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-muted-foreground truncate">
                                Aktive Gärten
                            </dt>
                            <dd class="text-lg font-medium text-foreground">
                                {{ $gardens->where('is_active', true)->count() }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="flex items-center justify-between">
            <p class="text-muted-foreground">
                {{ $gardens->total() }} {{ $gardens->total() === 1 ? 'Garten' : 'Gärten' }} gefunden
                @if($isAdmin)
                    <span class="text-orange-600 dark:text-orange-400 font-medium">(Admin-Ansicht)</span>
                @endif
            </p>
            @if($gardens->hasPages())
                <p class="text-sm text-muted-foreground">
                    Seite {{ $gardens->currentPage() }} von {{ $gardens->lastPage() }}
                </p>
            @endif
        </div>

        <!-- Gardens Grid -->
        @if($gardens->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($gardens as $garden)
                    <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm hover:shadow-md transition-all group transform hover:scale-[1.02] relative">
                        <!-- Edit Button -->
                        @can('update', $garden)
                            <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a
                                    href="{{ route('gardens.edit', $garden) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-white/90 dark:bg-gray-900/90 rounded-full hover:bg-white dark:hover:bg-gray-900 transition-colors shadow-sm"
                                    title="Garten bearbeiten"
                                    onclick="event.stopPropagation()"
                                >
                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            </div>
                        @endcan
                        
                        <a 
                            href="{{ route('gardens.show', $garden) }}" 
                            class="block cursor-pointer"
                        >
                        <!-- Garden Header -->
                        <div class="aspect-video bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/20 dark:to-green-800/20 flex items-center justify-center relative">
                            @if($garden->hasCoordinates())
                                <div class="absolute top-3 right-3">
                                    <div class="bg-white/90 dark:bg-gray-900/90 rounded-full p-1.5">
                                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="text-center">
                                <svg class="h-12 w-12 text-green-600 dark:text-green-400 mx-auto group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
                                </svg>
                            </div>

                            <!-- Status Indicator -->
                            <div class="absolute top-3 left-3">
                                <div class="flex items-center gap-1">
                                    <div class="w-2.5 h-2.5 rounded-full {{ $garden->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 bg-white/90 dark:bg-gray-900/90 px-2 py-1 rounded">
                                        {{ $garden->is_active ? 'Aktiv' : 'Inaktiv' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            <!-- Garden Info -->
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">
                                        {{ $garden->name }}
                                    </h3>
                                    <p class="text-sm text-muted-foreground">
                                        {{ $garden->type->getLabel() }}
                                    </p>
                                </div>
                                
                                @if($isAdmin && $garden->user_id !== auth()->id())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300">
                                        {{ $garden->user->name ?? 'Unbekannt' }}
                                    </span>
                                @endif
                            </div>

                            <!-- Garden Stats -->
                            <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                @if($garden->size_sqm)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                        <span>{{ $garden->formatted_size }}</span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                                    </svg>
                                    <span>{{ $garden->plants_count ?? $garden->plants->count() }} Pflanzen</span>
                                </div>
                            </div>

                            <!-- Location -->
                            @if($garden->full_location !== 'Standort nicht angegeben')
                                <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="truncate">{{ $garden->full_location }}</span>
                                </div>
                            @endif

                            <!-- Age -->
                            @if($garden->age_in_years !== null)
                                <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $garden->age_in_years }} {{ $garden->age_in_years === 1 ? 'Jahr' : 'Jahre' }} alt</span>
                                </div>
                            @endif

                            <!-- View Details Link -->
                            <div class="pt-2">
                                <span class="text-sm font-medium text-primary group-hover:underline">
                                    Garten anzeigen →
                                </span>
                            </div>
                        </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($gardens->hasPages())
                <div class="flex justify-center">
                    {{ $gardens->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
                </svg>
                <h3 class="text-lg font-medium text-foreground mt-4">Keine Gärten gefunden</h3>
                <p class="text-muted-foreground mt-2">
                    Du hast noch keine Gärten erstellt.
                </p>
                <a 
                    href="{{ route('gardens.create') }}" 
                    class="inline-flex items-center px-4 py-2 mt-4 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ersten Garten erstellen
                </a>
            </div>
        @endif
    </div>
</x-layouts.page>