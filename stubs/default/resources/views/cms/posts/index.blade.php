@php use App\Models\Post; @endphp
<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-stickies me-1"></i>
            {{ __('Posts') }}
        </h1>
    </x-slot>

    @canany(['create', 'viewTrash'], Post::class)
        <x-slot name="actionButtons">
            @can('create', Post::class)
                <a class="btn btn-outline-primary btn-sm lh-sm"
                   href="{{ route(config('cms.route_name_prefix').'.posts.create') }}">
                    <i class="bi bi-plus-circle"></i> {{ __('New post') }}
                </a>
            @endcan

            @can('viewTrash', Post::class)
                <a class="btn btn-outline-secondary btn-sm lh-sm ms-sm-auto"
                   href="{{ route(config('cms.route_name_prefix').'.posts.viewTrash') }}">
                    <i class="bi bi-trash"></i> {{ __('Trash') }}
                    <span class="{{ $postsTrashCount > 0 ? 'fw-bold' : 'fw-light' }}">
                        {{ '(' . $postsTrashCount . ')' }}
                    </span>
                </a>
            @endcan
        </x-slot>
    @endcanany

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('All posts') }}
            </h2>

            @forelse($posts as $post)
                @if($loop->first)
                    <ul class="list-unstyled">
                        @endif

                        <li class="mb-1">
                            <a class="icon-link link-dark link-underline-opacity-25 link-underline-opacity-100-hover"
                               href="{{ route(config('cms.route_name_prefix').'.posts.show', $post) }}">
                                <i class="bi bi-sticky {{ $post->published_at ? 'text-success' : 'text-danger' }}"></i>
                                {{ $post->title }}
                                <div class="vr"></div>
                                <em class="small">{{ $post->user->name }}</em>
                            </a>
                        </li>

                        @if($loop->last)
                    </ul>
                @endif
            @empty
                <p class="fw-bold fst-italic">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ __('No posts found') }}
                </p>
            @endforelse
        </div>
    </div>
</x-cms-layout>
