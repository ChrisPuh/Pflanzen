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