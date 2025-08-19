@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.create-page 
    title="Garten bearbeiten" 
    subtitle="Bearbeite die Details deines Gartens"
    :form-action="route('gardens.update', $garden)"
    :cancel-route="route('gardens.show', $garden)"
    method="PUT"
    submit-text="Garten aktualisieren"
>
    <x-slot:actions>
        <a
            href="{{ route('gardens.show', $garden) }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Zurück zum Garten
        </a>
    </x-slot:actions>
            
            <!-- Garden Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-foreground mb-2">
                    Gartenname <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name"
                    name="name" 
                    value="{{ old('name', $garden->name) }}"
                    required
                    placeholder="z.B. Mein Gemüsegarten"
                    class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('name') border-red-500 @enderror"
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Garden Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-foreground mb-2">
                    Gartentyp <span class="text-red-500">*</span>
                </label>
                <select 
                    id="type" 
                    name="type" 
                    required
                    class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('type') border-red-500 @enderror"
                >
                    <option value="">Gartentyp auswählen</option>
                    @foreach($gardenTypes as $gardenType)
                        <option 
                            value="{{ $gardenType->value }}" 
                            {{ old('type', $garden->type->value) === $gardenType->value ? 'selected' : '' }}
                        >
                            {{ $gardenType->getLabel() }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-foreground mb-2">
                    Beschreibung
                </label>
                <textarea 
                    id="description"
                    name="description" 
                    rows="3"
                    placeholder="Beschreibe deinen Garten..."
                    class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('description') border-red-500 @enderror"
                >{{ old('description', $garden->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Size -->
            <div>
                <label for="size_sqm" class="block text-sm font-medium text-foreground mb-2">
                    Größe (m²)
                </label>
                <input 
                    type="number" 
                    id="size_sqm"
                    name="size_sqm" 
                    value="{{ old('size_sqm', $garden->size_sqm) }}"
                    step="0.01"
                    min="0"
                    placeholder="z.B. 25.5"
                    class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('size_sqm') border-red-500 @enderror"
                >
                @error('size_sqm')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-foreground mb-2">
                        Standort
                    </label>
                    <input 
                        type="text" 
                        id="location"
                        name="location" 
                        value="{{ old('location', $garden->location) }}"
                        placeholder="z.B. Hinterhof, Balkon"
                        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('location') border-red-500 @enderror"
                    >
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-foreground mb-2">
                        Stadt
                    </label>
                    <input 
                        type="text" 
                        id="city"
                        name="city" 
                        value="{{ old('city', $garden->city) }}"
                        placeholder="z.B. Berlin"
                        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('city') border-red-500 @enderror"
                    >
                    @error('city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Postal Code -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-foreground mb-2">
                        Postleitzahl
                    </label>
                    <input 
                        type="text" 
                        id="postal_code"
                        name="postal_code" 
                        value="{{ old('postal_code', $garden->postal_code) }}"
                        placeholder="z.B. 10115"
                        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('postal_code') border-red-500 @enderror"
                    >
                    @error('postal_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Established Date -->
                <div>
                    <label for="established_at" class="block text-sm font-medium text-foreground mb-2">
                        Gründungsdatum
                    </label>
                    <input 
                        type="date" 
                        id="established_at"
                        name="established_at" 
                        value="{{ old('established_at', $garden->established_at?->format('Y-m-d')) }}"
                        max="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('established_at') border-red-500 @enderror"
                    >
                    @error('established_at')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Coordinates -->
            <div class="border border-border rounded-lg p-4 bg-muted/20">
                <h3 class="text-sm font-medium text-foreground mb-3">
                    GPS-Koordinaten (optional)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Latitude -->
                    <div>
                        <label for="coordinates_latitude" class="block text-sm font-medium text-foreground mb-2">
                            Breitengrad
                        </label>
                        <input 
                            type="number" 
                            id="coordinates_latitude"
                            name="coordinates[latitude]" 
                            value="{{ old('coordinates.latitude', $garden->coordinates['latitude'] ?? '') }}"
                            step="0.0001"
                            min="-90"
                            max="90"
                            placeholder="z.B. 52.5200"
                            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('coordinates.latitude') border-red-500 @enderror"
                        >
                        @error('coordinates.latitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Longitude -->
                    <div>
                        <label for="coordinates_longitude" class="block text-sm font-medium text-foreground mb-2">
                            Längengrad
                        </label>
                        <input 
                            type="number" 
                            id="coordinates_longitude"
                            name="coordinates[longitude]" 
                            value="{{ old('coordinates.longitude', $garden->coordinates['longitude'] ?? '') }}"
                            step="0.0001"
                            min="-180"
                            max="180"
                            placeholder="z.B. 13.4050"
                            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('coordinates.longitude') border-red-500 @enderror"
                        >
                        @error('coordinates.longitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-muted-foreground mt-2">
                    Tipp: Du kannst Koordinaten von Google Maps oder anderen Karten-Apps kopieren.
                </p>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    id="is_active"
                    name="is_active" 
                    value="1"
                    {{ old('is_active', $garden->is_active) ? 'checked' : '' }}
                    class="rounded border-border text-primary focus:ring-primary focus:ring-offset-0"
                >
                <label for="is_active" class="ml-2 text-sm text-foreground">
                    Garten ist aktiv
                </label>
                @error('is_active')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

</x-layouts.create-page>