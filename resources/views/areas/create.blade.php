<x-layouts.create-page 
    title="Neuen Bereich erstellen" 
    subtitle="Füge einen neuen Bereich zu deinem Garten hinzu"
    :form-action="route('areas.store')"
    :cancel-route="route('areas.index')"
    submit-text="Bereich erstellen"
>

                <!-- Area Name -->
                <x-forms.input 
                    label="Name des Bereichs"
                    name="name"
                    type="text"
                    :value="old('name')"
                    placeholder="z.B. Gemüsebeet Nord, Rosengarten, Terrasse..."
                    required
                />

                <!-- Garden Selection -->
                <x-forms.select 
                    label="Garten"
                    name="garden_id"
                    placeholder="Garten auswählen"
                    :selected="$selectedGarden ? $selectedGarden->id : old('garden_id')"
                    :options="$userGardens->mapWithKeys(function($garden) use ($isAdmin) {
                        $label = $garden->name;
                        if ($isAdmin) {
                            $label .= ' (' . $garden->type->getLabel() . ')';
                        }
                        return [$garden->id => $label];
                    })"
                    required
                />

                <!-- Area Type -->
                <x-forms.select 
                    label="Typ des Bereichs"
                    name="type"
                    placeholder="Typ auswählen"
                    :selected="old('type')"
                    :options="collect($areaTypes)->mapWithKeys(function($type) {
                        return [$type['value'] => $type['label']];
                    })"
                    required
                />

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-foreground mb-2">
                        Beschreibung
                    </label>
                    <textarea 
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Beschreibe den Bereich (optional)..."
                        class="w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent resize-vertical"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Size -->
                <x-forms.input 
                    label="Größe (m²)"
                    name="size_sqm"
                    type="number"
                    :value="old('size_sqm')"
                    placeholder="z.B. 25.5"
                    step="0.01"
                    min="0"
                />

                <!-- Coordinates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-forms.input 
                        label="X-Koordinate"
                        name="coordinates_x"
                        type="number"
                        :value="old('coordinates_x')"
                        placeholder="z.B. 12.5"
                        step="0.01"
                    />
                    
                    <x-forms.input 
                        label="Y-Koordinate"
                        name="coordinates_y"
                        type="number"
                        :value="old('coordinates_y')"
                        placeholder="z.B. 8.2"
                        step="0.01"
                    />
                </div>

                <!-- Color -->
                <div>
                    <label for="color" class="block text-sm font-medium text-foreground mb-2">
                        Farbe (für Anzeige)
                    </label>
                    <div class="flex items-center gap-3">
                        <input 
                            type="color"
                            id="color"
                            name="color"
                            value="{{ old('color', '#10b981') }}"
                            class="w-12 h-10 border border-border rounded-lg bg-background cursor-pointer"
                        >
                        <input 
                            type="text"
                            placeholder="#10b981"
                            value="{{ old('color', '#10b981') }}"
                            class="flex-1 px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent"
                            pattern="^#[0-9A-Fa-f]{6}$"
                            id="color-text"
                        >
                    </div>
                    @error('color')
                        <span class="text-danger text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary bg-background border-border rounded focus:ring-primary focus:ring-2"
                    >
                    <label for="is_active" class="text-sm font-medium text-foreground">
                        Bereich ist aktiv
                    </label>
                </div>

    <script>
        // Sync color picker and text input
        const colorPicker = document.getElementById('color');
        const colorText = document.getElementById('color-text');
        
        colorPicker.addEventListener('change', function() {
            colorText.value = this.value;
        });
        
        colorText.addEventListener('input', function() {
            if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                colorPicker.value = this.value;
            }
        });
    </script>
</x-layouts.create-page>