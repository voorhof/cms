<div class="cms-sidebar d-flex flex-column">
    <div class="cms-sidebar-header d-flex align-items-center justify-content-center px-2 py-1">
        <a class="navbar-brand d-flex align-items-center gap-1 pe-2" href="{{ Route::has('dashboard') ? route('dashboard') : config('app.url') }}">
            <x-cms.application-logo style="fill: currentColor; width: 25px;" />
            {{ config('app.name', 'CMS') }}
        </a>
    </div>

    <nav class="cms-sidebar-nav nav nav-pills lh-sm flex-column px-2 py-3">
        <x-cms.nav-link title="<i class='bi bi-speedometer2'></i> {{ __('Dashboard') }}" class="mb-2"
                        route="{{ config('cms.route_name_prefix') }}.dashboard" />

        <x-cms.nav-link title="<i class='bi bi-people'></i> {{ __('Users') }}" class="mb-2"
                        route="{{ config('cms.route_name_prefix') }}.users.index" />

        @can('manage roles')
            <x-cms.nav-link title="<i class='bi bi-shield-lock'></i> {{ __('Roles') }}" class="mb-2"
                            route="{{ config('cms.route_name_prefix') }}.roles.index" />
        @endcan

{{--        <x-cms.nav-link title="<i class='bi bi-stickies'></i> {{ __('Posts') }}" class="mb-2" route="{{ config('cms.route_name_prefix') }}.posts.index" />--}}

{{--        <x-cms.nav-link title="<i class='bi bi-images'></i> {{ __('Media') }}" class="mb-2" route="{{ config('cms.route_name_prefix') }}.media.index" />--}}

        <x-cms.nav-link title="<i class='bi bi-link-45deg'></i> {{ __('Example link') }}" />

        <hr class="my-2">

        @can('access admin')
            access admin
{{--            <x-cms.nav-link title="<i class='bi bi-gear'></i> {{ __('Admin') }}" route="{{ config('cms.route_name_prefix') }}.admin.index" childRoutes="{{ config('cms.route_name_prefix') }}.admin.*" />--}}
            <hr class="my-2">
        @endcan

        @can('access logs')
            access logs
{{--            <x-cms.nav-link title="<i class='bi bi-journal-code'></i> {{ __('Log Viewer') }}" route="{{ config('cms.route_name_prefix') }}.log-viewer.index" target="_blank" />--}}
            <hr class="my-2">
        @endcan

        <x-cms.nav-link title="<i class='bi bi-github'></i> {{ __('GitHub') }}" target="_blank" href="https://github.com/voorhof"/>
    </nav>

    <div class="cms-sidebar-footer text-center lh-sm mt-auto p-1">
        <small>{{ config('app.name', 'CMS') }}<br>&copy; {{ now()->year }} </small>
    </div>
</div>
