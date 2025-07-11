@php
    $attributes = $attributes ?? null;

    $class = $attributes['class'] ?? '';
    $route = $attributes['route'] ?? null;
    $childRoutes = $attributes['childRoutes'] ?? null;

    $activeRoutes = [
        $route,
        /* Also set the active attribute on child routes */
        $route ? (Str::beforeLast($route, '.') === 'cms' ? null : Str::beforeLast($route, '.') .'.*' ) : null,
        $childRoutes,
    ];

    $routeParam = $attributes['routeParam'] ?? null;
    $href = $attributes['href'] ?? '#';
    $title = $attributes['title'] ?? '';
    $active = $attributes['active'] ?? request()->routeIs($activeRoutes);

    $attributes = $attributes->except(['class', 'route', 'childRoutes', 'routeParam', 'href', 'title', 'active']);
@endphp

<a @class(['nav-link', 'active' => $active, $class]) href="{{ $route ? route($route, $routeParam) : $href}}"
   @if($active)aria-current="page"@endif {{ $attributes }}>{!! $title !!}</a>
