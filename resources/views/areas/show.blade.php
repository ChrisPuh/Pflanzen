@php
    use App\Enums\Area\AreaTypeEnum;
@endphp

<x-layouts.page
    :title="$area->name"
    :subtitle="$area->type->getLabel()"
>
    <x-slot:actions>
        <div class="flex items-center gap-3">
            @can('update', $area)
                <a
                    href="{{ route('areas.edit', $area) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Bearbeiten
                </a>
            @endcan
            <a
                href="{{ route('areas.index') }}"
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
        <!-- Area Header -->
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-foreground">{{ $area->name }}</h1>
                    <p class="text-xl text-muted-foreground mt-1">{{ $area->type->getLabel() }}</p>
                </div>
                
                <!-- Area Type Badge with Color -->
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary"
                      @if($area->color) style="background-color: {{ $area->color }}20; color: {{ $area->color }}" @endif>
                    {{ $area->type->getLabel() }}
                </span>
            </div>

            <!-- Description -->
            @if($area->description)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-foreground mb-2">Beschreibung</h3>
                    <div class="prose prose-sm max-w-none text-muted-foreground">
                        {!! nl2br(e($area->description)) !!}
                    </div>
                </div>
            @endif

            <!-- Status -->
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full {{ $area->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                    <span class="text-sm font-medium text-foreground">
                        {{ $area->is_active ? 'Aktiv' : 'Inaktiv' }}
                    </span>
                </span>
            </div>
        </div>

        <!-- Area Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Area Visualization -->
            <div class="lg:col-span-1">
                <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm">
                    <div class="aspect-square bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center"
                         @if($area->color) style="background: linear-gradient(135deg, {{ $area->color }}20, {{ $area->color }}40)" @endif>
                        @if($area->hasCoordinates())
                            <div class="text-center">
                                <svg class="h-16 w-16 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-sm text-primary font-medium">Koordinaten verfügbar</p>
                                <p class="text-xs text-primary/80 mt-1">
                                    X{{ $area->getXCoordinate() }}, Y{{ $area->getYCoordinate() }}
                                </p>
                            </div>
                        @else
                            <div class="text-center">
                                <svg class="h-16 w-16 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-sm text-primary font-medium">{{ $area->type->getLabel() }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Area Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Bereichsdetails</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Typ</dt>
                            <dd class="text-sm text-foreground mt-1">{{ $area->type->getLabel() }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Kategorie</dt>
                            <dd class="text-sm text-foreground mt-1">{{ $area->type->category() }}</dd>
                        </div>
                        
                        @if($area->size_sqm)
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Größe</dt>
                                <dd class="text-sm text-foreground mt-1">{{ $area->formatted_size }}</dd>
                            </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Garten</dt>
                            <dd class="text-sm text-foreground mt-1">
                                <a href="{{ route('gardens.show', $area->garden) }}" class="text-primary hover:underline">
                                    {{ $area->garden->name }}
                                </a>
                            </dd>
                        </div>
                        
                        @if($area->hasCoordinates())
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Koordinaten</dt>
                                <dd class="text-sm text-foreground mt-1">X{{ $area->getXCoordinate() }}, Y{{ $area->getYCoordinate() }}</dd>
                            </div>
                        @endif
                        
                        @if($area->color)
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Farbe</dt>
                                <dd class="text-sm text-foreground mt-1 flex items-center gap-2">
                                    <div class="w-4 h-4 rounded border border-border" style="background-color: {{ $area->color }}"></div>
                                    {{ strtoupper($area->color) }}
                                </dd>
                            </div>
                        @endif
                        
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd class="text-sm text-foreground mt-1 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $area->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                                {{ $area->is_active ? 'Aktiv' : 'Inaktiv' }}
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Erstellt am</dt>
                            <dd class="text-sm text-foreground mt-1">{{ $area->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Plants in Area -->
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-foreground mb-4">Pflanzen in diesem Bereich</h3>
                    @if($area->plants->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($area->plants as $plant)
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
                            <p class="text-muted-foreground text-sm">Noch keine Pflanzen in diesem Bereich</p>
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