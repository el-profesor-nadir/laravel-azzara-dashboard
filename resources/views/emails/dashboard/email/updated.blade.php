@component('mail::message')
# @lang('Email updated')

@lang("We're letting you know that your email was updated")

<br>
<br>

@component('mail::button', ['url' => route('login')])
Login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

