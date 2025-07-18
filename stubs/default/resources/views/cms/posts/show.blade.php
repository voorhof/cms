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

        @canany(['manage posts', 'edit post'])
            <a class="btn btn-outline-primary btn-sm lh-sm ms-sm-auto" href="{{ route(config('cms.route_name_prefix').'.posts.edit', $post) }}">
                <i class="bi bi-pencil-square"></i> {{ __('Edit post') }}
            </a>

            @can('manage posts')
                {{-- Trigger delete post modal --}}
                <x-cms.button type="button" class="btn-outline-danger btn-sm lh-sm" data-bs-toggle="modal" data-bs-target="#deletePostModal">
                    <i class="bi bi-trash"></i> {{ __('Delete post') }}
                </x-cms.button>
            @endcan
        @endcan
    </x-slot>

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                <i class="bi bi-sticky {{ $post->published_at ? 'text-success' : 'text-danger' }}"></i>

                {{ $post->title }}
            </h2>

            <div class="row gap-4 pt-3">
                <div class="col-auto">
                    <h3 class="fs-5">
                        {{ __('Details') }}
                    </h3>

                    <table class="table w-auto">
                        <tr>
                            <th>ID</th>
                            <td>{{ $post->id }}</td>
                        </tr>
                        <tr>
                            <th>{{__('Author') }}</th>
                            <td>
                                @if($post->user->id)
                                    <a href="{{ route(config('cms.route_name_prefix').'.users.show', $post->user) }}">
                                        {{ $post->user->name }}
                                    </a>
                                @else
                                    {{ $post->user->name }}
                                @endif
                            </td>
                        </tr>

                        @if($post->published_at)
                            <tr>
                                <th>{{ __('Published by') }}</th>
                                <td>
                                    @if($post->publisher->id)
                                        <a href="{{ route(config('cms.route_name_prefix').'.users.show', $post->publisher) }}">
                                            {{ $post->publisher->name }}
                                        </a>
                                    @else
                                        {{ $post->publisher->name }}
                                    @endif
                                </td>
                            </tr>
                        @endif

                        <tr>
                            <th>{{ __('Published at') }}</th>
                            <td class="{{ $post->published_at > now() ? 'text-info' : '' }}">
                                @if($post->published_at)
                                    {{ $post->published_at }}
                                @endif

                                @canany(['manage posts', 'publish post'])
                                    {{-- Trigger publish post modal --}}
                                    @if($post->published_at)
                                        <x-cms.button type="button" class="btn-outline-dark btn-sm lh-sm d-block mt-1" data-bs-toggle="modal" data-bs-target="#publishPostModal">
                                            <i class="bi bi-box-arrow-down"></i> {{ __('Unpublish') }}
                                        </x-cms.button>
                                    @else
                                        <x-cms.button type="button" class="btn-outline-success btn-sm lh-sm" data-bs-toggle="modal" data-bs-target="#publishPostModal">
                                            <i class="bi bi-box-arrow-up"></i> {{ __('Publish') }}
                                        </x-cms.button>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created at') }}</th>
                            <td>{{ $post->created_at }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Updated at') }}</th>
                            <td>{{ $post->updated_at }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col">
                    <h3 class="fs-5">
                        {{ __('Body') }}
                    </h3>

                    <div>
                        {{ $post->body  }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
        {{-- Publish post modal--}}
        @canany(['manage posts', 'publish post'])
            <div class="modal fade" id="publishPostModal" tabindex="-1" aria-labelledby="publishPostModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form class="modal-content" method="POST" action="{{ route(config('cms.route_name_prefix').'.posts.publish', $post) }}">
                        @csrf
                        @method('PATCH')

                        <div class="modal-header">
                            <h2 class="modal-title fs-5" id="publishPostModalLabel">
                                {{ $post->published_at ? __('Unpublish post') : __('Publish post') .': ' . $post->title }}
                            </h2>

                            <x-cms.button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
                        </div>

                        <div class="modal-body">
                            @if($post->published_at)
                                {{ __('Are you sure?') }}
                            @else
                                <x-cms.input-label for="published_at" :value="__('Set publish date.')" />
                                <x-cms.input-text type="datetime-local" id="published_at" name="published_at"
                                                  :value="old('published_at') ?? now()" :isInvalid="$errors->has('published_at')"
                                                  required />
                                <x-cms.input-error :messages="$errors->get('published_at')" :defaultMessage="__('This field is required.')" />
                            @endif
                        </div>

                        <div class="modal-footer justify-content-between">
                            <x-cms.button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </x-cms.button>

                            @if($post->published_at)
                                <x-cms.button class="btn-dark">
                                    <i class="bi bi-box-arrow-down"></i> {{ __('Unpublish') }}
                                </x-cms.button>
                            @else
                                <x-cms.button class="btn-success">
                                    <i class="bi bi-box-arrow-up"></i> {{ __('Publish') }}
                                </x-cms.button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        @endcanany

        {{-- Delete post modal--}}
        @can('manage posts')
            <div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title fs-5" id="deletePostModalLabel">
                                {{ __('Delete post') .': ' . $post->title }}
                            </h2>

                            <x-cms.button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
                        </div>

                        <div class="modal-body">
                            {{ __('Are you sure?') }}
                        </div>

                        <div class="modal-footer justify-content-between">
                            <x-cms.button type="button" class="btn-secondary" data-bs-dismiss="modal">
                                {{ __('Cancel') }}
                            </x-cms.button>

                            {{-- Delete post form --}}
                            <form method="POST" action="{{ route(config('cms.route_name_prefix').'.posts.destroy', $post) }}">
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
        @endcan
    @endpush
</x-cms-layout>
