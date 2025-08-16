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
        <span class="text-muted">{{ __('Profile') }}</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">{{ __('Profile') }}</h1>
        <p class="text-muted mt-1">{{ __('Update your name and email address') }}</p>
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
                        <form class="max-w-md mb-10" action="{{ route('settings.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <x-forms.input label="Name" name="name" type="text"
                                    value="{{ old('name', $user->name) }}" />
                            </div>

                            <div class="mb-6">
                                <x-forms.input label="Email" name="email" type="email"
                                    value="{{ old('email', $user->email) }}" />
                            </div>

                            <div>
                                <x-button type="primary">{{ __('Save') }}</x-button>
                            </div>
                        </form>

                        <!-- Delete Account Section -->
                        <div class="border-t border-default pt-6 mt-6">
                            <h2 class="text-lg font-medium text-foreground mb-1">
                                {{ __('Delete account') }}
                            </h2>
                            <p class="text-muted mb-4">
                                {{ __('Delete your account and all of its resources') }}
                            </p>
                            <form action="{{ route('settings.profile.destroy') }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure you want to delete your account?') }}')">
                                @csrf
                                @method('DELETE')
                                <x-button type="danger">{{ __('Delete account') }}</x-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
