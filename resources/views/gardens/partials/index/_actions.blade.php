<!-- Header Actions -->
<div class="flex items-center gap-3">
    @if($hasArchivedGardens)
        <x-ui.action-button 
            variant="secondary"
            href="{{ route('gardens.archived') }}"
        >
            <x-slot:icon>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 0l4-4m-4 4v11"></path>
            </x-slot:icon>
            Archivierte Gärten
        </x-ui.action-button>
    @endif

    <x-ui.action-button 
        variant="primary"
        href="{{ route('gardens.create') }}"
    >
        <x-slot:icon>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </x-slot:icon>
        Neuen Garten erstellen
    </x-ui.action-button>
</div>