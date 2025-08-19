@props(['stats' => []])

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($stats as $stat)
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 {{ $stat['iconBg'] ?? 'bg-green-100 dark:bg-green-900/20' }} rounded-lg flex items-center justify-center">
                        @if(isset($stat['iconComponent']))
                            <x-dynamic-component :component="$stat['iconComponent']" class="w-5 h-5 {{ $stat['iconClass'] ?? 'text-green-600 dark:text-green-400' }}" />
                        @else
                            {!! $stat['icon'] ?? '' !!}
                        @endif
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-muted-foreground truncate">
                            {{ $stat['label'] }}
                        </dt>
                        <dd class="text-lg font-medium text-foreground">
                            {{ $stat['value'] }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    @endforeach
</div>