@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.page
    :title="$garden->name"
    :subtitle="$garden->type->getLabel()"
>
    <x-slot:actions>
        <div class="flex items-center gap-3">
            @can('update', $garden)
                <a
                    href="{{ route('areas.create', ['garden_id' => $garden->id]) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Bereich hinzufügen
                </a>
                
                <a
                    href="{{ route('gardens.edit', $garden) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Garten bearbeiten
                </a>
            @endcan

            @can('delete', $garden)
                <form method="POST" action="{{ route('gardens.destroy', $garden) }}" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        onclick="return confirm('Möchtest du den Garten \"{{ $garden->name }}\" wirklich archivieren? Er kann später wiederhergestellt werden.')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 0l4-4m-4 4v11"></path>
                        </svg>
                        Garten archivieren
                    </button>
                </form>
            @endcan
            
            <a
                href="{{ route('gardens.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Zurück zur Übersicht
            </a>
        </div>
    </x-slot:actions>

    <div class="space-y-8">
        <!-- Garden Header -->
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-foreground">{{ $garden->name }}</h1>
                    <p class="text-xl text-muted-foreground mt-1">{{ $garden->type->getLabel() }}</p>
                </div>
                
                <!-- Garden Type Badge -->
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary">
                    {{ $garden->type->getLabel() }}
                </span>
            </div>

            <!-- Description -->
            @if($garden->description)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-foreground mb-2">Beschreibung</h3>
                    <div class="prose prose-sm max-w-none text-muted-foreground">
                        {!! nl2br(e($garden->description)) !!}
                    </div>
                </div>
            @endif

            <!-- Status -->
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $garden->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                    <span class="text-sm font-medium text-foreground">
                        {{ $garden->is_active ? 'Aktiv' : 'Inaktiv' }}
                    </span>
                </span>
            </div>
        </div>

        <!-- Garden Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Garden Map/Image Placeholder -->
            <div class="lg:col-span-1">
                <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm">
                    <div class="aspect-square bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/20 dark:to-green-800/20 flex items-center justify-center">
                        @if($garden->hasCoordinates())
                            <div class="text-center">
                                <svg class="h-16 w-16 text-green-600 dark:text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-sm text-green-700 dark:text-green-300 font-medium">GPS-Koordinaten verfügbar</p>
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                    {{ number_format($garden->getLatitude(), 4) }}, {{ number_format($garden->getLongitude(), 4) }}
                                </p>
                            </div>
                        @else
                            <div class="text-center">
                                <svg class="h-16 w-16 text-green-600 dark:text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
                                </svg>
                                <p class="text-sm text-green-700 dark:text-green-300 font-medium">{{ $garden->type->getLabel() }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Garden Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Gartendetails</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Typ</dt>
                            <dd class="text-sm text-foreground mt-1">{{ $garden->type->getLabel() }}</dd>
                        </div>
                        
                        @if($garden->size_sqm)
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Größe</dt>
                                <dd class="text-sm text-foreground mt-1">{{ $garden->formatted_size }}</dd>
                            </div>
                        @endif
                        
                        @if($garden->location)
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Standort</dt>
                                <dd class="text-sm text-foreground mt-1">{{ $garden->location }}</dd>
                            </div>
                        @endif
                        
                        @if($garden->full_location !== 'Standort nicht angegeben')
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Adresse</dt>
                                <dd class="text-sm text-foreground mt-1">{{ $garden->full_location }}</dd>
                            </div>
                        @endif
                        
                        @if($garden->established_at)
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Angelegt am</dt>
                                <dd class="text-sm text-foreground mt-1">
                                    {{ $garden->established_at->format('d.m.Y') }}
                                    @if($garden->age_in_years)
                                        ({{ $garden->age_in_years }} {{ $garden->age_in_years === 1 ? 'Jahr' : 'Jahre' }} alt)
                                    @endif
                                </dd>
                            </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd class="text-sm text-foreground mt-1 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $garden->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                {{ $garden->is_active ? 'Aktiv' : 'Inaktiv' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Erstellt am</dt>
                            <dd class="text-sm text-foreground mt-1">{{ $garden->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Plants in Garden -->
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Pflanzen in diesem Garten</h3>
                    @if($garden->plants->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($garden->plants as $plant)
                                <div class="flex items-center p-3 bg-secondary/50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-foreground">{{ $plant->name }}</p>
                                        @if($plant->latin_name)
                                            <p class="text-xs text-muted-foreground italic">{{ $plant->latin_name }}</p>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <a 
                                            href="{{ route('plants.show', $plant) }}" 
                                            class="text-primary hover:text-primary/80 text-sm font-medium"
                                        >
                                            Anzeigen →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-muted-foreground mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                            </svg>
                            <p class="text-muted-foreground text-sm">Noch keine Pflanzen in diesem Garten</p>
                            <a 
                                href="{{ route('plants.index') }}" 
                                class="text-primary hover:text-primary/80 text-sm font-medium mt-2 inline-block"
                            >
                                Pflanzen durchstöbern →
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.page>