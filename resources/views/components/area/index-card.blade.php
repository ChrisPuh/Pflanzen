@props([
    'area',
    'isAdmin' => false
])

<x-ui.index-card :href="route('areas.show', $area)">
    <!-- Actions -->
    <x-slot:actions>
        <div class="flex gap-2">
            @can('update', $area)
                <a
                    href="{{ route('areas.edit', $area) }}"
                    onclick="event.stopPropagation();"
                    class="inline-flex items-center p-2 text-white bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors shadow-sm"
                    title="Bearbeiten"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
            @endcan

            @can('delete', $area)
                <button
                    onclick="event.stopPropagation(); if(confirm('Möchtest du den Bereich {{ json_encode($area->name) }} wirklich löschen? Er kann später wiederhergestellt werden.')) { document.getElementById('delete-area-form-{{ $area->id }}').submit(); }"
                    class="inline-flex items-center p-2 text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors shadow-sm"
                    title="Löschen"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>

                <form id="delete-area-form-{{ $area->id }}" method="POST" action="{{ route('areas.destroy', $area) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endcan
        </div>
    </x-slot:actions>

    <!-- Header -->
    <x-slot:header>
        <div 
            class="aspect-video bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center relative"
            style="background: linear-gradient(135deg, {{ $area->getDisplayColor() }}20, {{ $area->getDisplayColor() }}40)"
        >
            <!-- Status Indicator -->
            <div class="absolute top-3 left-3">
                <div class="flex items-center gap-1">
                    <div class="w-2.5 h-2.5 rounded-full {{ $area->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 bg-white/90 dark:bg-gray-900/90 px-2 py-1 rounded">
                        {{ $area->is_active ? 'Aktiv' : 'Inaktiv' }}
                    </span>
                </div>
            </div>

            <!-- Position Indicator -->
            @if($area->hasCoordinates())
                <div class="absolute bottom-3 right-3">
                    <div class="bg-white/90 dark:bg-gray-900/90 rounded-full p-1.5">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            @endif

            <!-- Main Icon -->
            <div class="text-center">
                <svg class="h-12 w-12 text-primary/60 group-hover:text-primary transition-colors mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>
    </x-slot:header>

    <!-- Title -->
    <x-slot:title>
        <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">
            {{ $area->name }}
        </h3>
        <p class="text-sm text-muted-foreground">
            {{ $area->type->getLabel() }}
            <span class="mx-1">•</span>
            <span>{{ $area->type->category() }}</span>
        </p>
    </x-slot:title>

    <!-- Admin Badge -->
    <x-slot:badge>
        @if($isAdmin && $area->garden->user_id !== auth()->id())
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300">
                {{ $area->garden->user->name ?? 'Unbekannt' }}
            </span>
        @endif
    </x-slot:badge>

    <!-- Metadata -->
    <x-slot:metadata>
        <!-- Garden Link -->
        <div class="flex items-center gap-1 text-sm text-muted-foreground">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="truncate hover:text-primary transition-colors">{{ $area->garden->name }}</span>
        </div>

        @if($area->hasCoordinates())
            <div class="flex items-center gap-1 text-sm text-muted-foreground">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span>Position: X{{ $area->getXCoordinate() }}, Y{{ $area->getYCoordinate() }}</span>
            </div>
        @endif
    </x-slot:metadata>

    <!-- Stats -->
    <x-slot:stats>
        <div class="flex items-center gap-4 text-sm text-muted-foreground">
            @if($area->size_sqm)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                    <span>{{ $area->formatted_size }}</span>
                </div>
            @endif

            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                </svg>
                <span>{{ $area->plants->count() }} Pflanzen</span>
            </div>
        </div>
    </x-slot:stats>

    <!-- Description -->
    @if($area->description)
        <p class="text-sm text-muted-foreground line-clamp-2">
            {{ $area->description }}
        </p>
    @endif

    <!-- Action Link -->
    <x-slot:action>
        <span class="text-sm font-medium text-primary group-hover:underline">
            Bereich anzeigen →
        </span>
    </x-slot:action>
</x-ui.index-card>