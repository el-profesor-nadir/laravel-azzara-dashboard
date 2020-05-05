@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('Create new role')</h4>
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
            <a href="{{route('dashboard.roles.index')}}">@lang('Roles')</a>
        </li>
        <li class="separator">
            <i class="flaticon-right-arrow"></i>
        </li>
        <li class="nav-item">
            <a href="">@lang('New')</a>
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
                            @lang('Create new role')</span>
                        </div>
                        <div class="card-tools">

                        </div>
                    </div>

                </div>
                <form class="forms-sample" method="POST" action="{{ route('dashboard.roles.store') }}">
                    @csrf
                    <div class="card-body">
                        <h4 class="card-title">@lang('Role Details')</h4>
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
                                value="{{ old('name') }}" placeholder="Name" required autofocus>
                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        @if(!$permissions->isEmpty())
                            <div class='form-group'>
                                <label>{{ __('Assign Permissions') }}*</label>
                                @foreach ($permissions as $permission)

                                    <div class="custom-control custom-checkbox d-block">
                                        <input class="custom-control-input {{ $errors->has('permissions') ? ' is-invalid' : '' }}" type="checkbox" value="{{ $permission->id }}"
                                            name="permissions[]" id="{{ $permission->name }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="{{ $permission->name }}">{{ ucfirst($permission->name) }}</label>
                                    </div>

                                @endforeach
                                @if ($errors->has('permissions'))
                                    <div class="alert alert-danger mt-2">
                                        <ul>
                                            <li>{{ $errors->first('permissions') }}</li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> @lang('Save')
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

@section('scripts')
    <script>

    </script>
@endsection
