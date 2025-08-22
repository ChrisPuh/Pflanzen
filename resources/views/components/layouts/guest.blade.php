<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <!-- Theme Appearance Script -->
    <script>
        window.setAppearance = function(appearance) {
            let setDark = () => document.documentElement.classList.add('dark');
            let setLight = () => document.documentElement.classList.remove('dark');
            let setButtons = (appearance) => {
                document.querySelectorAll('button[onclick^="setAppearance"]').forEach((button) => {
                    button.setAttribute('aria-pressed', String(appearance === button.value));
                });
            };
            if (appearance === 'system') {
                let media = window.matchMedia('(prefers-color-scheme: dark)');
                window.localStorage.removeItem('appearance');
                media.matches ? setDark() : setLight();
            } else if (appearance === 'dark') {
                window.localStorage.setItem('appearance', 'dark');
                setDark();
            } else if (appearance === 'light') {
                window.localStorage.setItem('appearance', 'light');
                setLight();
            }
            if (document.readyState === 'complete') {
                setButtons(appearance);
            } else {
                document.addEventListener('DOMContentLoaded', () => setButtons(appearance));
            }
        };
        window.setAppearance(window.localStorage.getItem('appearance') || 'system');
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface text-foreground antialiased">
    <!-- Guest Navigation -->
    <header class="bg-surface-2 border-b border-default">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Navigation Links -->
                <div class="flex items-center space-x-8">
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="text-xl font-semibold text-primary hover:text-primary/80 transition-colors">
                            {{ config('app.name') }}
                        </a>
                    </div>
                    
                    <!-- Navigation Links next to logo -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('home') }}"
                            class="@if(request()->routeIs('home')) text-primary border-b-2 border-primary @else text-muted hover:text-foreground @endif px-3 py-2 text-sm font-medium transition-colors">
                            Home
                        </a>
                        <a href="{{ route('about') }}"
                            class="@if(request()->routeIs('about')) text-primary border-b-2 border-primary @else text-muted hover:text-foreground @endif px-3 py-2 text-sm font-medium transition-colors">
                            Über uns
                        </a>
                    </div>
                </div>

                <!-- Right Side Navigation -->
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-muted hover:text-foreground px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-muted hover:text-foreground px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Anmelden
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors">
                                    Registrieren
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif

                <!-- Mobile menu button (for future mobile menu implementation) -->
                <div class="md:hidden">
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-muted hover:text-foreground px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-muted hover:text-foreground px-2 py-1 rounded-md text-xs font-medium transition-colors">
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="bg-primary text-white px-3 py-1 rounded-lg text-xs font-medium hover:bg-primary/90 transition-colors">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
            
            <!-- Mobile Navigation Links -->
            <div class="md:hidden pb-3 pt-2 border-t border-default mt-3">
                <div class="flex space-x-6">
                    <a href="{{ route('home') }}"
                        class="@if(request()->routeIs('home')) text-primary border-b-2 border-primary @else text-muted hover:text-foreground @endif pb-2 text-sm font-medium transition-colors">
                        Home
                    </a>
                    <a href="{{ route('about') }}"
                        class="@if(request()->routeIs('about')) text-primary border-b-2 border-primary @else text-muted hover:text-foreground @endif pb-2 text-sm font-medium transition-colors">
                        Über uns
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-surface-2 border-t border-default mt-16">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Legal Links -->
                <div class="flex justify-center space-x-6 mb-4">
                    <a href="{{ route('privacy') }}" 
                        class="text-muted hover:text-foreground text-sm transition-colors">
                        Datenschutz
                    </a>
                    <a href="{{ route('terms') }}" 
                        class="text-muted hover:text-foreground text-sm transition-colors">
                        AGB
                    </a>
                    <a href="{{ route('about') }}" 
                        class="text-muted hover:text-foreground text-sm transition-colors">
                        Über uns
                    </a>
                </div>
                
                <!-- Copyright -->
                <div class="text-muted text-sm">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.</p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>