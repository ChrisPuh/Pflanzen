@props([
    'label',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'error' => false,
    'class' => '',
    'labelClass' => '',
])

@if ($label)
    <label for="{{ $name }}"
        {{ $attributes->merge(['class' => 'block ml-1 text-sm font-medium text-muted mb-1 ' . $labelClass]) }}>
        {{ $label }}
    </label>
@endif

<input type="{{ $type }}" id="{{ $name }}" placeholder="{{ $placeholder }}" name="{{ $name }}"
    {{ $attributes->merge(['class' => 'w-full px-4 py-1.5 rounded-lg text-foreground bg-surface-2 border border-default focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent']) }}>

@error($name)
    <span class="text-danger">{{ $message }}</span>
@enderror
