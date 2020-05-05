@extends('layouts.dashboard')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card text-center">
                <div class="card-header bg-warning">
                    <div class="card-title text-white">{{ __('Verify Your Email Address') }}</div>
                </div>
                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif
                    <p>
                        {{ __('Before proceeding, please check your email for a verification link.') }}
                        {{ __('If you did not receive the email') }},
                    </p>
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-warning">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
