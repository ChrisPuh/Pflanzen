@props([
    'title' => null,
    'subtitle' => null,
    'actions' => null,
])

<div {{ $attributes->merge(['class' => 'bg-card rounded-xl border border-border p-6 shadow-sm']) }}>
    @if($title || $subtitle || $actions)
        <div class="flex items-center justify-between mb-4">
            <div>
                @if($title)
                    <h3 class="text-lg font-semibold text-foreground">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-sm text-muted-foreground">{{ $subtitle }}</p>
                @endif
            </div>
            
            @if($actions)
                <div>{{ $actions }}</div>
            @endif
        </div>
    @endif
    
    {{ $slot }}
</div>