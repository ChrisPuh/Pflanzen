<!-- Actions Dropdown -->
<x-ui.back-button
    :href="route('gardens.index')"
    text="Zurück zur Übersicht"
/>
@if(auth()->user()->can('update', $garden) || auth()->user()->can('delete', $garden))
    <div class="relative" x-data="{ open: false }">
        <button
            @click="open = !open"
            @click.away="open = false"
            class="inline-flex items-center px-3 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
        </button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute right-0 z-10 mt-2 w-56 rounded-lg bg-card border border-border shadow-lg"
            x-cloak
        >
            <div class="py-1">
                @can('update', $garden)
                    <a
                        href="{{ route('areas.create', ['garden_id' => $garden->id]) }}"
                        class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-secondary/50 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"></path>
                        </svg>
                        Bereich hinzufügen
                    </a>

                    <a
                        href="{{ route('gardens.edit', $garden) }}"
                        class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-secondary/50 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Garten bearbeiten
                    </a>

                    <div class="border-t border-border my-1"></div>
                @endcan

                @can('delete', $garden)
                    <button
                        onclick="if(confirm('Möchtest du den Garten {{ json_encode($garden->name) }} wirklich archivieren? Er kann später wiederhergestellt werden.')) { document.getElementById('archive-garden-form').submit(); }"
                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 8l4 4 4-4m0 0l4-4m-4 4v11"></path>
                        </svg>
                        Garten archivieren
                    </button>

                    <form id="archive-garden-form" method="POST" action="{{ route('gardens.destroy', $garden) }}"
                          class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endif
