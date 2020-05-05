@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('Edit user') : {{ $user->name }}</h4>
    <ul class="breadcrumbs">
        <li class="nav-home">
            <a href="{{route('dashboard.index')}}">
                <i class="flaticon-home"></i>
            </a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="{{route('dashboard.users.index')}}">@lang('Users')</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="">@lang('Edit')</a>
        </li>
    </ul>
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">
                            @lang('Edit user')</span>
                        </div>
                        <div class="card-tools">

                        </div>
                    </div>

                </div>
                <form class="forms-sample" method="POST" action="{{ route('dashboard.users.update', $user) }}">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="card-body">
                        <h4 class="card-title">@lang('User Details')</h4>
                        <p class="card-description">
                        </p>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @lang('Please correct the errors below and try again')
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">{{ __('Name') }}*</label>
                            <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
                                value="{{ old('name', optional($user)->name) }}" placeholder="Name" required autofocus>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('E-Mail Address') }}*</label>

                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                value="{{ old('email', optional($user)->email) }}" placeholder="Email" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>

                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                name="password" placeholder="Password" >

                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>

                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                                placeholder="Confirm password">
                        </div>

                        <div class="form-group">
                            <label for="role_id">{{ __('Roles') }}*</label>

                            <select id="role_id" type="text" name="role_id"
                                class="form-control{{ $errors->has('role_id') ? ' is-invalid' : '' }}" >
                                <option value="">@lang('Choose')</option>
                                @foreach ($roles as $role)
                                    @if ($user->hasRole($role->name) || old('role_id') == $role->id)
                                        <option value="{{ $role->id }}" selected="selected">
                                            {{ $role->name }}
                                        </option>
                                    @else
                                        <option value="{{ $role->id }}">
                                            {{ $role->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                            @if ($errors->has('role_id'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('role_id') }}</strong>
                                </span>
                            @endif

                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="need_verification" id="need_verification" {{ old('need_verification') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="need_verification">@lang('Send email verification to user')</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> @lang('Update')
                        </button>
                        <a href="{{route('dashboard.users.index')}}" class="btn btn-danger">
                            <i class="fa fa-times"></i> @lang('Cancel')</a>
                    </div>
                </form>
                <div class="card-footer">
                    <p class="card-description">
                        * : @lang('required')
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
