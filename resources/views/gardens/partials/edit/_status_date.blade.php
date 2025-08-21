<!-- Established Date and Status -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Established Date -->
    <div>
        <x-forms.input 
            name="established_at"
            type="date"
            label="Gründungsdatum"
            value="{{ $garden->established_at?->format('Y-m-d') }}"
            max="{{ date('Y-m-d') }}"
        />
    </div>

    <!-- Active Status -->
    <div class="flex items-center pt-7">
        <x-forms.checkbox 
            name="is_active"
            label="Garten ist aktiv"
            :checked="$garden->is_active"
        />
    </div>
</div>