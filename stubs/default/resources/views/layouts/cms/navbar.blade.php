<nav class="cms-navbar navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container-xl">
        {{-- Sidebar Toggle --}}
        <button class="navbar-toggler" type="button"
                data-js-class="js-show-sidebar" aria-label="Toggle sidebar">
            <i class="bi bi-three-dots-vertical"></i>
        </button>

        {{-- Collapse toggle --}}
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#CmsNavbarCollapse" aria-controls="CmsNavbarCollapse" aria-expanded="false" aria-label="Toggle navbar navigation">
            <i class="bi bi-list"></i>
        </button>

        {{-- Collapse container --}}
        <div class="collapse navbar-collapse" id="CmsNavbarCollapse">
            {{-- Left menu --}}
            <div class="navbar-nav">
                <x-cms.nav-link title="{{ __('Example Link') }}" class="py-lg-0" />
            </div>

            {{-- Right menu --}}
            <div class="navbar-nav ms-auto">
                {{-- Dropdown profile --}}
                <x-cms.dropdown align="right" class="nav-item" togglerClasses="nav-link">
                    <x-slot:trigger>
                        {{ Auth::user()->name }}
                    </x-slot:trigger>

                    <x-slot:content>
                        @if (Route::has('profile.edit'))
                            <x-cms.dropdown-link :href="route('profile.edit')">
                                <i class="bi bi-person-circle me-1"></i> {{ __('Profile') }}
                            </x-cms.dropdown-link>
                        @endif

                        <x-cms.dropdown-link>
                            <i class="bi bi-box me-1"></i> Another action
                        </x-cms.dropdown-link>

                        <hr class="dropdown-divider border-secondary">

                        {{-- Log out form --}}
                        <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                            @csrf

                            <x-cms.button class="dropdown-item btn-link">
                                <i class="bi bi-power me-1"></i> {{ __('Log Out') }}
                            </x-cms.button>
                        </form>
                    </x-slot:content>
                </x-cms.dropdown>
            </div>
        </div>
    </div>
</nav>
