<x-layouts.guest>
    <!-- Forgot Password Section -->
    <section class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Forgot Password Card -->
            <div class="bg-surface-2 rounded-lg shadow-md border border-default overflow-hidden">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-foreground">{{ __('Forgot Password') }}</h1>
                        <p class="text-muted mt-2">
                            {{ __('Enter your email to receive a password reset link') }}</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-success bg-success/10 border border-success/20 rounded-lg p-3">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf
                        <!-- Email Input -->
                        <div>
                            <x-forms.input name="email" type="email" label="Email" placeholder="your@email.com" />
                        </div>

                        <!-- Send Reset Link Button -->
                        <x-button type="primary" buttonType="submit" class="w-full">
                            {{ __('Send Password Reset Link') }}
                        </x-button>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="text-center mt-6 pt-4 border-t border-default">
                        <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">{{ __('Back to login') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.guest>
