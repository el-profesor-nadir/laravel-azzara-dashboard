@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('Password Update')</h4>
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
            <a href="">@lang('Password Update')</a>
        </li>
    </ul>
</div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">
                            <span>@lang('Change Password')</span>
                        </div>
                        <div class="card-tools">

                        </div>
                    </div>

                </div>
                <form class="forms-sample" method="POST" action="{{ route('dashboard.password.store') }}">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @lang('Please correct the errors below and try again')
                            </div>
                        @endif

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group form-group-default">
                                    <label for="password_current" class="col-form-label">@lang('Current Password')*</label>
                                    <input type="password" id="password_current" name="password_current" required
                                        class="form-control{{ $errors->has('password_current') ? ' is-invalid' : '' }}" >
                                    @if ($errors->has('password_current'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password_current') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label for="password" class="col-form-label">@lang('New Password')*</label>
                                    <input type="password" id="password" name="password" required
                                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" >
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label for="password_confirmation" class="col-form-label">@lang('New Password Confirmation')*</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                        class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" >
                                    @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> @lang('Save')
                        </button>
                        <a href="{{route('dashboard.index')}}" class="btn btn-danger">
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
        <div class="col-md-4">
            <div class="card card-profile card-secondary">
                <div class="card-header" style="background-image: url('{{asset('templates/dashboard/assets/img/blogpost.jpg')}}')">
                    <div class="profile-picture">
                        <div class="avatar avatar-xl">
                            <img src="{{asset('templates/dashboard/assets/img/profile.jpg')}}" alt="..." class="avatar-img rounded-circle">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="user-profile text-center">
                        <div class="name">Hizrian, 19</div>
                        <div class="job">Frontend Developer</div>
                        <div class="desc">A man who hates loneliness</div>
                        <div class="social-media">
                            <a class="btn btn-info btn-twitter btn-sm btn-link" href="#">
                                <span class="btn-label just-icon"><i class="flaticon-twitter"></i> </span>
                            </a>
                            <a class="btn btn-danger btn-sm btn-link" rel="publisher" href="#">
                                <span class="btn-label just-icon"><i class="flaticon-google-plus"></i> </span>
                            </a>
                            <a class="btn btn-primary btn-sm btn-link" rel="publisher" href="#">
                                <span class="btn-label just-icon"><i class="flaticon-facebook"></i> </span>
                            </a>
                            <a class="btn btn-danger btn-sm btn-link" rel="publisher" href="#">
                                <span class="btn-label just-icon"><i class="flaticon-dribbble"></i> </span>
                            </a>
                        </div>
                        <div class="view-profile">
                            <a href="#" class="btn btn-secondary btn-block">View Full Profile</a>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row user-stats text-center">
                        <div class="col">
                            <div class="number">125</div>
                            <div class="title">Post</div>
                        </div>
                        <div class="col">
                            <div class="number">25K</div>
                            <div class="title">Followers</div>
                        </div>
                        <div class="col">
                            <div class="number">134</div>
                            <div class="title">Following</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
