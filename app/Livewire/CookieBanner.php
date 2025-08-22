<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

final class CookieBanner extends Component
{
    public bool $showBanner = false;

    public bool $showSettings = false;

    public function mount(): void
    {
        // Only show banner if user hasn't made a choice yet
        // We check this via a cookie that gets set when user makes a choice
        $this->showBanner = ! request()->hasCookie('cookie_consent');
    }

    public function acceptAll(): void
    {
        $this->setCookieConsent([
            'essential' => true,
            'analytics' => false,
            'marketing' => false,
        ]);
        $this->hideBanner();
    }

    public function acceptEssential(): void
    {
        $this->setCookieConsent([
            'essential' => true,
            'analytics' => false,
            'marketing' => false,
        ]);
        $this->hideBanner();
    }

    public function toggleSettings(): void
    {
        $this->showSettings = ! $this->showSettings;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.cookie-banner');
    }

    private function setCookieConsent(array $preferences): void
    {
        $consent = json_encode([
            ...$preferences,
            'timestamp' => time(),
            'version' => '1.0',
        ]);

        // Set cookie for 1 year
        cookie()->queue('cookie_consent', $consent, 60 * 24 * 365);
    }

    private function hideBanner(): void
    {
        $this->showBanner = false;
        $this->showSettings = false;
    }
}
