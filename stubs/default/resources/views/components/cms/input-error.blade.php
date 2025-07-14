{{--
Messages come from server-side validation.
The default message is for client-side form validation,
this would be displayed on the first form submission.
--}}
@props([
    'messages',
    'defaultMessage' => null,
])

@if($messages)
    @foreach ((array) $messages as $message)
        <div {{ $attributes->merge(['class' => 'invalid-feedback']) }}>{{ $message }}</div>
    @endforeach
@elseif($defaultMessage)
    <div {{ $attributes->merge(['class' => 'invalid-feedback']) }}>{{ $defaultMessage }}</div>
@endif
