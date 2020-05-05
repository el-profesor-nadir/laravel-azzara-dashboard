@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('My Profile')</h4>
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
            <a href="">@lang('My Profile')</a>
        </li>
    </ul>
</div>
@endsection

@section('content')
<form class="forms-sample" method="POST" action="{{ route('dashboard.users.profile.update',$user) }}" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    @lang('Please correct the errors below and try again')
                </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-8 order-2 order-md-1">
            <div class="card">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">
                            <span>@lang('Profile Details')</span>
                        </div>
                        <div class="card-tools">

                        </div>
                    </div>

                </div>
                <div class="card-body">

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label for="name">@lang('Name')*</label>
                                <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" required
                                    name="name" id="name" placeholder="@lang('UserName')" value="{{ old('name', $user->name) }}">
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label for="first_name">@lang('First name')*</label>
                                <input type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" placeholder="@lang('Your first name')"
                                    id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                @if ($errors->has('first_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label for="last_name">@lang('Last name')*</label>
                                <input type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" placeholder="@lang('Your last name')"
                                    id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                @if ($errors->has('last_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label for="address">@lang('Address')*</label>
                                <textarea class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" placeholder="@lang('Your address')"
                                    id="address" name="address" rows="3" required>{{ old('address', $user->address) }}</textarea>
                                @if ($errors->has('address'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label for="country">@lang('Country')*</label>
                                <select class="form-control {{ $errors->has('country') ? ' is-invalid' : '' }}"
                                    id="country" name="country" required>
                                    <option value="Morocco" {{ old('country', $user->country)==="Morocco" ? 'selected="selected"' : '' }}>@lang('Morocco')</option>
                                </select>
                                @if ($errors->has('country'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label for="city">@lang('City')*</label>
                                <input type="text" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" required
                                    name="city" id="city" placeholder="@lang('City')" value="{{ old('city', $user->city) }}">
                                @if ($errors->has('city'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group form-group-default">
                                <label for="zip_code">@lang('Zip Code')*</label>
                                <input type="text" class="form-control{{ $errors->has('zip_code') ? ' is-invalid' : '' }}" required
                                    name="zip_code" id="zip_code" placeholder="@lang('Zip Code')" value="{{ old('zip_code', $user->zip_code) }}">
                                @if ($errors->has('zip_code'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('zip_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label for="gender">@lang('Genre')*</label>
                                <select class="form-control {{ $errors->has('gender') ? ' is-invalid' : '' }}"
                                    id="gender" name="gender" required>
                                    <option value="">@lang('Choose')</option>
                                    <option value="female" {{ old('gender', $user->gender)==="female" ? 'selected="selected"' : '' }}>@lang('Female')</option>
                                    <option value="male"   {{ old('gender', $user->gender)==="male"   ? 'selected="selected"' : '' }}>@lang('Male')</option>
                                </select>
                                @if ($errors->has('gender'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label for="phone">@lang('Phone')*</label>
                                <input type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="@lang('ex : 0607553655')"
                                    id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                @if ($errors->has('phone'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-action">
                    @can('Edit users profile')
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> @lang('Save')
                        </button>
                    @endcan
                    <a href="{{route('dashboard.index')}}" class="btn btn-danger">
                        <i class="fa fa-times"></i> @lang('Cancel')</a>
                </div>
                <div class="card-footer">
                    <p class="card-description">
                        * : @lang('required')
                    </p>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 order-1 order-md-2">
            <div class="card">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">
                            <span>@lang('Profile Avatar')</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="input-file input-file-image text-center">
                                @if ( $user->avatar )
                                    <img  alt="{{ $user->first_name }} {{ $user->last_name}}" class="img-upload-preview img-circle mx-auto"
                                        src="{{ optional($user->avatar)->path() }}" width="128" height="128" id="img"  >
                                @else
                                    <img  alt="{{ $user->first_name }} {{ $user->last_name}}" class="img-upload-preview img-circle mx-auto"
                                        src="{{  config('image.image_no_available_url') }}" width="128" height="128" id="img"  >
                                @endif
                                <input type="file" class="form-control form-control-file{{ $errors->has('avatar') ? ' is-invalid' : '' }}"
                                    id="avatar" name="avatar" accept="image/*">
                                <label for="avatar" class="  label-input-file btn btn-default btn-round">
                                    <span class="btn-label">
                                        <i class="fa fa-file-image"></i>
                                    </span>
                                    @lang('Add avatar')
                                </label>
                                @if ($errors->has('avatar'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('avatar') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

@endsection
