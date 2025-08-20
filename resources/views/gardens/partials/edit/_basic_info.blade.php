<!-- Garden Basic Information -->
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