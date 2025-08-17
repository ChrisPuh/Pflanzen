@props(['active' => false, 'href' => '#'])

<a href="{{ $href }}" @class([
    'flex items-center px-3 py-2 text-sm rounded-md transition-colors duration-200',
    'bg-hover text-hover-foreground font-medium' => $active,
    'hover:bg-hover hover:text-hover-foreground text-sidebar-foreground' => !$active,
])>
    <span x-data="{}" :class="{ 'opacity-0 hidden': !sidebarOpen }">{{ $slot }}</span>
</a>
