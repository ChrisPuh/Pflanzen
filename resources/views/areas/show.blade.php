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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Bearbeiten
                </a>
            @endcan

            @can('delete', $area)
                <button
                    onclick="if(confirm('Möchtest du den Bereich {{ json_encode($area->name) }} wirklich löschen? Er kann später wiederhergestellt werden.')) { document.getElementById('delete-area-form').submit(); }"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Löschen
                </button>

                <form id="delete-area-form" method="POST" action="{{ route('areas.destroy', $area) }}" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
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

        <x-ui.header>
            <x-slot:heading>
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-sm text-primary font-medium mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <a href="{{ route('gardens.show', $area->garden) }}" class="hover:underline">
                                {{ $area->garden->name }}
                            </a>
                        </p>
                        <p class="text-xl text-muted-foreground mt-1">{{ $area->type->getLabel() }}</p>
                    </div>

                    <!-- Area Type Badge with Color -->
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary"
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
            </x-slot:heading>
            <x-ui.status-badge :status="$area->is_active ? 'active' : 'inactive'" />

        </x-ui.header>
        <!-- Area Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Area Visualization -->
            <div class="lg:col-span-1">
                <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm">
                    <div
                        class="aspect-square bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center"
                        @if($area->color) style="background: linear-gradient(135deg, {{ $area->color }}20, {{ $area->color }}40)" @endif>
                        @if($area->hasCoordinates())
                            <div class="text-center">
                                <svg class="h-16 w-16 text-primary mx-auto mb-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <p class="text-sm text-primary font-medium">Koordinaten verfügbar</p>
                                <p class="text-xs text-primary/80 mt-1">
                                    X{{ $area->getXCoordinate() }}, Y{{ $area->getYCoordinate() }}
                                </p>
                            </div>
                        @else
                            <div class="text-center">
                                <svg class="h-16 w-16 text-primary mx-auto mb-2" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
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
                                <a href="{{ route('gardens.show', $area->garden) }}"
                                   class="text-primary hover:underline">
                                    {{ $area->garden->name }}
                                </a>
                            </dd>
                        </div>

                        @if($area->hasCoordinates())
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Koordinaten</dt>
                                <dd class="text-sm text-foreground mt-1">X{{ $area->getXCoordinate() }},
                                    Y{{ $area->getYCoordinate() }}</dd>
                            </div>
                        @endif

                        @if($area->color)
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Farbe</dt>
                                <dd class="text-sm text-foreground mt-1 flex items-center gap-2">
                                    <div class="w-4 h-4 rounded border border-border"
                                         style="background-color: {{ $area->color }}"></div>
                                    {{ strtoupper($area->color) }}
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Status</dt>
                            <dd class="text-sm text-foreground mt-1 flex items-center gap-2">
                                <div
                                    class="w-2 h-2 rounded-full {{ $area->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></div>
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
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-foreground">Pflanzen in diesem Bereich</h3>
                        @can('update', $area)
                            <button
                                onclick="toggleAddPlantForm()"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"></path>
                                </svg>
                                Pflanzen hinzufügen
                            </button>
                        @endcan
                    </div>

                    <!-- Add Plant Form -->
                    @can('update', $area)
                        <div id="add-plant-form"
                             class="hidden mb-6 p-4 bg-secondary/30 rounded-lg border border-border/50">
                            <form method="POST" action="{{ route('areas.plants.store', $area) }}" class="space-y-4">
                                @csrf

                                <div id="plants-container">
                                    <div class="plant-entry space-y-3 p-4 bg-card rounded-lg border border-border/50">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label for="plants[0][plant_id]"
                                                       class="block text-sm font-medium text-foreground mb-1">
                                                    Pflanze
                                                </label>
                                                <select
                                                    name="plants[0][plant_id]"
                                                    id="plants[0][plant_id]"
                                                    class="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                                                    required
                                                >
                                                    <option value="">Pflanze auswählen...</option>
                                                    @foreach($availablePlants as $plant)
                                                        <option value="{{ $plant->id }}">
                                                            {{ $plant->name }}@if($plant->latin_name)
                                                                ({{ $plant->latin_name }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label for="plants[0][quantity]"
                                                       class="block text-sm font-medium text-foreground mb-1">
                                                    Anzahl
                                                </label>
                                                <input
                                                    type="number"
                                                    name="plants[0][quantity]"
                                                    id="plants[0][quantity]"
                                                    min="1"
                                                    max="9999"
                                                    value="1"
                                                    class="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                                                    required
                                                >
                                            </div>

                                            <div>
                                                <label for="plants[0][notes]"
                                                       class="block text-sm font-medium text-foreground mb-1">
                                                    Notizen (optional)
                                                </label>
                                                <input
                                                    type="text"
                                                    name="plants[0][notes]"
                                                    id="plants[0][notes]"
                                                    maxlength="500"
                                                    placeholder="z.B. Position, Besonderheiten..."
                                                    class="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        onclick="addPlantEntry()"
                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-muted-foreground bg-secondary rounded-lg hover:bg-secondary/80 transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Weitere Pflanze hinzufügen
                                    </button>
                                </div>

                                <div class="flex items-center gap-3 pt-2 border-t border-border/50">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors"
                                    >
                                        Pflanzen hinzufügen
                                    </button>
                                    <button
                                        type="button"
                                        onclick="toggleAddPlantForm()"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-muted-foreground bg-secondary rounded-lg hover:bg-secondary/80 transition-colors"
                                    >
                                        Abbrechen
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endcan

                    @if($area->plants->count() > 0)
                        <div class="space-y-3">
                            @foreach($area->plants as $plant)
                                <div class="flex items-center justify-between p-4 bg-secondary/50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium text-foreground">{{ $plant->name }}</p>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary/10 text-primary">
                                                    {{ $plant->pivot->quantity }}x
                                                </span>
                                            </div>
                                            @if($plant->latin_name)
                                                <p class="text-xs text-muted-foreground italic">{{ $plant->latin_name }}</p>
                                            @endif
                                            @if($plant->pivot->notes)
                                                <p class="text-xs text-muted-foreground mt-1">{{ $plant->pivot->notes }}</p>
                                            @endif
                                            @if($plant->pivot->planted_at)
                                                <p class="text-xs text-muted-foreground">
                                                    Hinzugefügt: {{ \Carbon\Carbon::parse($plant->pivot->planted_at)->format('d.m.Y') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a
                                            href="{{ route('plants.show', $plant) }}"
                                            class="text-primary hover:text-primary/80 text-sm font-medium"
                                        >
                                            Anzeigen
                                        </a>
                                        @can('update', $area)
                                            <button
                                                onclick="if(confirm('Möchtest du {{ json_encode($plant->name) }} ({{ $plant->pivot->quantity }}x) wirklich aus diesem Bereich entfernen?')) { document.getElementById('remove-plant-{{ $plant->id }}').submit(); }"
                                                class="text-red-600 hover:text-red-700 text-sm font-medium"
                                            >
                                                Entfernen
                                            </button>
                                            <form id="remove-plant-{{ $plant->id }}" method="POST"
                                                  action="{{ route('areas.plants.destroy', [$area, $plant]) }}"
                                                  class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-muted-foreground mx-auto mb-3" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                            </svg>
                            <p class="text-muted-foreground text-sm">Noch keine Pflanzen in diesem Bereich</p>
                            @can('update', $area)
                                <button
                                    onclick="toggleAddPlantForm()"
                                    class="text-primary hover:text-primary/80 text-sm font-medium mt-2 inline-block"
                                >
                                    Pflanzen hinzufügen →
                                </button>
                            @else
                                <a
                                    href="{{ route('plants.index') }}"
                                    class="text-primary hover:text-primary/80 text-sm font-medium mt-2 inline-block"
                                >
                                    Pflanzen durchstöbern →
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        let plantEntryCount = 1;

        function toggleAddPlantForm() {
            const form = document.getElementById('add-plant-form');
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

        function addPlantEntry() {
            const container = document.getElementById('plants-container');
            const newEntry = createPlantEntry(plantEntryCount);
            container.appendChild(newEntry);
            plantEntryCount++;
        }

        function createPlantEntry(index) {
            const entry = document.createElement('div');
            entry.className = 'plant-entry space-y-3 p-4 bg-card rounded-lg border border-border/50 relative';

            entry.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="plants[${index}][plant_id]" class="block text-sm font-medium text-foreground mb-1">
                            Pflanze
                        </label>
                        <select
                            name="plants[${index}][plant_id]"
                            id="plants[${index}][plant_id]"
                            class="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                            required
                        >
                            <option value="">Pflanze auswählen...</option>
                            @foreach($availablePlants as $plant)
            <option value="{{ $plant->id }}">
                                    {{ $plant->name }}@if($plant->latin_name) ({{ $plant->latin_name }})@endif
            </option>
@endforeach
            </select>
        </div>

        <div>
            <label for="plants[${index}][quantity]" class="block text-sm font-medium text-foreground mb-1">
                            Anzahl
                        </label>
                        <input
                            type="number"
                            name="plants[${index}][quantity]"
                            id="plants[${index}][quantity]"
                            min="1"
                            max="9999"
                            value="1"
                            class="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                            required
                        >
                    </div>

                    <div>
                        <label for="plants[${index}][notes]" class="block text-sm font-medium text-foreground mb-1">
                            Notizen (optional)
                        </label>
                        <input
                            type="text"
                            name="plants[${index}][notes]"
                            id="plants[${index}][notes]"
                            maxlength="500"
                            placeholder="z.B. Position, Besonderheiten..."
                            class="w-full px-3 py-2 border border-border rounded-lg bg-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                        >
                    </div>
                </div>
                ${index > 0 ? `
                    <button
                        type="button"
                        onclick="removePlantEntry(this)"
                        class="absolute top-2 right-2 text-red-600 hover:text-red-700 p-1"
                        title="Eintrag entfernen"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                ` : ''}
            `;

            return entry;
        }

        function removePlantEntry(button) {
            button.closest('.plant-entry').remove();
        }
    </script>
</x-layouts.page>
