@props([
    'show' => false,
    'maxWidth' => '4xl',
    'closeable' => true,
    'title' => null,
    'subtitle' => null,
])

<div
    x-show="{{ $show }}"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        @if($closeable)
            <div
                class="fixed inset-0 bg-surface-2 opacity-75 transition-opacity"
                {{ $attributes->whereStartsWith(['wire:', '@']) }}
            ></div>
        @else
            <div class="fixed inset-0 bg-surface-2 opacity-75 transition-opacity"></div>
        @endif

        <!-- Modal Content -->
        <div
            x-show="{{ $show }}"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative bg-surface rounded-lg shadow-xl w-full max-w-{{ $maxWidth }} max-h-[90vh] overflow-hidden z-10"
        >
            @if($title || $closeable)
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-default">
                    @if($title || $subtitle)
                        <div>
                            @if($title)
                                <h3 class="text-lg font-semibold text-foreground">
                                    {{ $title }}
                                </h3>
                            @endif
                            @if($subtitle)
                                <p class="text-sm text-muted mt-1">
                                    {{ $subtitle }}
                                </p>
                            @endif
                        </div>
                    @endif

                    @if($closeable)
                        <x-ui.action-button
                            variant="secondary"
                            size="sm"
                            class="p-2"
                            :icon="'<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M6 18L18 6M6 6l12 12\'></path>'"
                            {{ $attributes->whereStartsWith(['wire:', '@']) }}
                        />
                    @endif
                </div>
            @endif

            <!-- Modal Body -->
            <div class="flex-1 overflow-hidden">
                {{ $slot }}
            </div>

            @isset($footer)
                <!-- Modal Footer -->
                <div class="flex items-center justify-between p-6 border-t border-default bg-surface-2">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>