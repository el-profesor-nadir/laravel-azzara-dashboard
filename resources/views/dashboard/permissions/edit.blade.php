@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('Edit permission') : {{ $permission->name }}</h4>
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
            <a href="{{route('dashboard.permissions.index')}}">@lang('Permissions')</a>
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
                            @lang('Edit permission')</span>
                        </div>
                        <div class="card-tools">

                        </div>
                    </div>

                </div>
                <form class="forms-sample" method="POST" action="{{ route('dashboard.permissions.update', $permission) }}">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="card-body">
                        <h4 class="card-title">@lang('Permission Details')</h4>
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
                                value="{{ old('name', optional($permission)->name) }}" placeholder="Name" required autofocus>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>


                        @if(!$roles->isEmpty())
                            <div class='form-group'>
                                <label>{{ __('Assign Roles') }}*</label>
                                @foreach ($roles as $role)

                                    <div class="custom-control custom-checkbox d-block">
                                        <input class="custom-control-input {{ $errors->has('roles') ? ' is-invalid' : '' }}" type="checkbox" value="{{ $role->id }}"
                                            name="roles[]" id="{{ $role->name }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : ($permission->hasRole($role->name) ? 'checked' : '') }}>
                                        <label class="custom-control-label" for="{{ $role->name }}">{{ ucfirst($role->name) }}</label>
                                    </div>

                                @endforeach
                                @if ($errors->has('roles'))
                                    <div class="alert alert-danger mt-2">
                                        <ul>
                                            <li>{{ $errors->first('roles') }}</li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif



                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> @lang('Update')
                        </button>
                        <a href="{{route('dashboard.permissions.index')}}" class="btn btn-danger">
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
