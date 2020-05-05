<!-- Sidebar -->
<div class="sidebar">

    <div class="sidebar-background"></div>
    <div class="sidebar-wrapper scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left mr-2">
                    @if ( auth()->user()->avatar )
                        <img  alt="{{ auth()->user()->first_name }} {{ auth()->user()->last_name}}" class="avatar-img rounded-circle"
                            src="{{ optional(auth()->user()->avatar)->path() }}" width="128" height="128" id="img">
                    @else
                        <img  alt="{{ auth()->user()->first_name }} {{ auth()->user()->last_name}}" class="avatar-img rounded-circle"
                            src="{{  config('image.image_no_available_url') }}" width="128" height="128" id="img">
                    @endif
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{ Auth::user()->name }}
                            <span class="user-level">{{ Auth::user()->roles->pluck('name')->implode(' ') }}</span>
                            <span class="caret"></span>
                        </span>
                    </a>
                    <div class="clearfix"></div>

                    <div class="collapse in" id="collapseExample">
                        <ul class="nav">
                            <li>
                                <a href="{{ route('dashboard.profile.index') }}">
                                    <span class="link-collapse">My Profile</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('dashboard.password.index') }}">
                                    <span class="link-collapse">@lang('Edit Password')</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('dashboard.email.index') }}">
                                    <span class="link-collapse">@lang('Edit Email')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <ul class="nav">
                @can('View dashboard')
                <li class="nav-item {{ return_if(on_page('dashboard'), 'active') }}">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>@lang('Dashboard')</p>
                    </a>
                </li>
                @endcan
                @can('View users')
                <li class="nav-item {{ return_if(on_page('dashboard/users')||on_page('dashboard/users/*'), 'active') }}">
                    <a href="{{ route('dashboard.users.index') }}">
                        <i class="fas fa-users"></i>
                        <p>@lang('Users')</p>
                    </a>
                </li>
                @endcan
                @can('View permissions')
                <li class="nav-item {{ return_if(on_page('dashboard/permissions')||on_page('dashboard/permissions/*'), 'active') }}">
                    <a href="{{ route('dashboard.permissions.index') }}">
                        <i class="fas fa-user-shield"></i>
                        <p>@lang('Permissions')</p>
                    </a>
                </li>
                @endcan
                @can('View roles')
                <li class="nav-item {{ return_if(on_page('dashboard/roles')||on_page('dashboard/roles/*'), 'active') }}">
                    <a href="{{ route('dashboard.roles.index') }}">
                        <i class="fas fa-user-secret"></i>
                        <p>@lang('Roles')</p>
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
