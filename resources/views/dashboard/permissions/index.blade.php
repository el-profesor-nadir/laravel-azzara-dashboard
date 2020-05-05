@extends('layouts.dashboard')

@section('page-header')
<div class="page-header">
    <h4 class="page-title">@lang('Permissions')</h4>
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
                            @lang('Permissions') <span class="badge badge-pill badge-primary">( {{$permissions->count()}}/{{$permissions->total()}} )</span>
                        </div>
                        <div class="card-tools">
                            @can('Add permissions')
                                <a href="{{ route('dashboard.permissions.create') }}" class="btn btn-info btn-border btn-round btn-sm mr-2">
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
                                    <th>@lang('Roles')</th>
                                    @if(auth()->user()->can('Edit permissions') || auth()->user()->can('Delete permissions'))
                                        <th scope="col">Operations</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($permissions as $permission)
                                    <tr>
                                        <th scope="row">{{ $permission->id }}</th>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->created_at->format('d/m/Y')}}</td>
                                        <td>{{ $permission->roles->pluck('name')->implode(' | ') }}</td>
                                        @if(auth()->user()->can('Edit permissions') || auth()->user()->can('Delete permissions'))
                                            <td>
                                                <div class="btn-group">
                                                    @can('Edit permissions')
                                                        <a href="{{ route('dashboard.permissions.edit', $permission) }}" class="btn btn-info">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('Delete permissions')
                                                        <button type="button" class="btn btn-danger" data-toggle="modal"
                                                            data-target="#delete" data-form-id="delete-permission-{{$permission->id}}"
                                                            data-element="{{ $permission->name }}">
                                                            <i class="fa fa-minus-circle"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                                @can('Delete permissions')
                                                    <form action="{{route('dashboard.permissions.destroy', $permission)}}" method="post" style="display: none;"
                                                        id="delete-permission-{{$permission->id}}">
                                                        @csrf
                                                        {{ method_field('DELETE') }}
                                                    </form>
                                                @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <p class="mb-0 p-3">@lang('There is no permissions')</p>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                      {{ $permissions->links() }}
                    </ul>
                  </div>
            </div>
        </div>
    </div>
@endsection
