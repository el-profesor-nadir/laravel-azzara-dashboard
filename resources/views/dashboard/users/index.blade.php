@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('Users')</h4>
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
                            @lang('Users') <span class="badge badge-pill badge-primary">( {{$users->count()}}/{{$users->total()}} )</span>
                        </div>
                        <div class="card-tools">
                            @can('Add users')
                                <a href="{{ route('dashboard.users.create') }}" class="btn btn-info btn-border btn-round btn-sm mr-2">
                                    <span class="btn-label"><i class="fa fa-plus"></i></span> @lang('New')
                                </a>
                            @endcan
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-head-bg-primary">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Email')</th>
                                    <th>@lang('Date Creation')</th>
                                    <th>@lang('Roles')</th>
                                    <th>@lang('Status')</th>
                                    @if(auth()->user()->can('Edit users') || auth()->user()->can('Delete users') || auth()->user()->can('Edit profile users'))
                                        <th scope="col">Operations</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <th scope="row">{{ $user->id }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('d/m/Y')}}</td>
                                        <td>{{ $user->roles->pluck('name')->implode(' ') }}</td>
                                        <td>
                                            @if ($user->hasVerifiedEmail())
                                                <span class="badge bg-success text-white">@lang('verified')</span>
                                            @else
                                                <span class="badge bg-danger text-white">@lang('unverified')</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->can('Edit users') || auth()->user()->can('Delete users') || auth()->user()->can('Edit profile users'))
                                            <td>
                                                <div class="btn-group">
                                                    @can('Edit users')
                                                        <a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-info">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('View users profile')
                                                        <a href="{{ route('dashboard.users.profile.edit', $user) }}" class="btn btn-primary">
                                                            <i class="fas fa-user"></i>
                                                        </a>
                                                    @endcan
                                                    @can('Delete users')
                                                        <button type="button" class="btn btn-danger" data-toggle="modal"
                                                            data-target="#delete" data-form-id="delete-user-{{$user->id}}"
                                                            data-element="{{ $user->name }} : {{ $user->email }}">
                                                            <i class="fa fa-minus-circle"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                                @can('Delete users')
                                                    <form action="{{route('dashboard.users.destroy', $user)}}" method="post" style="display: none;"
                                                        id="delete-user-{{$user->id}}">
                                                        @csrf
                                                        {{ method_field('DELETE') }}
                                                    </form>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <p class="mb-0 p-3">@lang('There is no users')</p>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                      {{ $users->links() }}
                    </ul>
                  </div>
            </div>
        </div>
    </div>
@endsection
