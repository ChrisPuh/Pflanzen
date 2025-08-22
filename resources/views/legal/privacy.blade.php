<x-layouts.guest>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-surface-2 rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-foreground mb-8">Datenschutzerklärung</h1>
            
            <div class="prose prose-lg max-w-none text-muted space-y-6">
                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">1. Verantwortlicher</h2>
                    <p>
                        Verantwortlich für die Datenverarbeitung auf dieser Website ist:<br>
                        <strong>[Dein Name/Firmenname]</strong><br>
                        [Deine Adresse]<br>
                        [E-Mail: deine@email.de]
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">2. Erfassung und Verwendung von Daten</h2>
                    <h3 class="text-lg font-medium text-foreground mb-2">2.1 Registrierung</h3>
                    <p>
                        Für die Nutzung von {{ config('app.name') }} ist eine Registrierung erforderlich. 
                        Dabei erfassen wir folgende Daten:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Name</li>
                        <li>E-Mail-Adresse</li>
                        <li>Passwort (verschlüsselt gespeichert)</li>
                    </ul>
                    <p class="mt-2">
                        Diese Daten verwenden wir ausschließlich zur Bereitstellung unserer Dienste 
                        und zur Kommunikation mit Ihnen.
                    </p>

                    <h3 class="text-lg font-medium text-foreground mb-2 mt-4">2.2 Garten- und Pflanzendaten</h3>
                    <p>
                        Sie können in Ihrem Account Informationen über Ihre Gärten, Bereiche und Pflanzen speichern. 
                        Diese Daten werden ausschließlich für die Bereitstellung der App-Funktionalität verwendet.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">3. Cookies und Tracking</h2>
                    <p>
                        Wir verwenden nur technisch notwendige Cookies:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li><strong>Session-Cookies:</strong> Für die Anmeldung und Navigation</li>
                        <li><strong>Theme-Präferenz:</strong> Speicherung Ihrer Dark/Light Mode Einstellung</li>
                        <li><strong>Sidebar-Zustand:</strong> Speicherung der Sidebar-Position</li>
                    </ul>
                    <p class="mt-2">
                        Wir verwenden keine Tracking-Cookies oder Analytics von Drittanbietern.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">4. Weitergabe von Daten</h2>
                    <p>
                        Ihre Daten werden nicht an Dritte weitergegeben, verkauft oder vermietet. 
                        Eine Weitergabe erfolgt nur in folgenden Fällen:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Sie haben ausdrücklich eingewilligt</li>
                        <li>Die Weitergabe ist gesetzlich vorgeschrieben</li>
                        <li>Es ist zur Durchsetzung unserer Nutzungsbedingungen erforderlich</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">5. Ihre Rechte</h2>
                    <p>Sie haben das Recht auf:</p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Auskunft über Ihre gespeicherten Daten</li>
                        <li>Berichtigung unrichtiger Daten</li>
                        <li>Löschung Ihrer Daten</li>
                        <li>Einschränkung der Verarbeitung</li>
                        <li>Datenübertragbarkeit</li>
                        <li>Widerspruch gegen die Verarbeitung</li>
                    </ul>
                    <p class="mt-2">
                        Zur Ausübung Ihrer Rechte können Sie uns jederzeit unter [deine@email.de] kontaktieren.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">6. Datensicherheit</h2>
                    <p>
                        Wir treffen angemessene technische und organisatorische Maßnahmen, 
                        um Ihre Daten vor unbefugtem Zugriff, Verlust oder Missbrauch zu schützen. 
                        Alle Passwörter werden verschlüsselt gespeichert.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">7. Änderungen</h2>
                    <p>
                        Wir behalten uns vor, diese Datenschutzerklärung bei Bedarf zu aktualisieren. 
                        Die jeweils aktuelle Version finden Sie auf dieser Seite.
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-foreground mb-4">8. Kontakt</h2>
                    <p>
                        Bei Fragen zum Datenschutz können Sie uns unter [deine@email.de] erreichen.
                    </p>
                </section>

                <div class="mt-8 pt-6 border-t border-border text-sm text-muted">
                    <p>Stand: {{ date('d.m.Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>