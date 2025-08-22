<x-layouts.guest>
    <!-- Register Section -->
    <section class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Register Card -->
            <div class="bg-surface-2 rounded-lg shadow-md border border-default overflow-hidden">
                <div class="p-6">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-foreground text-center">{{ __('Register an account') }}</h1>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf
                        <!-- Full Name Input -->
                        <div>
                            <x-forms.input label="Full Name" name="name" type="text" placeholder="{{ __('Full Name') }}" />
                        </div>

                        <!-- Email Input -->
                        <div>
                            <x-forms.input label="Email" name="email" type="email" placeholder="your@email.com" />
                        </div>

                        <!-- Password Input -->
                        <div>
                            <x-forms.input label="Password" name="password" type="password" placeholder="••••••••" />
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <x-forms.input label="Confirm Password" name="password_confirmation" type="password"
                                placeholder="••••••••" />
                        </div>

                        <!-- Register Button -->
                        <x-button type="primary" class="w-full">{{ __('Create Account') }}</x-button>
                    </form>

                    <!-- Login Link -->
                    <div class="text-center mt-6 pt-4 border-t border-default">
                        <p class="text-sm text-muted">
                            Already have an account?
                            <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">{{ __('Sign in') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.guest>
