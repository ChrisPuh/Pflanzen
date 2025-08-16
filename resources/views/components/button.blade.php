@props([
    'type' => 'primary',
    'buttonType' => 'submit',
    'tag' => 'button',
])

@php
    $styleClasses = \Illuminate\Support\Arr::toCssClasses([
        'cursor-pointer',
        match ($type) {
            'primary' => 'btn btn-primary focus:ring-primary',
            'danger' => 'btn btn-danger focus:ring-danger',
            default => 'btn btn-primary focus:ring-primary',
        },
    ]);
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $styleClasses]) }}>
    {{ $slot }}
</{{ $tag }}>
