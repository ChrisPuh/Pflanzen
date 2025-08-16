@props(['label', 'name', 'value' => null])

<label for="{{ $name }}"
    {{ $attributes->merge(['class' => 'flex items-center text-sm text-muted']) }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}"
        {{ $attributes }} class="h-4 w-4 text-primary focus:ring-primary border-default rounded mr-1">
    {{ $label }}
</label>

@error($name)
    <span class="text-danger">{{ $message }}</span>
@enderror
