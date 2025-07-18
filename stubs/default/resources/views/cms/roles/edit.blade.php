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
    </x-slot>

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('Edit role') }}
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <form method="POST" action="{{ route(config('cms.route_name_prefix').'.roles.update', $role) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('patch')

                        <h3 class="fs-5 pt-2">
                            {{ __('Details') }}
                        </h3>

                        {{-- Name --}}
                        <div class="mb-3">
                            <x-cms.input-label for="name" :value="__('Name')" />
                            <x-cms.input-text type="text" id="name" name="name"
                                              :value="old('name') ?? $role->name" :isInvalid="$errors->has('name')"
                                              required maxlength="32" :readonly="in_array($role->name, config('cms.secured_roles'))"/>
                            <x-cms.input-error :messages="$errors->get('name')" :defaultMessage="__('This field is required.')" />
                        </div>

                        <h3 class="fs-5">
                            {{ __('Permissions') }}
                        </h3>

                        {{-- Permissions --}}
                        <div class="mb-3">
                            @forelse($permissions as $permission)
                                <div class="form-check mb-1">
                                    <input class="form-check-input {{ $errors->has('permissions.*') ? 'is-invalid' : '' }}"
                                           type="checkbox" value="{{ $permission }}"
                                           name="permissions[]" id="permission{{ $loop->index }}"
                                           {{ (is_array(old('permissions')) && in_array($permission, old('permissions')) || $role->hasPermissionTo($permission)) ? 'checked' : '' }}
                                           {{ in_array($role->name, config('cms.secured_roles')) ? 'disabled' : '' }}>
                                    <label class="form-check-label text-capitalize" for="permission{{ $loop->index }}">
                                        {{ $permission }}
                                    </label>

                                    @if($loop->last)
                                        <x-cms.input-error :messages="$errors->first('permissions.*')" :defaultMessage="__('This field is required.')" />
                                    @endif
                                </div>
                            @empty
                                <p class="fst-italic">
                                    <i class="bi bi-exclamation-circle"></i> {{ __('No permissions found') }}
                                </p>
                            @endforelse
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between py-1">
                            <x-cms.button class="btn-primary">
                                <i class="bi bi-save"></i> {{ __('Save') }}
                            </x-cms.button>

                            <a href="{{ route(config('cms.route_name_prefix').'.roles.show', $role) }}" class="btn btn-dark">
                                <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-cms-layout>
