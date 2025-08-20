@props(['action', 'method' => 'GET'])

<div class="bg-card rounded-xl border border-border p-6 shadow-sm">
    <form method="{{ $method }}" action="{{ $action }}" class="space-y-4">
        {{ $slot }}

        <!-- Filter Actions -->
        <div class="flex flex-col justify-end sm:flex-row gap-3 pt-2">
            <button
                type="submit"
                class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors font-medium"
            >
                Filter anwenden
            </button>
            <a
                href="{{ $action }}"
                class="px-6 py-2 bg-secondary text-secondary-foreground rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors font-medium text-center"
            >
                Filter zurücksetzen
            </a>
        </div>
    </form>
</div>
