<!-- Garden Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <x-stats-card
        title="Gärten"
        :value="$stats['total_gardens'] . ' (' . $stats['active_gardens'] . ' aktiv)'"
        icon-color="green"
    >
        <x-slot:icon>
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 21v-4a4 4 0 014-4h5a4 4 0 014 4v4M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14l9-5-9-5-9 5 9 5z"></path>
            </svg>
        </x-slot:icon>
    </x-stats-card>

    <x-stats-card
        title="Gesamte Pflanzen"
        :value="$stats['total_plants']"
        icon-color="blue"
    >
        <x-slot:icon>
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v6.5l3 3V21a2 2 0 002-2V5z"></path>
            </svg>
        </x-slot:icon>
    </x-stats-card>

    <x-stats-card
        title="Bereiche"
        :value="$stats['total_areas'] . ($stats['active_areas'] > 0 ? ' (' . $stats['active_areas'] . ' aktiv)' : '')"
        icon-color="purple"
    >
        <x-slot:icon>
            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </x-slot:icon>
    </x-stats-card>
</div>