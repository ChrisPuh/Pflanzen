<!-- Garden Header -->
<div class="mb-8">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <!-- Garden Type Badge -->
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                    {{ $garden->type->getLabel() }}
                </span>
                
                <x-ui.status-badge :status="$garden->is_active ? 'active' : 'inactive'" />
            </div>
            
            <!-- Description -->
            @if($garden->description)
                <p class="text-base text-muted-foreground mb-4 max-w-3xl">
                    {{ $garden->description }}
                </p>
            @endif
        </div>
    </div>
</div>