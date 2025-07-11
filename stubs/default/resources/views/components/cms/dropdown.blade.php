@props([
    'align' => 'right',
    'togglerClasses' => '',
])

@php
    $alignmentClasses = match ($align) {
        'left' => '',
        'right' => 'dropdown-menu-end',
    };
@endphp

<div {{ $attributes->merge(['class' => 'dropdown']) }}>
    <a class="dropdown-toggle {{ $togglerClasses }}"
       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $trigger }}
    </a>

    <div class="dropdown-menu {{ $alignmentClasses }}">
        {{ $content }}
    </div>
</div>
