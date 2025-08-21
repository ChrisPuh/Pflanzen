<!-- Size -->
<div>
    <label for="size_sqm" class="block text-sm font-medium text-foreground mb-2">
        Größe (m²)
    </label>
    <input 
        type="number" 
        id="size_sqm"
        name="size_sqm" 
        value="{{ old('size_sqm') }}"
        step="0.01"
        min="0"
        placeholder="z.B. 25.5"
        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('size_sqm') border-red-500 @enderror"
    >
    @error('size_sqm')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Location Information -->
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
            value="{{ old('location') }}"
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
            value="{{ old('city') }}"
            placeholder="z.B. Berlin"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('city') border-red-500 @enderror"
        >
        @error('city')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Postal Code and Established Date -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="postal_code" class="block text-sm font-medium text-foreground mb-2">
            Postleitzahl
        </label>
        <input 
            type="text" 
            id="postal_code"
            name="postal_code" 
            value="{{ old('postal_code') }}"
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
            value="{{ old('established_at') }}"
            max="{{ date('Y-m-d') }}"
            class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:ring-2 focus:ring-primary focus:border-transparent @error('established_at') border-red-500 @enderror"
        >
        @error('established_at')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>