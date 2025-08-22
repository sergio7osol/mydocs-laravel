@php($url = route('documents.index'))

@component('mail::message')
# Welcome to MyDocs

Hi {{ $user->first_name ?? $user->name ?? 'there' }},

Your account has been created successfully. We're glad to have you on board!

@component('mail::panel')
**Name:** {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: ($user->name ?? 'New user') }}  
**Email:** {{ $user->email }}
@endcomponent

@component('mail::button', ['url' => $url])
Go to MyDocs
@endcomponent

Thanks,  
{{ config('app.name') }}

@component('mail::subcopy')
If you're having trouble clicking the "Go to MyDocs" button, copy and paste this URL into your web browser:  
{{ $url }}
@endcomponent
@endcomponent
