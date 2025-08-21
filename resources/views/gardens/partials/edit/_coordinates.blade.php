<!-- Coordinates -->
<div class="border border-border rounded-lg p-4 bg-muted/20">
    <h3 class="text-sm font-medium text-foreground mb-3">
        GPS-Koordinaten (optional)
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Latitude -->
        <div>
            <x-forms.input 
                name="coordinates[latitude]"
                type="number"
                label="Breitengrad"
                placeholder="z.B. 52.5200"
                value="{{ $garden->coordinates['latitude'] ?? '' }}"
                step="0.0001"
                min="-90"
                max="90"
            />
        </div>

        <!-- Longitude -->
        <div>
            <x-forms.input 
                name="coordinates[longitude]"
                type="number"
                label="Längengrad"
                placeholder="z.B. 13.4050"
                value="{{ $garden->coordinates['longitude'] ?? '' }}"
                step="0.0001"
                min="-180"
                max="180"
            />
        </div>
    </div>
    <p class="text-xs text-muted-foreground mt-2">
        Tipp: Du kannst Koordinaten von Google Maps oder anderen Karten-Apps kopieren.
    </p>
</div>