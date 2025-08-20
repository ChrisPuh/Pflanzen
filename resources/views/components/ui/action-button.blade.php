@props([
    'variant' => 'primary', // primary, secondary, danger
    'size' => 'md', // sm, md
    'icon' => null,
    'href' => null,
])

@php
$variantClasses = match($variant) {
    'primary' => 'text-primary-foreground bg-primary hover:bg-primary/90',
    'secondary' => 'text-foreground bg-secondary hover:bg-secondary/80',
    'danger' => 'text-white bg-red-600 hover:bg-red-700',
    default => 'text-primary-foreground bg-primary hover:bg-primary/90',
};

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2 text-sm',
    default => 'px-4 py-2 text-sm',
};

$iconSizeClass = match($size) {
    'sm' => 'w-3 h-3',
    'md' => 'w-4 h-4',
    default => 'w-4 h-4',
};

$tag = $href ? 'a' : 'button';
$attributes = $href ? $attributes->merge(['href' => $href]) : $attributes;
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-lg focus:ring-2 focus:ring-offset-2 transition-colors {$sizeClasses} {$variantClasses}"]) }}>
    @if($icon)
        <svg class="{{ $iconSizeClass }} {{ $slot->isEmpty() ? '' : 'mr-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif
    {{ $slot }}
</{{ $tag }}>