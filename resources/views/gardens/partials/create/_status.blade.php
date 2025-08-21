<!-- Active Status -->
<div class="flex items-center">
    <input 
        type="checkbox" 
        id="is_active"
        name="is_active" 
        value="1"
        {{ old('is_active', true) ? 'checked' : '' }}
        class="rounded border-border text-primary focus:ring-primary focus:ring-offset-0"
    >
    <label for="is_active" class="ml-2 text-sm text-foreground">
        Garten ist aktiv
    </label>
    @error('is_active')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>