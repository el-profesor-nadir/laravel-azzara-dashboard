@extends('layouts.auth')

@section('content')
<div class="container container-signup animated fadeIn">
    <h3 class="text-center">{{ __('Reset Password') }}</h3>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="login-form">

            <div class="form-group form-floating-label">
                <input  id="email" name="email" type="email" autocomplete="email" value="{{ $email ?? old('email') }}"
                    class="form-control input-border-bottom @error('email') is-invalid @enderror" required>
                <label for="email" class="placeholder">{{ __('E-Mail Address') }}</label>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group form-floating-label">
                <input  id="password" name="password" type="password" autocomplete="new-password"
                    class="form-control input-border-bottom @error('password') is-invalid @enderror" required>
                <label for="password" class="placeholder">{{ __('Password') }}</label>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <div class="show-password">
                    <i class="flaticon-interface"></i>
                </div>
            </div>

            <div class="form-group form-floating-label">
                <input  id="password-confirm" name="password_confirmation" type="password" autocomplete="new-password"
                    class="form-control input-border-bottom" required>
                <label for="password-confirm" class="placeholder">{{ __('Confirm Password') }}</label>
                <div class="show-password">
                    <i class="flaticon-interface"></i>
                </div>
            </div>

            <div class="form-action">
                <button type="submit" class="btn btn-primary btn-login">
                    {{ __('Reset Password') }}
                </button>
            </div>

        </div>
    </form>
</div>

@endsection
