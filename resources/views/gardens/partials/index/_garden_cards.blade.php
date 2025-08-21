<!-- Gardens Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($gardens as $garden)
        <div
            class="bg-card rounded-xl border border-border overflow-hidden shadow-sm hover:shadow-md transition-all group transform hover:scale-[1.02] relative">
            <!-- Edit Button -->
            @can('update', $garden)
                <div
                    class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a
                        href="{{ route('gardens.edit', $garden) }}"
                        class="inline-flex items-center justify-center w-8 h-8 bg-white/90 dark:bg-gray-900/90 rounded-full hover:bg-white dark:hover:bg-gray-900 transition-colors shadow-sm"
                        title="Garten bearbeiten"
                        onclick="event.stopPropagation()"
                    >
                        <svg
                            class="w-4 h-4 text-gray-600 dark:text-gray-400 hover:text-primary transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                </div>
            @endcan

            <a
                href="{{ route('gardens.show', $garden) }}"
                class="block cursor-pointer"
            >
                <!-- Garden Header -->
                <div
                    class="aspect-video bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/20 dark:to-green-800/20 flex items-center justify-center relative">
                    @if($garden->hasCoordinates())
                        <div class="absolute top-3 right-3">
                            <div class="bg-white/90 dark:bg-gray-900/90 rounded-full p-1.5">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                        </div>
                    @endif

                    <div class="text-center">
                        <svg
                            class="h-12 w-12 text-green-600 dark:text-green-400 mx-auto group-hover:text-green-700 dark:group-hover:text-green-300 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
                        </svg>
                    </div>

                    <!-- Status Indicator -->
                    <div class="absolute top-3 left-3">
                        <div class="flex items-center gap-1">
                            <div
                                class="w-2.5 h-2.5 rounded-full {{ $garden->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                            <span
                                class="text-xs font-medium text-gray-700 dark:text-gray-300 bg-white/90 dark:bg-gray-900/90 px-2 py-1 rounded">
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
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300">
                            {{ $garden->user->name ?? 'Unbekannt' }}
                        </span>
                        @endif
                    </div>

                    <!-- Garden Stats -->
                    <div class="flex items-center gap-4 text-sm text-muted-foreground">
                        @if($garden->size_sqm)
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                                <span>{{ $garden->formatted_size }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>{{ $garden->areas->count() }} {{ $garden->areas->count() === 1 ? 'Bereich' : 'Bereiche' }}</span>
                        </div>

                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                            </svg>
                            <span>{{ $garden->plant_count }} Pflanzen</span>
                        </div>
                    </div>

                    <!-- Location -->
                    @if($garden->full_location !== 'Standort nicht angegeben')
                        <div class="flex items-center gap-1 text-sm text-muted-foreground">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="truncate">{{ $garden->full_location }}</span>
                        </div>
                    @endif

                    <!-- Age -->
                    @if($garden->age_in_years !== null)
                        <div class="flex items-center gap-1 text-sm text-muted-foreground">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $garden->age_display }}</span>
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