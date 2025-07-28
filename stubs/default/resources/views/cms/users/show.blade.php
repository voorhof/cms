@php use App\Models\Role; @endphp
<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-people me-1"></i>
            {{ __('Users') }}
        </h1>
    </x-slot>

    <x-slot name="actionButtons">
        <a class="btn btn-sm lh-sm me-sm-auto" href="{{ route(config('cms.route_name_prefix').'.users.index') }}">
            <i class="bi bi-arrow-left"></i> {{ __('All users') }}
        </a>

        @can('update', $user)
            <a class="btn btn-outline-primary btn-sm lh-sm"
               href="{{ route(config('cms.route_name_prefix').'.users.edit', $user) }}">
                <i class="bi bi-pencil-square"></i> {{ __('Edit user') }}
            </a>
        @endcan

        @can('delete', $user)
            {{-- Trigger delete user modal --}}
            <x-cms.button type="button" class="btn-outline-danger btn-sm lh-sm" data-bs-toggle="modal"
                          data-bs-target="#deleteUserModal">
                <i class="bi bi-trash"></i> {{ __('Delete user') }}
            </x-cms.button>

            {{-- Delete user modal--}}
            @push('modals')
                <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title fs-5" id="deleteUserModalLabel">
                                    {{ __('Delete user') .': ' . $user->name }}
                                </h2>

                                <x-cms.button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"/>
                            </div>

                            <div class="modal-body">
                                {{ __('Are you sure?') }}
                            </div>

                            <div class="modal-footer justify-content-between">
                                <x-cms.button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                    {{ __('Cancel') }}
                                </x-cms.button>

                                {{-- Delete user form --}}
                                <form method="POST"
                                      action="{{ route(config('cms.route_name_prefix').'.users.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-cms.button class="btn-danger">
                                        <i class="bi bi-trash"></i> {{ __('Delete') }}
                                    </x-cms.button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endpush
        @endcan
    </x-slot>

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                @if(! $user->email_verified_at)
                    <i class="bi bi-person-dash text-danger"></i>
                @else
                    <i class="bi bi-person-check text-success"></i>
                @endif

                {{ $user->name }}
            </h2>

            <div class="row gap-4 pt-3">
                <div class="col-auto">
                    <h3 class="fs-5">
                        {{ __('Details') }}
                    </h3>

                    <table class="table w-auto">
                        <tr>
                            <th>ID</th>
                            <td>{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>{{__('Name') }}</th>
                            <td>{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td><a href="mailto:{{ $user->email }}" target="_blank">{{ $user->email }}</a></td>
                        </tr>
                        <tr>
                            <th>{{ __('Verified at') }}</th>
                            <td>{{ $user->email_verified_at }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Created at') }}</th>
                            <td>{{ $user->created_at }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Updated at') }}</th>
                            <td>{{ $user->updated_at }}</td>
                        </tr>
                    </table>
                </div>

                @can('viewAny', Role::class)
                    <div class="col-auto">
                        <h3 class="fs-5">
                            {{ __('Role') }}
                        </h3>

                        @forelse($user->roles as $role)
                            @if($loop->first)
                                <ul class="list-unstyled">
                                    @endif

                                    <li class="text-capitalize">
                                        <i class="bi bi-shield-lock"></i>

                                        @can('view', $role)
                                            <a href="{{ route(config('cms.route_name_prefix').'.roles.show', $role) }}"
                                               class="link-dark ms-1">
                                                {{ $role->name }}
                                            </a>
                                        @else
                                            {{ $role->name }}
                                        @endcan
                                    </li>

                                    @if($loop->last)
                                </ul>
                            @endif
                        @empty
                            <p class="fst-italic">
                                <i class="bi bi-exclamation-circle"></i> {{ __('No role found') }}
                            </p>
                        @endforelse

                        <h4 class="fs-5">
                            {{ __('Permissions') }}
                        </h4>

                        @forelse($user->getAllPermissions()->sortby('name') as $permission)
                            @if($loop->first)
                                <ul>
                                    @endif

                                    <li class="text-capitalize">
                                        {{ $permission->name }}
                                    </li>

                                    @if($loop->last)
                                </ul>
                            @endif
                        @empty
                            <p class="fst-italic">
                                <i class="bi bi-exclamation-circle"></i> {{ __('No permissions found') }}
                            </p>
                        @endforelse
                    </div>
                @endcan
            </div>
        </div>
    </div>
</x-cms-layout>
