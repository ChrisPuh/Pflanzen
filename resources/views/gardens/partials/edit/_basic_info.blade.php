<!-- Garden Basic Information -->
<!-- Garden Name -->
<div>
    <x-forms.input 
        name="name"
        type="text"
        label="Gartenname *"
        placeholder="z.B. Mein Gemüsegarten"
        value="{{ old('name', $garden->name) }}"
        required
    />
</div>

<!-- Garden Type -->
@php
    $gardenTypeOptions = collect($gardenTypes)->mapWithKeys(function($type) {
        return [$type->value => $type->getLabel()];
    })->toArray();
@endphp

<div>
    <x-forms.select 
        name="type"
        label="Gartentyp *"
        placeholder="Gartentyp auswählen"
        :options="$gardenTypeOptions"
        :selected="old('type', $garden->type->value)"
        required
    />
    @error('type')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<!-- Description -->
<div>
    <x-forms.textarea 
        name="description"
        label="Beschreibung"
        placeholder="Beschreibe deinen Garten..."
        rows="3"
        :value="$garden->description"
    />
</div>