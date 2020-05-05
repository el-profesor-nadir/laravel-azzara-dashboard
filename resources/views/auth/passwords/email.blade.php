@extends('layouts.auth')

@section('content')
<div class="container container-login animated fadeIn">
    <h3 class="text-center">{{ __('Reset Password') }}</h3>
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="login-form">
            <div class="form-group form-floating-label">
                <input id="email" name="email" type="email" value="{{ old('email') }}"  autocomplete="email"
                    class="form-control input-border-bottom @error('email') is-invalid @enderror" required>

                <label for="email" class="placeholder">{{ __('E-Mail Address') }}</label>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-action mb-3">
                <button type="submit" class="btn btn-primary  btn-block">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>

            <div class="login-account">
                <span class="msg">{{ __("Don't have an account yet ") }}</span>
                <a href="{{ route('register') }}" class="link">{{ __('Register') }}</a>
            </div>

        </div>
    </form>
</div>

@endsection
