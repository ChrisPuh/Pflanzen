<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
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
    @livewireStyles
</head>

<body class="bg-surface text-foreground antialiased" x-data="{
    sidebarOpen: localStorage.getItem('sidebarOpen') === null ? window.innerWidth >= 1024 : localStorage.getItem('sidebarOpen') === 'true',
    isMobile: window.innerWidth < 768,
    init() {
        window.addEventListener('resize', () => {
            const wasMobile = this.isMobile;
            this.isMobile = window.innerWidth < 768;
            // Close mobile sidebar when switching from mobile to desktop
            if (wasMobile && !this.isMobile && this.sidebarOpen) {
                // Keep sidebar open when switching to desktop
            }
        });
    },
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
        localStorage.setItem('sidebarOpen', this.sidebarOpen);
    },
    temporarilyOpenSidebar() {
        if (!this.sidebarOpen) {
            this.sidebarOpen = true;
            localStorage.setItem('sidebarOpen', true);
        }
    },
    formSubmitted: false,
}">

<!-- Main Container -->
<div class="h-screen flex flex-col overflow-hidden">

    <!-- Fixed Header -->
    <div class="fixed top-0 left-0 right-0 z-30">
        <x-layouts.app.header />
    </div>

    <!-- Main Content Area with fixed sidebar -->
    <div class="flex flex-1 pt-16 overflow-hidden">

        <!-- Fixed Sidebar -->
        <div class="fixed left-0 top-16 bottom-0 z-20 transition-all duration-300" 
             :class="{ 
                'w-64': sidebarOpen && !isMobile, 
                'w-16 hidden md:block': !sidebarOpen,
                'w-full md:w-64': sidebarOpen && isMobile
             }">
            <x-layouts.app.sidebar />
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen && isMobile" 
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
        </div>

        <!-- Scrollable Main Content -->
        <main class="flex-1 overflow-auto bg-surface content-transition transition-all duration-300"
              :class="{ 
                'ml-64': sidebarOpen && !isMobile, 
                'ml-16': !sidebarOpen && !isMobile,
                'ml-0': isMobile
              }">
            <div class="p-6">
                <!-- Success Message -->
                @session('status')
                <div x-data="{ showStatusMessage: true }" x-show="showStatusMessage"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mb-6 bg-success-soft border-l-4 border-success p-4 rounded-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-success"
                                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                      clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-success">{{ session('status') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button @click="showStatusMessage = false"
                                        class="inline-flex rounded-md p-1.5 text-success hover:bg-success-soft focus:outline-none focus:ring-2 focus:ring-offset-2">
                                    <span class="sr-only">{{ __('Dismiss') }}</span>
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                         fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endsession

                {{ $slot }}

            </div>
        </main>
    </div>
</div>
@livewireScripts
</body>

</html>
