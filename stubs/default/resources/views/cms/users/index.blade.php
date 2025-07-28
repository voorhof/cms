@php use App\Models\User; @endphp
<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-people me-1"></i>
            {{ __('Users') }}
        </h1>
    </x-slot>

    @canany(['create', 'viewTrash'], User::class)
        <x-slot name="actionButtons">
            @can('create', User::class)
                <a class="btn btn-outline-primary btn-sm lh-sm"
                   href="{{ route(config('cms.route_name_prefix').'.users.create') }}">
                    <i class="bi bi-plus-circle"></i> {{ __('New user') }}
                </a>
            @endcan

            @can('viewTrash', User::class)
                <a class="btn btn-outline-secondary btn-sm lh-sm ms-sm-auto"
                   href="{{ route(config('cms.route_name_prefix').'.users.viewTrash') }}">
                    <i class="bi bi-trash"></i> {{ __('Trash') }}
                    <span class="{{ $usersTrashCount > 0 ? 'fw-bold' : 'fw-light' }}">
                        {{ '(' . $usersTrashCount . ')' }}
                    </span>
                </a>
            @endcan
        </x-slot>
    @endcanany

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('All users') }}
            </h2>

            @forelse($users as $user)
                @if($loop->first)
                    <ul class="list-unstyled">
                        @endif

                        <li class="mb-1">
                            @can('view', $user)
                                <a class="icon-link link-dark link-underline-opacity-25 link-underline-opacity-100-hover"
                                   href="{{ route(config('cms.route_name_prefix').'.users.show', $user) }}">
                                    @if(! $user->email_verified_at)
                                        <i class="bi bi-person-dash text-danger"></i>
                                    @else
                                        <i class="bi bi-person-check text-success"></i>
                                    @endif

                                    {{ $user->name }}
                                </a>
                            @else
                                <i class="bi bi-person text-dark"></i>
                                {{ $user->name }}
                            @endcan
                        </li>

                        @if($loop->last)
                    </ul>
                @endif
            @empty
                <p class="fw-bold fst-italic">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ __('No users found') }}
                </p>
            @endforelse
        </div>
    </div>
</x-cms-layout>
