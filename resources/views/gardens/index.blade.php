@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.index
    title="Meine Gärten"
    subtitle="Verwalte und durchstöbere deine Gärten"
>
    <x-slot:actions>
        @include('gardens.partials.index._actions')
    </x-slot:actions>

    <x-slot:stats>
        @include('gardens.partials.index._statistics')
    </x-slot:stats>

    @include('gardens.partials.index._results_info')

    <div class="space-y-6">
        @if($gardens->count() > 0)
            @include('gardens.partials.index._garden_cards')
        @else
            @include('gardens.partials.index._empty_state')
        @endif
    </div>
</x-layouts.index>
