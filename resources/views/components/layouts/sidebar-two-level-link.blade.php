@props(['active' => false, 'href' => '#', 'icon' => 'fas-house'])

<a href="{{ $href }}" @class([
    'flex items-center px-3 py-2 text-sm rounded-md transition-colors duration-200',
    'bg-hover text-hover-foreground font-medium' => $active,
    'hover:bg-hover hover:text-hover-foreground text-sidebar-foreground' => !$active,
])>
    <div class="flex items-center">
        @svg($icon, $active ? 'w-5 h-5 mr-3 text-hover-foreground' : 'w-5 h-5 mr-3 text-sidebar-foreground opacity-70')
        <span x-data="{}" :class="{ 'opacity-0 hidden': !sidebarOpen }">{{ $slot }}</span>
    </div>
</a>
