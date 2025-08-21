@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.page
    :title="$garden->name"
    :subtitle="$garden->type->getLabel()"
>
    <x-slot:actions>
        <div class="flex items-center gap-3">
            @include('gardens.partials.show._actions')
        </div>
    </x-slot:actions>
    <div class="space-y-8">
        @include('gardens.partials.show._header')

        @include('gardens.partials.show._details')

        @include('gardens.partials.show._areas')

        @include('gardens.partials.show._plants')
    </div>
</x-layouts.page>
