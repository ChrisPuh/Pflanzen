<x-layouts.guest>
    <!-- About Hero Section -->
    <section class="bg-gradient-to-br from-success/5 to-primary/5 py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-foreground mb-6">
                    Über {{ config('app.name') }}
                </h1>
                <p class="text-xl md:text-2xl text-muted mb-8 max-w-4xl mx-auto">
                    Eine moderne Plattform für Gartenliebhaber und alle, die ihre grünen Oasen professionell verwalten möchten.
                </p>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">
                    {{ config('app.name') }} in Zahlen
                </h2>
                <p class="text-lg text-muted max-w-2xl mx-auto">
                    Entdecke die Vielfalt unserer Plattform und was unsere Community bereits erreicht hat.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                <!-- Pflanzen Statistik -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-seedling text-2xl text-success"></i>
                    </div>
                    <div class="text-3xl font-bold text-foreground mb-2">{{ number_format($stats['plants_count']) }}</div>
                    <div class="text-sm text-muted">Pflanzen verfügbar</div>
                </div>

                <!-- Pflanzentypen Statistik -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-leaf text-2xl text-primary"></i>
                    </div>
                    <div class="text-3xl font-bold text-foreground mb-2">{{ number_format($stats['plant_types_count']) }}</div>
                    <div class="text-sm text-muted">Verschiedene Pflanzentypen</div>
                </div>

                <!-- Kategorien Statistik -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tags text-2xl text-info"></i>
                    </div>
                    <div class="text-3xl font-bold text-foreground mb-2">{{ number_format($stats['categories_count']) }}</div>
                    <div class="text-sm text-muted">Kategorien</div>
                </div>

                <!-- Benutzer Statistik -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-warning"></i>
                    </div>
                    <div class="text-3xl font-bold text-foreground mb-2">{{ number_format($stats['users_count']) }}</div>
                    <div class="text-sm text-muted">Registrierte Benutzer</div>
                </div>

                <!-- Gärten Statistik -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tree text-2xl text-success"></i>
                    </div>
                    <div class="text-3xl font-bold text-foreground mb-2">{{ number_format($stats['gardens_count']) }}</div>
                    <div class="text-sm text-muted">Verwaltete Gärten</div>
                </div>

                <!-- Bereiche Statistik -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-th-large text-2xl text-primary"></i>
                    </div>
                    <div class="text-3xl font-bold text-foreground mb-2">{{ number_format($stats['areas_count']) }}</div>
                    <div class="text-sm text-muted">Organisierte Bereiche</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="bg-surface-2 py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-6">
                        Unsere Mission
                    </h2>
                    <p class="text-lg text-muted mb-6">
                        Wir möchten jedem Gartenliebhaber dabei helfen, seine grünen Träume zu verwirklichen. 
                        Mit {{ config('app.name') }} wird Gartenmanagement so einfach wie nie zuvor.
                    </p>
                    <p class="text-lg text-muted mb-8">
                        Unsere Plattform kombiniert moderne Technologie mit praktischer Gartenerfahrung, 
                        um dir die besten Tools für deine Gartenarbeit zur Verfügung zu stellen.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-user-plus mr-2"></i>
                                Jetzt mitmachen
                            </a>
                        @endif
                        <a href="{{ route('home') }}"
                            class="border border-primary text-primary px-6 py-3 rounded-lg font-semibold hover:bg-primary hover:text-white transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-home mr-2"></i>
                            Zur Startseite
                        </a>
                    </div>
                </div>
                <div class="lg:text-center">
                    <div class="inline-block p-8 bg-primary/5 rounded-2xl">
                        <i class="fas fa-heart text-6xl text-primary mb-4"></i>
                        <h3 class="text-2xl font-bold text-foreground mb-4">
                            Gemacht mit Leidenschaft
                        </h3>
                        <p class="text-muted">
                            Für Gartenliebhaber von Gartenliebhabern entwickelt.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Overview -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">
                    Was macht uns besonders?
                </h2>
                <p class="text-lg text-muted max-w-2xl mx-auto">
                    {{ config('app.name') }} bietet eine einzigartige Kombination aus Funktionen, 
                    die deine Gartenarbeit revolutionieren werden.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-xl text-success"></i>
                    </div>
                    <h3 class="font-semibold text-foreground mb-2">Responsive Design</h3>
                    <p class="text-sm text-muted">Funktioniert perfekt auf allen Geräten</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-database text-xl text-primary"></i>
                    </div>
                    <h3 class="font-semibold text-foreground mb-2">Umfangreiche Database</h3>
                    <p class="text-sm text-muted">Detaillierte Informationen zu jeder Pflanze</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-info/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-xl text-info"></i>
                    </div>
                    <h3 class="font-semibold text-foreground mb-2">Übersichtliche Statistiken</h3>
                    <p class="text-sm text-muted">Behalte deine Gärten im Überblick</p>
                </div>

                <!-- Feature 4 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-xl text-warning"></i>
                    </div>
                    <h3 class="font-semibold text-foreground mb-2">Sicher & Zuverlässig</h3>
                    <p class="text-sm text-muted">Deine Daten sind bei uns geschützt</p>
                </div>
            </div>
        </div>
    </section>
</x-layouts.guest>