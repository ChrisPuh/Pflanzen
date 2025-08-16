<x-layouts.page title="{{ __('Dashboard') }}" subtitle="{{ __('Welcome to the dashboard') }}">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-surface-2 rounded-lg shadow-sm p-6 border border-default">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">{{ __('Total Users') }}</p>
                    <p class="text-2xl font-bold text-foreground mt-1">--</p>
                    <p class="text-xs text-muted flex items-center mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        {{ __('No data') }}
                    </p>
                </div>
                <div class="bg-primary-soft p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="bg-surface-2 rounded-lg shadow-sm p-6 border border-default">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">{{ __('Total Revenue') }}</p>
                    <p class="text-2xl font-bold text-foreground mt-1">--</p>
                    <p class="text-xs text-muted flex items-center mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        {{ __('No data') }}
                    </p>
                </div>
                <div class="bg-success-soft p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-surface-2 rounded-lg shadow-sm p-6 border border-default">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">{{ __('Total Orders') }}</p>
                    <p class="text-2xl font-bold text-foreground mt-1">--</p>
                    <p class="text-xs text-muted flex items-center mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        {{ __('No data') }}
                    </p>
                </div>
                <div class="bg-info-soft p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-info"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Visitors Card -->
        <div class="bg-surface-2 rounded-lg shadow-sm p-6 border border-default">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted">{{ __('Total Visitors') }}</p>
                    <p class="text-2xl font-bold text-foreground mt-1">--</p>
                    <p class="text-xs text-muted flex items-center mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        {{ __('No data') }}
                    </p>
                </div>
                <div class="bg-warning-soft p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

</x-layouts.page>
