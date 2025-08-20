<!-- Established Date and Status -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

    <!-- Active Status -->
    <div class="flex items-center pt-7">
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
</div>