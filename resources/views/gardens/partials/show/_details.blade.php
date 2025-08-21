<!-- Garden Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Garden Map/Image Placeholder -->
    <div class="lg:col-span-1">
        <x-ui.card>
            <div
                class="aspect-square bg-gradient-to-br from-secondary/30 to-secondary/50 rounded-lg flex items-center justify-center relative">
                @if($garden->hasCoordinates())
                    <div class="absolute top-4 right-4">
                        <div class="bg-white/90 dark:bg-gray-900/90 rounded-full p-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                @endif

                <div class="text-center">
                    <svg class="h-16 w-16 text-muted-foreground mx-auto mb-2" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-muted-foreground">{{ $garden->type->getLabel() }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Garden Information -->
    <div class="lg:col-span-2">
        <x-ui.detail-list 
            :details="$garden->getDetails()"
            title="Garten-Informationen"
        />
    </div>
</div>
