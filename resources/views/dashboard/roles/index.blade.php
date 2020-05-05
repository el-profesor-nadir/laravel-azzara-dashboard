@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('Roles')</h4>
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
                            @lang('Roles') <span class="badge badge-pill badge-primary">( {{$roles->count()}}/{{$roles->total()}} )</span>
                        </div>
                        <div class="card-tools">
                            @can('Add roles')
                                <a href="{{ route('dashboard.roles.create') }}" class="btn btn-info btn-border btn-round btn-sm mr-2">
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
                                    <th>@lang('Date Creation')</th>
                                    <th>@lang('Permissions')</th>
                                    @if(auth()->user()->can('Edit roles') || auth()->user()->can('Delete roles'))
                                        <th scope="col">Operations</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <th scope="row">{{ $role->id }}</th>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->created_at->format('d/m/Y')}}</td>
                                        <td>{{ $role->permissions->pluck('name')->implode(' | ') }}</td>
                                        @if(auth()->user()->can('Edit roles') || auth()->user()->can('Delete roles'))
                                            <td>
                                                <div class="btn-group">
                                                    @can('Edit roles')
                                                        <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-info">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('Delete roles')
                                                        <button type="button" class="btn btn-danger" data-toggle="modal"
                                                            data-target="#delete" data-form-id="delete-role-{{$role->id}}"
                                                            data-element="{{ $role->name }}">
                                                            <i class="fa fa-minus-circle"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                                @can('Delete roles')
                                                    <form action="{{route('dashboard.roles.destroy', $role)}}" method="post" style="display: none;"
                                                        id="delete-role-{{$role->id}}">
                                                        @csrf
                                                        {{ method_field('DELETE') }}
                                                    </form>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <p class="mb-0 p-3">@lang('There is no roles')</p>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                      {{ $roles->links() }}
                    </ul>
                  </div>
            </div>
        </div>
    </div>
@endsection
