<!-- Plants in Garden -->
<x-ui.card title="Pflanzen in diesem Garten">
    @if($areasStats['total_plants'] > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($garden->plants()->get() as $plant)
                <div class="flex items-center p-3 bg-secondary/50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div
                            class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h4 class="text-sm font-medium text-foreground">{{ $plant->name }}</h4>
                        <p class="text-xs text-muted-foreground">
                            {{ $plant->latin_name ?? 'Wissenschaftlicher Name nicht verfügbar' }}
                        </p>
                        @if($plant->pivot && $plant->pivot->quantity)
                            <p class="text-xs text-muted-foreground mt-1">
                                {{ $plant->pivot->quantity }} Stück
                            </p>
                        @endif
                    </div>
                    <div class="ml-3">
                        <a
                            href="{{ route('plants.show', $plant) }}"
                            class="text-primary hover:text-primary/80 text-sm font-medium"
                        >
                            Anzeigen →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-4 border-t border-border text-sm text-muted-foreground">
            Insgesamt {{ $areasStats['total_plants'] }} {{ $areasStats['total_plants'] === 1 ? 'Pflanze' : 'Pflanzen' }} in diesem Garten
        </div>
    @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-muted-foreground mx-auto mb-3" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
            </svg>
            <p class="text-muted-foreground text-sm">Noch keine Pflanzen in diesem Garten</p>
            <a
                href="{{ route('plants.index') }}"
                class="text-primary hover:text-primary/80 text-sm font-medium mt-2 inline-block"
            >
                Pflanzen durchstöbern →
            </a>
        </div>
    @endif
</x-ui.card>