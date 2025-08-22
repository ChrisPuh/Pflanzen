            <aside class="h-full w-full bg-surface-2 text-foreground border-r border-default sidebar-transition overflow-hidden">
                <!-- Sidebar Content -->
                <div class="h-full flex flex-col">
                    <!-- Sidebar Menu -->
                    <nav class="flex flex-col justify-between h-full overflow-y-auto custom-scrollbar py-4">
                        <div>
                            <!-- HOME SECTION -->
                            <div class="px-2">
                                <div x-show="sidebarOpen" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3">
                                    Home
                                </div>
                                <ul class="space-y-1">
                                    <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon='fas-house'
                                        :active="request()->routeIs('dashboard*')">Dashboard</x-layouts.sidebar-link>
                                </ul>
                            </div>

                            <!-- Section Spacer -->
                            <div x-show="sidebarOpen" class="my-6 border-t border-border"></div>

                            <!-- GARTEN MANAGEMENT SECTION -->
                            <div class="px-2">
                                <div x-show="sidebarOpen" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3">
                                    Garten Management
                                </div>
                                <ul class="space-y-1">
                                    @can('viewAny', App\Models\Garden::class)
                                        <x-layouts.sidebar-link href="{{ route('gardens.index') }}" icon='fas-tree'
                                            :active="request()->routeIs('gardens*')">Meine Gärten</x-layouts.sidebar-link>
                                    @endcan

                                    @can('viewAny', App\Models\Area::class)
                                        <x-layouts.sidebar-link href="{{ route('areas.index') }}" icon='fas-th-large'
                                            :active="request()->routeIs('areas*')">Meine Bereiche</x-layouts.sidebar-link>
                                    @endcan
                                </ul>
                            </div>

                            <!-- Section Spacer -->
                            <div x-show="sidebarOpen" class="my-6 border-t border-border"></div>

                            <!-- PFLANZEN SECTION -->
                            <div class="px-2">
                                <div x-show="sidebarOpen" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3">
                                    Pflanzen
                                </div>
                                <ul class="space-y-1">
                                    <x-layouts.sidebar-link href="{{ route('plants.index') }}" icon='fas-seedling'
                                        :active="request()->routeIs('plants*')">Pflanzen entdecken</x-layouts.sidebar-link>
                                </ul>
                            </div>
                        </div>
                        <!-- SETTINGS SECTION -->
                        <div class="px-2 pt-4 border-t border-border">
                            <div x-show="sidebarOpen" class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3">
                                Einstellungen
                            </div>
                            <ul class="space-y-1">
                                <x-layouts.sidebar-two-level-link-parent title="Settings" icon="fas-gear"
                                                                         :active="request()->routeIs('settings*')">
                                    <x-layouts.sidebar-two-level-link href="{{ route('settings.profile.edit') }}" icon='fas-user'
                                                                      :active="request()->routeIs('settings.profile.*')">Profile</x-layouts.sidebar-two-level-link>
                                    <x-layouts.sidebar-two-level-link href="{{ route('settings.password.edit') }}" icon='fas-key'
                                                                      :active="request()->routeIs('settings.password.*')">Password</x-layouts.sidebar-two-level-link>
                                    <x-layouts.sidebar-two-level-link href="{{ route('settings.appearance.edit') }}" icon='fas-palette'
                                                                      :active="request()->routeIs('settings.appearance.*')">Appearance</x-layouts.sidebar-two-level-link>
                                </x-layouts.sidebar-two-level-link-parent>
                            </ul>
                        </div>

                        {{-- Example links (commented out) --}}
                        {{--
                        <div class="px-2">
                            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-3">
                                Examples
                            </div>
                            <ul class="space-y-1">
                                <!-- Example two level -->
                                <x-layouts.sidebar-two-level-link-parent title="Example two level" icon="fas-house"
                                                                         :active="request()->routeIs('two-level*')">
                                    <x-layouts.sidebar-two-level-link href="#" icon='fas-house'
                                                                      :active="request()->routeIs('two-level*')">Child</x-layouts.sidebar-two-level-link>
                                </x-layouts.sidebar-two-level-link-parent>

                                <!-- Example three level -->
                                <x-layouts.sidebar-two-level-link-parent title="Example three level" icon="fas-house"
                                                                         :active="request()->routeIs('three-level*')">
                                    <x-layouts.sidebar-two-level-link href="#" icon='fas-house'
                                                                      :active="request()->routeIs('three-level*')">Single Link</x-layouts.sidebar-two-level-link>

                                    <x-layouts.sidebar-three-level-parent title="Third Level" icon="fas-house"
                                                                          :active="request()->routeIs('three-level*')">
                                        <x-layouts.sidebar-three-level-link href="#" :active="request()->routeIs('three-level*')">
                                            Third Level Link
                                        </x-layouts.sidebar-three-level-link>
                                    </x-layouts.sidebar-three-level-parent>
                                </x-layouts.sidebar-two-level-link-parent>
                            </ul>
                        </div>
                        --}}
                    </nav>
                </div>
            </aside>
