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

        @can('manage posts')
            {{-- Trash all posts --}}
            <form method="POST" action="{{ route(config('cms.route_name_prefix').'.posts.emptyTrash') }}" class="ms-auto">
                @csrf
                @method('DELETE')
                <x-cms.button class="btn-outline-danger btn-sm lh-sm ms-auto" :disabled="$posts->count() < 1">
                    <i class="bi bi-trash-fill"></i> {{ __('Empty trash') }}
                </x-cms.button>
            </form>
        @endcan
    </x-slot>

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('Deleted posts') }}
            </h2>

            @forelse($posts as $post)
                @if($loop->first)
                    <ul class="list-unstyled">
                        @endif

                        <li class="d-flex align-items-center gap-2 mb-2">
                            {{-- Delete form --}}
                            <form method="POST" action="{{ route(config('cms.route_name_prefix').'.posts.delete', $post) }}">
                                @csrf
                                @method('DELETE')
                                <x-cms.button class="btn-danger btn-sm lh-sm">
                                    <i class="bi bi-trash"></i> {{ __('Delete') }}
                                </x-cms.button>
                            </form>

                            {{-- Restore form --}}
                            <form method="POST" action="{{ route(config('cms.route_name_prefix').'.posts.restore', $post) }}">
                                @csrf
                                @method('PATCH')
                                <x-cms.button class="btn-warning btn-sm lh-sm">
                                    <i class="bi bi-arrow-counterclockwise"></i> {{ __('Restore') }}
                                </x-cms.button>
                            </form>

                            {{-- Post title --}}
                            <strong class="fs-5">
                                {{ $post->title }}
                                <div class="vr"></div>
                                <em class="small">{{ $post->user->name }}</em>
                            </strong>
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
