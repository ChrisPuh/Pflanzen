@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.create-page
    title="Neuen Garten erstellen"
    subtitle="Erstelle einen neuen Garten und füge ihn zu deiner Sammlung hinzu"
    :form-action="route('gardens.store')"
    :cancel-route="route('gardens.index')"
    submit-text="Garten erstellen"
>
    <x-slot:actions>
        @include('gardens.partials.create._actions')
    </x-slot:actions>

    <div class="space-y-6">
        @include('gardens.partials.create._basic_info')

        @include('gardens.partials.create._location')

        @include('gardens.partials.create._coordinates')

        @include('gardens.partials.create._status')
    </div>

</x-layouts.create-page>
