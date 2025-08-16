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
        <span class="text-muted">{{ __('Password') }}</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-foreground">{{ __('Update password') }}</h1>
        <p class="text-muted mt-1">
            {{ __('Ensure your account is using a long, random password to stay secure') }}
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
                        <form class="max-w-md mb-10" action="{{ route('settings.password.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <x-forms.input label="Current Password" name="current_password" type="password" />
                            </div>

                            <div class="mb-6">
                                <x-forms.input label="New Password" name="password" type="password" />
                            </div>

                            <div class="mb-6">
                                <x-forms.input label="Confirm Password" name="password_confirmation" type="password" />
                            </div>

                            <div>
                                <x-button type="primary">{{ __('Update Password') }}</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
