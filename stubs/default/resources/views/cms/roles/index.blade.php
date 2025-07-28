@php use App\Models\Role; @endphp
<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-shield-lock me-1"></i>
            {{ __('Roles') }}
        </h1>
    </x-slot>

    @can('create', Role::class)
        <x-slot name="actionButtons">
            <a class="btn btn-outline-primary btn-sm lh-sm"
               href="{{ route(config('cms.route_name_prefix').'.roles.create') }}">
                <i class="bi bi-plus-circle"></i> {{ __('New role') }}
            </a>
        </x-slot>
    @endcan

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('All roles') }}
            </h2>

            @forelse($roles as $role)
                @if($loop->first)
                    <ul class="list-unstyled">
                        @endif

                        <li class="mb-1">
                            @can('view', $role)
                                <a class="icon-link link-dark link-underline-opacity-25 link-underline-opacity-100-hover text-capitalize"
                                   href="{{ route(config('cms.route_name_prefix').'.roles.show', $role) }}">
                                    <i class="bi bi-shield-lock"></i>
                                    {{ $role->name }}
                                </a>
                            @else
                                <i class="bi bi-shield-lock"></i>
                                {{ $role->name }}
                            @endcan
                        </li>

                        @if($loop->last)
                    </ul>
                @endif
            @empty
                <p class="fw-bold fst-italic">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ __('No roles found') }}
                </p>
            @endforelse
        </div>
    </div>
</x-cms-layout>
