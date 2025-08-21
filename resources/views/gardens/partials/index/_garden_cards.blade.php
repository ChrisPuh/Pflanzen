<!-- Gardens Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($gardens as $garden)
        <x-garden.index-card :garden="$garden" :isAdmin="$isAdmin" />
    @endforeach
</div>

<!-- Pagination -->
@if($gardens->hasPages())
    <div class="flex justify-center">
        {{ $gardens->links() }}
    </div>
@endif