@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => '',
    'rows' => 3,
    'value' => ''
])

@php
    $id = $id ?? $name;
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-foreground mb-2">
            {{ $label }}
        </label>
    @endif
    <textarea 
        name="{{ $name }}" 
        id="{{ $id }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground placeholder-muted-foreground focus:ring-2 focus:ring-primary focus:border-transparent']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>