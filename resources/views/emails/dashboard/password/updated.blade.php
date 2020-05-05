@component('mail::message')
# @lang('Password updated')

@lang("We're letting you know that your password was updated")

<br>
<br>

@component('mail::button', ['url' => route('login')])
Login
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
