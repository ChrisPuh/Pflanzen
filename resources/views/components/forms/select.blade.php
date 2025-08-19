@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'options' => [],
    'selected' => null
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
    <select 
        name="{{ $name }}" 
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-2 border border-border rounded-lg bg-background text-foreground focus:ring-2 focus:ring-primary focus:border-transparent']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $option)
            @if(is_array($option))
                <option value="{{ $option['value'] ?? $option['id'] }}" 
                        {{ $selected == ($option['value'] ?? $option['id']) ? 'selected' : '' }}>
                    {{ $option['label'] ?? $option['name'] ?? $option['text'] }}
                </option>
            @else
                <option value="{{ $option }}" {{ $selected == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endif
        @endforeach
    </select>
</div>