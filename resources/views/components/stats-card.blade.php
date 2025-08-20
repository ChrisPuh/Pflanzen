@props([
    'title',
    'value',
    'icon',
    'iconColor' => 'green'
])

<div class="bg-card rounded-xl border border-border p-6 shadow-sm">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-{{ $iconColor }}-100 dark:bg-{{ $iconColor }}-900/20 rounded-lg flex items-center justify-center">
                {{ $icon }}
            </div>
        </div>
        <div class="ml-5 w-0 flex-1">
            <dl>
                <dt class="text-sm font-medium text-muted-foreground truncate">
                    {{ $title }}
                </dt>
                <dd class="text-lg font-medium text-foreground">
                    {{ $value }}
                </dd>
            </dl>
        </div>
    </div>
</div>