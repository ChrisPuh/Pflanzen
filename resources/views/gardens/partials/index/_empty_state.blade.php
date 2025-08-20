<!-- Empty State -->
<div class="text-center py-16">
    <svg class="mx-auto h-24 w-24 text-muted-foreground/50" fill="none" stroke="currentColor"
         viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
              d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
    </svg>
    <h3 class="text-lg font-medium text-foreground mt-4">Keine Gärten gefunden</h3>
    <p class="text-muted-foreground mt-2">
        Du hast noch keine Gärten erstellt.
    </p>
    <x-ui.action-button
        variant="primary"
        href="{{ route('gardens.create') }}"
        class="mt-4"
    >
        <x-slot:icon>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </x-slot:icon>
        Ersten Garten erstellen
    </x-ui.action-button>
</div>