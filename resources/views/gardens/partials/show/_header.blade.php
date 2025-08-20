<!-- Garden Header -->
<x-ui.header :garden="$garden">
    <x-slot:heading>
        <div class="flex items-start justify-between mb-4">

            <p class="text-xl text-muted-foreground mt-1">{{ $garden->type->getLabel() }}</p>

            <!-- Area Type Badge with Color -->
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary"
                @if($garden->color) style="background-color: {{ $garden->color }}20; color: {{ $garden->color }}" @endif>
                    {{ $garden->type->getLabel() }}
            </span>
        </div>
    </x-slot:heading>
    <!-- Description -->
    @if($garden->description)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-foreground mb-2">Beschreibung</h3>
            <div class="prose prose-sm max-w-none text-muted-foreground">
                {!! nl2br(e($garden->description)) !!}
            </div>
        </div>
    @endif
    <x-ui.status-badge :status="$garden->is_active ? 'active' : 'inactive'" />

    <!-- Status -->
</x-ui.header>
