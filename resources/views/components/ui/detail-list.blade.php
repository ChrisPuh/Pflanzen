@props([
    'details' => [],
    'title' => null
])

<x-ui.card :title="$title">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($details as $key => $detail)
            <div class="flex items-center gap-2">
                <dt class="text-sm font-medium text-muted-foreground">{{ $detail['label'] }}</dt>
                @if($key === 'status')
                    <x-ui.status-badge :status="$detail['value']" />
                @else
                    <dd class="text-sm text-foreground truncate">{{ $detail['value'] }}</dd>
                @endif
            </div>
        @endforeach
    </dl>
</x-ui.card>
