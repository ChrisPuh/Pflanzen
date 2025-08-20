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
        <a
            href="{{ route('gardens.show', $garden) }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Zurück zum Garten
        </a>
    </x-slot:actions>
    
    <div class="space-y-6">
        @include('gardens.partials.edit._basic_info')
        
        @include('gardens.partials.edit._location')
        
        @include('gardens.partials.edit._status_date')
        
        @include('gardens.partials.edit._coordinates')
    </div>

</x-layouts.create-page>