<div class="w-full md:w-64 shrink-0 border-r border-default pr-4">
    <nav class="bg-surface-2 rounded-lg overflow-hidden">
        <ul class="divide-y divide-default">
            <li>
                <a href="{{ route('settings.profile.edit') }}" @class([
                    'block px-4 py-3 text-muted hover:bg-surface-2' => !request()->routeIs('settings.profile.*'),
                    'bg-surface-2 block px-4 py-3 text-foreground font-medium' => request()->routeIs('settings.profile.*'),
                ])>
                    {{ __('Profile') }}
                </a>
            </li>
            <li>
                <a href="{{ route('settings.password.edit') }}" @class([
                    'block px-4 py-3 text-muted hover:bg-surface-2' => !request()->routeIs('settings.password.*'),
                    'bg-surface-2 block px-4 py-3 text-foreground font-medium' => request()->routeIs('settings.password.*'),
                ])>
                    {{ __('Password') }}
                </a>
            </li>
            <li>
                <a href="{{ route('settings.appearance.edit') }}" @class([
                    'block px-4 py-3 text-muted hover:bg-surface-2' => !request()->routeIs('settings.appearance.*'),
                    'bg-surface-2 block px-4 py-3 text-foreground font-medium' => request()->routeIs('settings.appearance.*'),
                ])>
                    {{ __('Appearance') }}
                </a>
            </li>
        </ul>
    </nav>
</div>
