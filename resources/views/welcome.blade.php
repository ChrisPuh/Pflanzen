<x-layouts.guest>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary/5 to-success/5 py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Hero Title -->
                <h1 class="text-4xl md:text-6xl font-bold text-foreground mb-6">
                    Willkommen bei
                    <span class="text-primary">{{ config('app.name') }}</span>
                </h1>
                
                <!-- Hero Subtitle -->
                <p class="text-xl md:text-2xl text-muted mb-8 max-w-3xl mx-auto">
                    Verwalte deine Gärten, organisiere deine Pflanzen und entdecke neue Möglichkeiten für deinen grünen Daumen.
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="bg-primary text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-primary/90 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fas fa-seedling mr-2"></i>
                            Jetzt starten
                        </a>
                    @endif
                    
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                            class="border-2 border-primary text-primary px-8 py-4 rounded-lg text-lg font-semibold hover:bg-primary hover:text-white transition-colors">
                            Bereits registriert? Anmelden
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">
                    Was {{ config('app.name') }} bietet
                </h2>
                <p class="text-lg text-muted max-w-2xl mx-auto">
                    Alles was du brauchst, um deine Gartenarbeit zu organisieren und zu optimieren.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1: Garten Management -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tree text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-foreground mb-3">Garten Management</h3>
                    <p class="text-muted">
                        Erstelle und verwalte mehrere Gärten. Organisiere sie nach Standort, Typ und individuellen Bedürfnissen.
                    </p>
                </div>

                <!-- Feature 2: Bereich Organisation -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-th-large text-2xl text-success"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-foreground mb-3">Bereich Organisation</h3>
                    <p class="text-muted">
                        Teile deine Gärten in Bereiche auf und verwalte verschiedene Zonen wie Gemüsebeet, Blumenbeet oder Gewächshaus.
                    </p>
                </div>

                <!-- Feature 3: Pflanzen Katalog -->
                <div class="text-center p-6 rounded-lg bg-surface-2 border border-default">
                    <div class="w-16 h-16 bg-info/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-seedling text-2xl text-info"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-foreground mb-3">Pflanzen Katalog</h3>
                    <p class="text-muted">
                        Entdecke eine große Auswahl an Pflanzen mit detaillierten Informationen zu Pflege, Standort und Eigenschaften.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="bg-primary/5 py-16">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-foreground mb-4">
                Bereit, deinen Garten zu organisieren?
            </h2>
            <p class="text-lg text-muted mb-8">
                Starte heute und erlebe, wie einfach Gartenmanagement sein kann.
            </p>
            
            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="bg-primary text-white px-10 py-4 rounded-lg text-xl font-semibold hover:bg-primary/90 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center">
                    <i class="fas fa-arrow-right mr-3"></i>
                    Kostenlos registrieren
                </a>
            @endif
        </div>
    </section>
</x-layouts.guest>