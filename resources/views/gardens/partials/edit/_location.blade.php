<!-- Location Information -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Location -->
    <div>
        <x-forms.input 
            name="location"
            type="text"
            label="Standort"
            placeholder="z.B. Hinterhof, Balkon"
            value="{{ $garden->location }}"
        />
    </div>

    <!-- City -->
    <div>
        <x-forms.input 
            name="city"
            type="text"
            label="Stadt"
            placeholder="z.B. Berlin"
            value="{{ $garden->city }}"
        />
    </div>
</div>

<!-- Postal Code -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-forms.input 
            name="postal_code"
            type="text"
            label="Postleitzahl"
            placeholder="z.B. 10115"
            value="{{ $garden->postal_code }}"
        />
    </div>

    <!-- Size -->
    <div>
        <x-forms.input 
            name="size_sqm"
            type="number"
            label="Größe (m²)"
            placeholder="z.B. 25.5"
            value="{{ $garden->size_sqm }}"
            step="0.01"
            min="0"
        />
    </div>
</div>