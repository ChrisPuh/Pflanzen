@props([
    'title',
    'subtitle' => null,

])

<x-layouts.page :title="$title" :subtitle="$subtitle">
    @if(isset($actions))
        <x-slot:actions>
            {{$actions}}
        </x-slot:actions>
    @endif
    <!-- Statistics Section -->
    @if(isset($stats))
        <div class="mb-6">
            {{ $stats }}
        </div>
    @endif
    <!-- Filter Section -->
    @if(isset($filters))
        <div class="mb-6">
            {{ $filters }}
        </div>
    @endif
    {{$slot}}
</x-layouts.page>
