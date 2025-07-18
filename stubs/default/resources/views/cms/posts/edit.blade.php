<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-stickies me-1"></i>
            {{ __('Posts') }}
        </h1>
    </x-slot>

    <x-slot name="actionButtons">
        <a class="btn btn-sm lh-sm" href="{{ route(config('cms.route_name_prefix').'.posts.index') }}">
            <i class="bi bi-arrow-left"></i> {{ __('All posts') }}
        </a>
    </x-slot>

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('Edit post') }}
            </h2>

            <div class="row">
                <div class="col-md-6">
                    <form method="POST" action="{{ route(config('cms.route_name_prefix').'.posts.update', $post) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('patch')

                        <h3 class="fs-5 pt-2">
                            {{ __('Details') }}
                        </h3>

                        {{-- Title --}}
                        <div class="mb-3">
                            <x-cms.input-label for="title" :value="__('Title')" />
                            <x-cms.input-text type="text" id="title" name="title"
                                              :value="old('title') ?? $post->title" :isInvalid="$errors->has('title')"
                                              required />
                            <x-cms.input-error :messages="$errors->get('title')" :defaultMessage="__('This field is required.')" />
                        </div>

                        {{-- Body --}}
                        <div class="mb-3">
                            <x-cms.input-label for="body" :value="__('Body')" />
                            <textarea class="form-control {{ $errors->has('body') ? 'is-invalid' : '' }}"
                                      id="body" name="body"
                                      rows="5"
                                      required>{{ old('body') ?? $post->body }}</textarea>
                            <x-cms.input-error :messages="$errors->get('body')" :defaultMessage="__('This field is required.')" />
                        </div>

                        {{-- Submit --}}
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between py-1">
                            <x-cms.button class="btn-primary">
                                <i class="bi bi-save"></i> {{ __('Save') }}
                            </x-cms.button>

                            <a href="{{ route(config('cms.route_name_prefix').'.posts.show', $post) }}" class="btn btn-dark">
                                <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-cms-layout>
