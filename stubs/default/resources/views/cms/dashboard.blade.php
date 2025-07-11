<x-cms-layout>
    <x-slot name="header">
        <h1 class="fs-2 text-center mb-0">
            <i class="bi bi-speedometer2 me-1"></i>
            {{ config('app.name', 'Laravel') }} {{ __('Dashboard') }}
        </h1>
    </x-slot>

    <x-slot name="actionButtons">
        <div>
            <button class="btn btn-primary btn-sm lh-sm">
                <i class="bi bi-question-circle"></i> {{ __('Test button') }}
            </button>
        </div>
    </x-slot>

    <div>
        {{ __("Welcome to your CMS dashboard!") }}
    </div>
</x-cms-layout>
