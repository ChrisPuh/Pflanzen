@props([
    'title' => null,
    'subtitle' => null,
    'formAction',
    'method' => 'POST',
    'cancelRoute' => null,
    'maxWidth' => 'max-w-2xl',
])

<x-layouts.page :title="$title" :subtitle="$subtitle">
    @if(isset($actions))
        <x-slot:actions>
            {{ $actions }}
        </x-slot:actions>
    @endif
    <div class="{{ $maxWidth }} mx-auto">
        <div class="bg-card rounded-xl border border-border p-6 shadow-sm">
            <form method="{{ $method === 'PUT' ? 'POST' : $method }}" action="{{ $formAction }}" class="space-y-6" {{ $attributes }}>
                @csrf
                @if($method === 'PUT')
                    @method('PUT')
                @endif
                {{ $slot }}

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-border">
                    <button 
                        type="submit" 
                        class="px-6 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors font-medium"
                    >
                        {{ $submitText ?? 'Speichern' }}
                    </button>
                    <a 
                        href="{{ $cancelRoute ?? url()->previous() }}" 
                        class="px-6 py-2 bg-secondary text-secondary-foreground rounded-lg hover:bg-secondary/80 focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-colors font-medium text-center"
                    >
                        Abbrechen
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.page>