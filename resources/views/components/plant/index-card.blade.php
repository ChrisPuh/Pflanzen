@props([
    'plant'
])

<x-ui.index-card :href="route('plants.show', $plant)">
    <!-- Header -->
    <x-slot:header>
        <div class="aspect-square bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center">
            <svg class="h-16 w-16 text-primary/60 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
            </svg>
        </div>
    </x-slot:header>

    <!-- Title -->
    <x-slot:title>
        <h3 class="font-semibold text-foreground group-hover:text-primary transition-colors">
            {{ $plant->name }}
        </h3>
        @if($plant->latin_name)
            <p class="text-sm text-muted-foreground italic">
                {{ $plant->latin_name }}
            </p>
        @endif
    </x-slot:title>

    <!-- Plant Type Badge -->
    <div class="flex items-center space-x-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
            {{ $plant->plantType->name->getLabel() }}
        </span>
    </div>

    <!-- Categories -->
    @if($plant->categories->count() > 0)
        <div class="flex flex-wrap gap-1">
            @foreach($plant->categories->take(3) as $category)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-secondary text-secondary-foreground">
                    {{ $category->name->getLabel() }}
                </span>
            @endforeach
            @if($plant->categories->count() > 3)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-muted text-muted-foreground">
                    +{{ $plant->categories->count() - 3 }}
                </span>
            @endif
        </div>
    @endif

    <!-- Description -->
    @if($plant->description)
        <p class="text-sm text-muted-foreground line-clamp-2">
            {{ $plant->description }}
        </p>
    @endif

    <!-- Action Link -->
    <x-slot:action>
        <span class="text-sm font-medium text-primary group-hover:underline">
            Details anzeigen →
        </span>
    </x-slot:action>
</x-ui.index-card>