@php
    use App\Enums\Garden\GardenTypeEnum;
@endphp

<x-layouts.page
    :title="$garden->name"
    :subtitle="$garden->type->getLabel()"
>
    <x-slot:actions>
        <div class="flex items-center gap-3">
            <a
                href="{{ route('gardens.index') }}"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Zurück zur Übersicht
            </a>

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
