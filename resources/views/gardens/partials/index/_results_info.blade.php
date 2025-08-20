<!-- Results Information -->
<div class="flex items-center justify-between mb-6">
    <p class="text-muted-foreground">
        {{ $gardens->total() }} {{ $gardens->total() === 1 ? 'Garten' : 'Gärten' }} gefunden
        @if($isAdmin)
            <span class="text-orange-600 dark:text-orange-400 font-medium">(Admin-Ansicht)</span>
        @endif
    </p>
    @if($gardens->hasPages())
        <p class="text-sm text-muted-foreground">
            Seite {{ $gardens->currentPage() }} von {{ $gardens->lastPage() }}
        </p>
    @endif
</div>