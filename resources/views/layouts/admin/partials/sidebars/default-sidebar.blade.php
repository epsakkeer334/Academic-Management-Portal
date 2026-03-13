<!-- Sidebar -->
@php
    $user = Auth::user();
    $dashboardRoute = match ($user->role ?? '') {
        'super-admin' => 'admin.dashboard',
        default       => 'admin.dashboard', // fallback
    };
@endphp

<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('admin/assets/img/small_logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 65%;">
        </a>

        <a href="{{ route($dashboardRoute) }}" class="logo-small">
            <img src="{{ asset('admin/assets/img/small_logo.png') }}" alt="Logo">
        </a>
        <a href="{{ route($dashboardRoute) }}" class="dark-logo">
            <img src="{{ asset('admin/assets/img/small_logo.png') }}" alt="Logo">
        </a>
    </div>
    <!-- /Logo -->

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- DASHBOARD -->
                <li class="menu-title"><span>Dashboard</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="{{ route($dashboardRoute) }}">
                                <i class="ti ti-layout-navbar"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @hasrole('super-admin')

                <!-- PRODUCTS MANAGEMENT -->
                <li class="menu-title"><span>Access Management</span></li>
                <li>
                    <ul>
                        <!-- Categories -->
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.users*' ,'admin.roles*', 'admin.permissions*']) ? 'active subdrop' : '' }}">
                                <i class="ti ti-users"></i>
                                <span>Users & Roles</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="{{ isActiveMenu(['admin.users*', 'admin.roles*', 'admin.permissions*']) ? 'display: block;' : '' }}">
                                <li class="menu-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.users') }}">
                                        <i class="ti ti-user"></i> Manage Users
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.roles') }}" class="menu-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                                        <i class="ti ti-shield"></i> Manage Roles
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.permissions') }}" class="menu-item {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}">
                                        <i class="ti ti-key"></i> Manage Permissions
                                    </a>
                                </li>
                            </ul>
                        </li>


                    </ul>
                </li>


                @endhasrole

            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
