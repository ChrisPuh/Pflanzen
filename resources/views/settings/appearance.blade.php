<x-layouts.app>
    <!-- Breadcrumbs -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-primary hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('settings.profile.edit') }}" class="text-primary hover:underline">{{ __('Profile') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-muted">{{ __('Appearance') }}</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">{{ __('Appearance') }}</h1>
        <p class="text-muted mt-1">
            {{ __('Update the appearance settings for your account') }}
        </p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Navigation -->
            @include('settings.partials.navigation')

            <!-- Profile Content -->
            <div class="flex-1">
                <div class="bg-surface-2 rounded-lg shadow-sm border border-default overflow-hidden mb-6">
                    <div class="p-6">
                        <!-- Profile Form -->
                        <div class="mb-4">
                            <label for="theme" class="block text-sm font-medium text-muted mb-1">{{ __('Theme') }}</label>
                            <div class="inline-flex rounded-md shadow-sm" role="group">
                                <button onclick="setAppearance('light')" value="light"
                                    class="px-4 py-2 text-sm font-medium text-foreground bg-surface-2 border border-default rounded-l-lg hover:opacity-90 focus:z-10 focus:ring-2 focus:ring-primary">
                                    {{ __('Light') }}
                                </button>
                                <button onclick="setAppearance('dark')" value="dark"
                                    class="px-4 py-2 text-sm font-medium text-foreground bg-surface-2 border-t border-b border-default hover:opacity-90 focus:z-10 focus:ring-2 focus:ring-primary">
                                    {{ __('Dark') }}
                                </button>
                                <button onclick="setAppearance('system')" value="system"
                                    class="px-4 py-2 text-sm font-medium text-foreground bg-surface-2 border border-default rounded-r-md hover:opacity-90 focus:z-10 focus:ring-2 focus:ring-primary">
                                    {{ __('System') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
