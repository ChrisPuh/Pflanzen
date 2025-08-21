@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.create-page
    title="Garten bearbeiten"
    subtitle="Bearbeite die Details deines Gartens"
    :form-action="route('gardens.update', $garden)"
    :cancel-route="route('gardens.show', $garden)"
    method="PUT"
    submit-text="Garten aktualisieren"
>
    <x-slot:actions>
        @include('gardens.partials.edit._actions')
    </x-slot:actions>
    <div class="space-y-6">
        @include('gardens.partials.edit._basic_info')

        @include('gardens.partials.edit._location')

        @include('gardens.partials.edit._status_date')

        @include('gardens.partials.edit._coordinates')
    </div>
</x-layouts.create-page>
