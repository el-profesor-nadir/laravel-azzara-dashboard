@extends('layouts.auth')

@section('content')
<div class="container container-login animated fadeIn">
    <h3 class="text-center">{{ __('Login') }}</h3>
    <form method="POST" action="{{ route('login') }}">
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

            <div class="form-group form-floating-label">
                <input id="password" name="password" type="password" autocomplete="current-password"
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

            <div class="row form-sub m-0">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link float-right">{{ __('Forgot Your Password?') }}</a>
                @endif
            </div>

            <div class="form-action mb-3">
                <button type="submit" class="btn btn-primary btn-login">
                    {{ __('Login') }}
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
