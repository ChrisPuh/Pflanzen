@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.page 
    title="Archivierte Gärten" 
    subtitle="Stelle deine archivierten Gärten wieder her"
>
    <x-slot:actions>
        <a
            href="{{ route('gardens.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Zurück zu aktiven Gärten
        </a>
    </x-slot:actions>

    <div class="space-y-6">
        <!-- Info Section -->
        <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-orange-800 dark:text-orange-200">
                        Archivierte Gärten
                    </h3>
                    <div class="mt-2 text-sm text-orange-700 dark:text-orange-300">
                        <p>Diese Gärten wurden archiviert und sind derzeit nicht aktiv. Du kannst sie jederzeit wiederherstellen.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Info -->
        <div class="flex items-center justify-between">
            <p class="text-muted-foreground">
                {{ $gardens->count() }} {{ $gardens->count() === 1 ? 'archivierter Garten' : 'archivierte Gärten' }} gefunden
                @if($isAdmin)
                    <span class="text-orange-600 dark:text-orange-400 font-medium">(Admin-Ansicht)</span>
                @endif
            </p>
        </div>

        <!-- Archived Gardens -->
        @if($gardens->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($gardens as $garden)
                    <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm relative opacity-75">
                        <!-- Archived Badge -->
                        <div class="absolute top-2 left-2 z-10">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 0l4-4m-4 4v11"></path>
                                </svg>
                                Archiviert
                            </span>
                        </div>
                        
                        <!-- Restore Button -->
                        @can('restore', $garden)
                            <div class="absolute top-2 right-2 z-10">
                                <form method="POST" action="{{ route('gardens.restore', $garden->id) }}" class="inline-block">
                                    @csrf
                                    <button
                                        type="submit"
                                        onclick="return confirm('Möchtest du den Garten \"{{ $garden->name }}\" wiederherstellen?')"
                                        class="inline-flex items-center justify-center w-9 h-9 bg-green-600 hover:bg-green-700 rounded-full transition-colors shadow-sm"
                                        title="Garten wiederherstellen"
                                    >
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endcan
                        
                        <!-- Garden Content -->
                        <div class="cursor-default">
                            <!-- Garden Header -->
                            <div class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800/20 dark:to-gray-700/20 flex items-center justify-center relative">
                                @if($garden->hasCoordinates())
                                    <div class="absolute top-3 right-12">
                                        <div class="bg-white/90 dark:bg-gray-900/90 rounded-full p-1.5">
                                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="text-center">
                                    <svg class="h-12 w-12 text-gray-500 dark:text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
                                    </svg>
                                </div>

                                <!-- Status Indicator -->
                                <div class="absolute bottom-3 left-3">
                                    <div class="flex items-center gap-1">
                                        <div class="w-2.5 h-2.5 rounded-full bg-gray-400"></div>
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400 bg-white/90 dark:bg-gray-900/90 px-2 py-1 rounded">
                                            Inaktiv
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 space-y-3">
                                <!-- Garden Info -->
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-foreground">
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
                                        <span>{{ \Illuminate\Support\Facades\DB::table('areas')->join('area_plant', 'areas.id', '=', 'area_plant.area_id')->where('areas.garden_id', $garden->id)->sum('area_plant.quantity') ?: 0 }} Pflanzen</span>
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

                                <!-- Archived Date -->
                                <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Archiviert am {{ $garden->deleted_at->format('d.m.Y') }}</span>
                                </div>

                                <!-- Restore Action -->
                                @can('restore', $garden)
                                    <div class="pt-2 border-t border-border">
                                        <form method="POST" action="{{ route('gardens.restore', $garden->id) }}" class="w-full">
                                            @csrf
                                            <button
                                                type="submit"
                                                onclick="return confirm('Möchtest du den Garten \"{{ $garden->name }}\" wiederherstellen?')"
                                                class="w-full inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                Garten wiederherstellen
                                            </button>
                                        </form>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-muted-foreground/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 8l4 4 4-4m0 0l4-4m-4 4v11"></path>
                </svg>
                <h3 class="text-lg font-medium text-foreground mt-4">Keine archivierten Gärten</h3>
                <p class="text-muted-foreground mt-2">
                    Du hast derzeit keine archivierten Gärten.
                </p>
                <a 
                    href="{{ route('gardens.index') }}" 
                    class="inline-flex items-center px-4 py-2 mt-4 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Zu aktiven Gärten
                </a>
            </div>
        @endif
    </div>
</x-layouts.page>