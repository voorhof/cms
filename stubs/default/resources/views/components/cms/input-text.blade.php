@props([
    'disabled' => false,
    'isInvalid' => false,
])

<input @disabled($disabled) {{ $attributes->merge(['class' => $isInvalid ? 'is-invalid form-control' : 'form-control']) }}>
