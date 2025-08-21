@props([
    'href'
])

<div class="bg-card rounded-xl border border-border overflow-hidden shadow-sm hover:shadow-md transition-all group transform hover:scale-[1.02] relative">
    <!-- Actions Slot (top-right corner) -->
    @isset($actions)
        <div class="absolute top-2 right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
            {{ $actions }}
        </div>
    @endisset

    <a href="{{ $href }}" class="block cursor-pointer">
        <!-- Header Slot -->
        @isset($header)
            {{ $header }}
        @endisset

        <!-- Content Section -->
        <div class="p-4 space-y-3">
            <!-- Title Section -->
            @isset($title)
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        {{ $title }}
                    </div>
                    @isset($badge)
                        {{ $badge }}
                    @endisset
                </div>
            @endisset

            <!-- Metadata Slot -->
            @isset($metadata)
                {{ $metadata }}
            @endisset

            <!-- Stats Slot -->
            @isset($stats)
                {{ $stats }}
            @endisset

            <!-- Main Content Slot -->
            {{ $slot }}

            <!-- Footer Action Link -->
            @isset($action)
                <div class="pt-2">
                    {{ $action }}
                </div>
            @endisset
        </div>
    </a>
</div>