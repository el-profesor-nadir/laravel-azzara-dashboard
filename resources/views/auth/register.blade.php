@extends('layouts.auth')

@section('content')

<div class="container container-signup animated fadeIn">
    <h3 class="text-center">{{ __('Register') }}</h3>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="login-form">

            <div class="form-group form-floating-label">
                <input  id="name" name="name" type="text" autocomplete="name"
                    class="form-control input-border-bottom @error('name') is-invalid @enderror" required>
                <label for="name" class="placeholder">{{ __('Name') }}</label>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group form-floating-label">
                <input  id="email" name="email" type="email" autocomplete="email"
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

            <div class="row form-sub m-0">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="agree" id="agree" required>
                    <label class="custom-control-label" for="agree">I Agree the terms and conditions.</label>
                </div>
            </div>

            <div class="form-action">
                <button type="submit" class="btn btn-primary btn-login">
                    {{ __('Register') }}
                </button>
            </div>

            <div class="login-account">
                <span class="msg">{{ __("I already have a account") }}</span>
                <a href="{{ route('login') }}"  class="link">{{ __('Login') }}</a>
            </div>
        </div>
    </form>
</div>

@endsection
