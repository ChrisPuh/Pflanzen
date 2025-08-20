@props([
    'title' => null,
    'subtitle' => null,
    'eventUrl' => null,
])

<x-layouts.app>
    <div class="mb-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <!-- Left: Title, Subtitle, Event URL -->
            <div class="min-w-0">
                @if($title)
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        {{ $title }}
                    </h1>
                @endif

                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        {{ $subtitle }}
                    </p>
                @endif

                @if($eventUrl)
                    <p class="mt-2">
                        <a href="{{ $eventUrl }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1 text-sm text-primary hover:opacity-90 underline">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                <path d="M13.5 3a1.5 1.5 0 000 3H17.3l-6.14 6.14a1.5 1.5 0 102.12 2.12L19.4 8.21V12a1.5 1.5 0 003 0V4.5A1.5 1.5 0 0020.9 3H13.5z" />
                                <path d="M6.75 5.25A2.25 2.25 0 004.5 7.5v10.5A2.25 2.25 0 006.75 20.25h10.5a2.25 2.25 0 002.25-2.25V13.5a1.5 1.5 0 10-3 0v3.75a.75.75 0 01-.75.75H7.5a.75.75 0 01-.75-.75V7.5a.75.75 0 01.75-.75H11.25a1.5 1.5 0 000-3H6.75z" />
                            </svg>
                            {{ __('Zum Event') }}
                        </a>
                    </p>
                @endif
            </div>

            <!-- Right: Actions Slot -->
            <div class="flex shrink-0 items-center gap-2 md:mt-0">
                {{ $actions ?? '' }}
            </div>
        </div>
    </div>
    <!-- Page Content -->
    <div>
        {{ $slot }}
    </div>
</x-layouts.app>

{{--
Beispiel-Nutzung:

<x-layouts.page title="Pflanzen" subtitle="Verwalte deine Pflanzen" event-url="https://example.com/event">
    <x-slot:actions>
        <a href="{{ route('plants.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
            + Neue Pflanze
        </a>
    </x-slot:actions>

    <div>
        <!-- Dein Seiteninhalt -->
    </div>
</x-layouts.page>
--}}
