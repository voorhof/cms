<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-people me-1"></i>
            {{ __('Users') }}
        </h1>
    </x-slot>

    <x-slot name="actionButtons">
        <a class="btn btn-sm lh-sm" href="{{ route('cms.users.index') }}">
            <i class="bi bi-arrow-left"></i> {{ __('All users') }}
        </a>

        @can('manage users')
            {{-- Trash all users --}}
            <form method="POST" action="{{ route('cms.users.emptyTrash') }}" class="ms-auto">
                @csrf
                @method('DELETE')
                <x-cms.button class="btn-outline-danger btn-sm lh-sm ms-auto" :disabled="$users->count() < 1">
                    <i class="bi bi-trash-fill"></i> {{ __('Empty trash') }}
                </x-cms.button>
            </form>
        @endcan
    </x-slot>

    {{-- $slot --}}
    <div class="row">
        <div class="col-12">
            <h2 class="fs-3 fw-light">
                {{ __('Deleted users') }}
            </h2>

            @forelse($users as $user)
                @if($loop->first)
                    <ul class="list-unstyled">
                        @endif

                        <li class="d-flex align-items-center gap-2 mb-2">
                            {{-- Delete form --}}
                            <form method="POST" action="{{ route('cms.users.delete', $user) }}">
                                @csrf
                                @method('DELETE')
                                <x-cms.button class="btn-danger btn-sm lh-sm">
                                    <i class="bi bi-trash"></i> {{ __('Delete') }}
                                </x-cms.button>
                            </form>

                            {{-- Restore form --}}
                            <form method="POST" action="{{ route('cms.users.restore', $user) }}">
                                @csrf
                                @method('PATCH')
                                <x-cms.button class="btn-warning btn-sm lh-sm">
                                    <i class="bi bi-arrow-counterclockwise"></i> {{ __('Restore') }}
                                </x-cms.button>
                            </form>

                            {{-- User name --}}
                            <strong class="fs-5">{{ $user->name }}</strong>
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
