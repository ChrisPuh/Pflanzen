<div>
    @if($showBanner)
        <div class="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6">
            <div class="max-w-7xl mx-auto">
                <div class="bg-surface-2 border border-default rounded-lg shadow-lg p-6 md:flex md:items-center md:justify-between">
                    <!-- Content -->
                    <div class="md:flex-1 md:mr-6">
                        <h3 class="text-lg font-semibold text-foreground mb-2">
                            <i class="fas fa-cookie-bite mr-2"></i>
                            Cookies & Datenschutz
                        </h3>
                        <p class="text-muted text-sm mb-4 md:mb-0">
                            Wir verwenden nur technisch notwendige Cookies für die Funktionalität der Website. 
                            Keine Tracking-Cookies oder Werbung.
                            <a href="{{ route('privacy') }}" class="text-primary hover:underline ml-1">
                                Mehr erfahren
                            </a>
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 md:flex-shrink-0">
                        <!-- Settings Button -->
                        <button wire:click="toggleSettings"
                                class="px-4 py-2 text-sm font-medium text-muted hover:text-foreground border border-default rounded-lg hover:bg-hover transition-colors">
                            <i class="fas fa-cog mr-2"></i>
                            Einstellungen
                        </button>

                        <!-- Accept All Button -->
                        <button wire:click="acceptAll"
                                class="px-6 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                            <i class="fas fa-check mr-2"></i>
                            Alle akzeptieren
                        </button>
                    </div>
                </div>

                <!-- Settings Panel -->
                @if($showSettings)
                    <div class="mt-4 bg-surface-2 border border-default rounded-lg shadow-lg p-6">
                        <h4 class="text-lg font-semibold text-foreground mb-4">Cookie-Einstellungen</h4>
                        
                        <div class="space-y-4">
                            <!-- Essential Cookies -->
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h5 class="font-medium text-foreground">Notwendige Cookies</h5>
                                    <p class="text-sm text-muted mt-1">
                                        Diese Cookies sind für die Grundfunktionen der Website erforderlich und können nicht deaktiviert werden.
                                    </p>
                                    <ul class="text-xs text-muted mt-2 list-disc list-inside">
                                        <li>Session-Management (Anmeldung)</li>
                                        <li>Theme-Präferenz (Dark/Light Mode)</li>
                                        <li>Sidebar-Zustand</li>
                                    </ul>
                                </div>
                                <div class="ml-4">
                                    <div class="bg-success text-white px-3 py-1 rounded text-xs font-medium">
                                        Erforderlich
                                    </div>
                                </div>
                            </div>

                            <!-- Analytics Cookies (Future) -->
                            <div class="flex items-start justify-between opacity-50">
                                <div class="flex-1">
                                    <h5 class="font-medium text-foreground">Analyse-Cookies</h5>
                                    <p class="text-sm text-muted mt-1">
                                        Helfen uns zu verstehen, wie die Website genutzt wird. (Aktuell nicht verwendet)
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <div class="relative">
                                        <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner cursor-not-allowed">
                                            <div class="w-4 h-4 bg-white rounded-full shadow translate-x-1 translate-y-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Marketing Cookies (Future) -->
                            <div class="flex items-start justify-between opacity-50">
                                <div class="flex-1">
                                    <h5 class="font-medium text-foreground">Marketing-Cookies</h5>
                                    <p class="text-sm text-muted mt-1">
                                        Für personalisierte Werbung und Inhalte. (Aktuell nicht verwendet)
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <div class="relative">
                                        <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner cursor-not-allowed">
                                            <div class="w-4 h-4 bg-white rounded-full shadow translate-x-1 translate-y-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Actions -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-4 border-t border-default">
                            <button wire:click="acceptEssential"
                                    class="px-4 py-2 text-sm font-medium text-muted hover:text-foreground border border-default rounded-lg hover:bg-hover transition-colors">
                                Nur notwendige
                            </button>
                            <button wire:click="acceptAll"
                                    class="px-6 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors">
                                Alle akzeptieren
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>