@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.show
    :title="$garden->name"
    :subtitle="$garden->type->getLabel()"
    actions-partial="gardens.partials.show._actions"
    :model="$garden"
>
    <div class="space-y-8">
        @include('gardens.partials.show._header')

        @include('gardens.partials.show._details')

        @include('gardens.partials.show._areas')

        @include('gardens.partials.show._plants')
    </div>
</x-layouts.show>
