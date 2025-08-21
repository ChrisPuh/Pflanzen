@props([
    'status' => 'active', // active, inactive
    'size' => 'sm', // sm, md
])

@php
$statusClasses = match($status) {
    'active' => 'bg-green-500 text-green-50',
    'inactive' => 'bg-gray-400 text-gray-50',
    default => 'bg-gray-400 text-gray-50',
};

$sizeClasses = match($size) {
    'sm' => 'px-2 py-1 text-xs',
    'md' => 'px-3 py-1.5 text-sm',
    default => 'px-2 py-1 text-xs',
};

$dotSizeClass = match($size) {
    'sm' => 'w-2 h-2',
    'md' => 'w-2.5 h-2.5',
    default => 'w-2 h-2',
};

$statusText = match($status) {
    'active' => 'Aktiv',
    'inactive' => 'Inaktiv',
    default => $status,
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-2 rounded-full font-medium {$sizeClasses} {$statusClasses}"]) }}>
    <div class="rounded-full {{ $dotSizeClass }} {{ $status === 'active' ? 'bg-green-300' : 'bg-gray-300' }}"></div>
    {{ $slot->isEmpty() ? $statusText : $slot }}
</span>