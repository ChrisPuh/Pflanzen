<div class="w-full md:w-64 shrink-0 border-r border-default pr-4">
    <nav class="rounded-lg overflow-hidden">
        <ul class="">
            @php
            $navItems = [
                ['route' => 'settings.profile.*', 'url' => route('settings.profile.edit'), 'label' => __('Profile')],
                ['route' => 'settings.password.*', 'url' => route('settings.password.edit'), 'label' => __('Password')],
                ['route' => 'settings.appearance.*', 'url' => route('settings.appearance.edit'), 'label' => __('Appearance')],
            ];
            @endphp

            @foreach($navItems as $item)
                <li>
                    <a href="{{ $item['url'] }}" @class([
                        'block px-4 py-3 text-muted hover:bg-hover hover:text-hover-foreground transition-colors' => !request()->routeIs($item['route']),
                        'text-hover-foreground block px-4 py-3 font-medium' => request()->routeIs($item['route']),
                    ])>
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
