<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-people me-1"></i>
            {{ __('Users') }}
        </h1>
    </x-slot>

    <x-slot name="actionButtons">
        <a class="btn btn-sm lh-sm" href="{{ route(config('cms.route_name_prefix').'.users.index') }}">
            <i class="bi bi-arrow-left"></i> {{ __('All users') }}
        </a>
    </x-slot>

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('Edit user') }}
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <form method="POST" action="{{ route(config('cms.route_name_prefix').'.users.update', $user) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('patch')

                        <h3 class="fs-5 pt-2">
                            {{ __('Details') }}
                        </h3>

                        {{-- Name --}}
                        <div class="mb-3">
                            <x-cms.input-label for="name" :value="__('Name')" />
                            <x-cms.input-text type="text" id="name" name="name"
                                              :value="old('name') ?? $user->name" :isInvalid="$errors->has('name')"
                                              required />
                            <x-cms.input-error :messages="$errors->get('name')" :defaultMessage="__('This field is required.')" />
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <x-cms.input-label for="email" :value="__('Email')" />
                            <x-cms.input-text type="email" id="email" name="email"
                                              :value="old('email') ?? $user->email" :isInvalid="$errors->has('email')"
                                              required />
                            <x-cms.input-error :messages="$errors->get('email')" :defaultMessage="__('This field must be a valid email address.')" />
                        </div>

                        {{-- Role select --}}
                        @can('manage roles')
                            <h3 class="fs-5">
                                {{ __('Role') }}
                            </h3>

                            <div class="mb-3">
                                @if($user->hasRole(config('cms.secured_roles')))
                                    <p class="fw-bold fst-italic">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ __('Secured role') }}
                                    </p>
                                @else
                                    <x-cms.input-label for="role" :value="__('Role')" />
                                    <select name="role" id="role" class="form-select text-capitalize {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                                        <option value="" selected disabled>{{ __('Choose') }}...</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" {{ old('role') || ($user->roles()->first() && $user->roles()->first()->name === $role) ? 'selected': '' }}>{{ $role }}</option>
                                        @endforeach
                                    </select>
                                    <x-cms.input-error :messages="$errors->get('role')" :defaultMessage="__('This field is required.')" />
                                @endif
                            </div>
                        @endcan

                        {{-- Submit --}}
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between py-1">
                            <x-cms.button class="btn-primary">
                                <i class="bi bi-save"></i> {{ __('Save') }}
                            </x-cms.button>

                            <a href="{{ route(config('cms.route_name_prefix').'.users.show', $user) }}" class="btn btn-dark">
                                <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-cms-layout>
