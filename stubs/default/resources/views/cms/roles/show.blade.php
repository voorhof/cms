<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-shield-lock me-1"></i>
            {{ __('Roles') }}
        </h1>
    </x-slot>

    <x-slot name="actionButtons">
        <a class="btn btn-sm lh-sm" href="{{ route(config('cms.route_name_prefix').'.roles.index') }}">
            <i class="bi bi-arrow-left"></i> {{ __('All roles') }}
        </a>

        @can('update', $role)
            <a class="btn btn-outline-primary btn-sm lh-sm ms-sm-auto" href="{{ route(config('cms.route_name_prefix').'.roles.edit', $role) }}">
                <i class="bi bi-pencil-square"></i> {{ __('Edit role') }}
            </a>
        @endcan

        @can('delete', $role)
            {{-- Trigger delete role modal --}}
            <x-cms.button type="button" class="btn-outline-danger btn-sm lh-sm" data-bs-toggle="modal" data-bs-target="#deleteRoleModal">
                <i class="bi bi-trash"></i> {{ __('Delete role') }}
            </x-cms.button>

            {{-- Delete role modal--}}
            @push('modals')
                <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title fs-5" id="deleteRoleModalLabel">
                                    {{ __('Delete role') .': ' . $role->name }}
                                </h2>

                                <x-cms.button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
                            </div>

                            <div class="modal-body">
                                {{ __('Are you sure?') }} <br>
                                <strong class="text-danger">{{ __('This action cannot be undone!') }}</strong>
                            </div>

                            <div class="modal-footer justify-content-between">
                                <x-cms.button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                    {{ __('Cancel') }}
                                </x-cms.button>


                                {{-- Delete role form --}}
                                <form method="POST" action="{{ route(config('cms.route_name_prefix').'.roles.destroy', $role) }}">
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
            <h2 class="fs-3 fw-light text-capitalize">
                <i class="bi bi-shield-lock"></i>
                {{ $role->name }}
            </h2>

            <div class="row gap-4 pt-3">
                <div class="col-auto">
                    <h3 class="fs-5">
                        {{ __('Details') }}
                    </h3>

                    <table class="table w-auto">
                        <tr>
                            <th>ID</th>
                            <td>{{ $role->id }}</td>
                        </tr>
                        <tr>
                            <th>{{__('Name') }}</th>
                            <td>{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Guard name') }}</th>
                            <td>{{ $role->guard_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Created at') }}</th>
                            <td>{{ $role->created_at }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Updated at') }}</th>
                            <td>{{ $role->updated_at }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-auto">
                    <h4 class="fs-5">
                        {{ __('Permissions') }}
                    </h4>

                    @forelse($role->permissions->sortby('name') as $permission)
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
            </div>
        </div>

        <div class="col-12">
            <h3 class="fs-4 fw-light">
                {{ __('Users') }}
            </h3>

            @forelse($users as $user)
                @if($loop->first)
                    <ul class="list-unstyled">
                        @endif

                        <li class="mb-1">
                            @if(! $user->email_verified_at)
                                <i class="bi bi-person-dash text-danger"></i>
                            @else
                                <i class="bi bi-person-check text-success"></i>
                            @endif

                            @can('view', $user)
                                <a href="{{ route(config('cms.route_name_prefix').'.users.show', $user) }}" class="link-dark ms-1">
                                    {{ $user->name }}
                                </a>
                            @else
                                {{ $user->name }}
                            @endcan
                        </li>
                        @if($loop->last)
                    </ul>
                @endif
            @empty
                <p class="fst-italic">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ __('No users found') }}
                </p>
            @endforelse
        </div>
    </div>
</x-cms-layout>
