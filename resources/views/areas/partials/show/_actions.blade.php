<!-- Actions Dropdown -->
<x-ui.back-button
    :href="route('areas.index')"
    text="Zurück zur Übersicht"
/>
@if(auth()->user()->can('update', $model ?? $area) || auth()->user()->can('delete', $model ?? $area))
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
                @can('update', $model ?? $area)
                    <a
                        href="{{ route('areas.edit', $model ?? $area) }}"
                        class="flex items-center px-4 py-2 text-sm text-foreground hover:bg-secondary/50 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Bereich bearbeiten
                    </a>

                    <div class="border-t border-border my-1"></div>
                @endcan

                @can('delete', $model ?? $area)
                    <button
                        onclick="if(confirm('Möchtest du den Bereich {{ json_encode(($model ?? $area)->name) }} wirklich löschen? Er kann später wiederhergestellt werden.')) { document.getElementById('delete-area-form').submit(); }"
                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Bereich löschen
                    </button>

                    <form id="delete-area-form" method="POST" action="{{ route('areas.destroy', $model ?? $area) }}"
                          class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endcan
            </div>
        </div>
    </div>
@endif