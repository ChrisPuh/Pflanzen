@php
    use App\Enums\PlantCategoryEnum;
    use App\Enums\PlantTypeEnum;
@endphp

<x-layouts.page
    :title="$plant->name"
    :subtitle="$plant->latin_name"
>
    <x-slot:actions>
        <a
            href="{{ route('plants.index') }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Zurück zu Pflanzen
        </a>
        @role('admin')
        <a
            href="{{ route('filament.admin.resources.plants.edit', $plant) }}"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Bearbeiten
        </a>
        @endrole
    </x-slot:actions>
    <div class="space-y-8">

        <!-- Main Plant Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Plant Image -->
            <div class="lg:col-span-1">
                <div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm">
                    <!-- Plant Image Placeholder -->
                    <div
                        class="aspect-square bg-gradient-to-br from-primary/10 to-primary/20 flex items-center justify-center">
                        <svg class="h-32 w-32 text-primary/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Plant Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-foreground">{{ $plant->name }}</h1>
                            @if($plant->latin_name)
                                <p class="text-xl text-muted-foreground italic mt-1">{{ $plant->latin_name }}</p>
                            @endif
                        </div>

                        <!-- Plant Type Badge -->
                        <span
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary">
                            {{ $plant->plantType->name->getLabel() }}
                        </span>
                    </div>

                    <!-- Categories -->
                    @if($plant->categories->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-foreground mb-2">Kategorien</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($plant->categories as $category)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-secondary text-secondary-foreground">
                                        {{ $category->name->getLabel() }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Description -->
                    @if($plant->description)
                        <div>
                            <h3 class="text-sm font-semibold text-foreground mb-2">Beschreibung</h3>
                            <div class="prose prose-sm max-w-none text-muted-foreground">
                                {!! nl2br(e($plant->description)) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Plant Details Card -->
                    <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-foreground mb-4">Pflanzendetails</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Typ</dt>
                                <dd class="text-sm text-foreground mt-1">{{ $plant->plantType->name->getLabel() }}</dd>
                            </div>
                            @if($plant->categories->count() > 0)
                                <div>
                                    <dt class="text-sm font-medium text-muted-foreground">Kategorien</dt>
                                    <dd class="text-sm text-foreground mt-1">
                                        {{ $plant->categories->pluck('name')->map(fn($cat) => $cat->getLabel())->join(', ') }}
                                    </dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-muted-foreground">Hinzugefügt am</dt>
                                <dd class="text-sm text-foreground mt-1">{{ $plant->created_at->format('d.m.Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-foreground mb-4">Schnelle Aktionen</h3>
                        <div class="space-y-3">
                            <a
                                href="{{ route('plants.index', ['search' => $plant->name]) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Ähnliche Pflanzen finden
                            </a>

                            <a
                                href="{{ route('plants.index', ['type' => $plant->plantType->name->value]) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Alle {{ $plant->plantType->name->getLabel() }} anzeigen
                            </a>

                            @if($plant->categories->count() > 0)
                                <a
                                    href="{{ route('plants.index', ['categories' => [$plant->categories->first()->name->value]]) }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    {{ $plant->categories->first()->name->getLabel() }} Pflanzen
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.page>
