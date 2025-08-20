@props([
    'route',
    'filters' => []
])

<div class="bg-card rounded-xl border border-border p-6 shadow-sm">
    <form method="GET" action="{{ $route }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{ $slot }}
        </div>

        @if(isset($statusFilter))
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-muted-foreground">Status:</span>
                {{ $statusFilter }}
            </div>
        @endif

        <div class="flex justify-between items-center">
            <div class="flex space-x-3">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                    </svg>
                    Filter anwenden
                </button>
                <a href="{{ $route }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-foreground bg-secondary rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Zurücksetzen
                </a>
            </div>
        </div>
    </form>
</div>