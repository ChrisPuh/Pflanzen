@props([
    'title' => null,
    'subtitle' => null,
    'backRoute' => null,
    'backText' => 'Zurück zur Übersicht',
    'actionsPartial' => null,
    'model' => null,
])

<x-layouts.app>
    <div class="mb-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
            <!-- Left: Title and Subtitle -->
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
            </div>

            <!-- Right: Actions -->
            <div class="flex shrink-0 items-center gap-3 md:mt-0">
                @if($actionsPartial)
                    @include($actionsPartial, ['model' => $model])
                @elseif(isset($backRoute))
                    <x-ui.back-button
                        :href="$backRoute"
                        :text="$backText"
                    />
                @endif
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
Beispiel-Nutzung für Garden Show:

<x-layouts.show
    :title="$garden->name"
    :subtitle="$garden->type->getLabel()"
    actions-partial="gardens.partials.show._actions"
    :model="$garden"
>
    <div class="space-y-8">
        <!-- Garden content -->
    </div>
</x-layouts.show>

Beispiel-Nutzung für Area Show:

<x-layouts.show
    :title="$area->name"
    :subtitle="$area->type->getLabel()"
    actions-partial="areas.partials.show._actions"
    :model="$area"
>
    <div class="space-y-8">
        <!-- Area content -->
    </div>
</x-layouts.show>

Einfache Nutzung mit nur Back-Button:

<x-layouts.show
    title="Simple Page"
    subtitle="With only back button"
    :back-route="route('simple.index')"
    back-text="Zurück zu Simple"
>
    <div>
        <!-- Simple content -->
    </div>
</x-layouts.show>
--}}