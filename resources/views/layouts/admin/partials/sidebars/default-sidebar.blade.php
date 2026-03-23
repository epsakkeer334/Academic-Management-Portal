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

                <!-- GENERAL MANAGEMENT -->
                <li class="menu-title"><span>General Management</span></li>
                <li>
                    <ul>
                        <!-- Categories -->
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.courses*', 'admin.curriculums*', 'admin.institutes*']) ? 'active subdrop' : '' }}">
                                <i class="ti ti-book"></i>
                                <span>General Management</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="{{ isActiveMenu(['admin.courses*', 'admin.curriculums*', 'admin.institutes*']) ? 'display: block;' : '' }}">

                                <li>
                                    <a href="{{ route('admin.courses') }}" class="menu-item {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                                        <i class="ti ti-book"></i> Manage Courses
                                    </a>
                                </li>


                                <li>
                                    <a href="{{ route('admin.curriculums') }}" class="menu-item {{ request()->routeIs('admin.curriculums*') ? 'active' : '' }}">
                                        <i class="ti ti-books"></i> Manage Curriculums
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.institutes') }}" class="menu-item {{ request()->routeIs('admin.institutes*') ? 'active' : '' }}">
                                        <i class="ti ti-building-community"></i> Manage Institutes
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>


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

                                <li>
                                    <a href="{{ route('admin.users') }}" class="menu-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                        <i class="ti ti-users"></i> Manage Users
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.roles') }}" class="menu-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                                        <i class="ti ti-shield"></i> Manage Roles
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.permissions') }}" class="menu-item {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}">
                                        <i class="ti ti-key"></i> Manage Permissions
                                    </a>
                                </li>
                            </ul>
                        </li>


                    </ul>
                </li>

                <!-- STUDENT MANAGEMENT -->
                <li class="menu-title"><span>Student Management</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="{{ route('admin.students') }}" class="{{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                                <i class="ti ti-users"></i>
                                <span>Students</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- EXAMINATION MANAGEMENT -->
                <li class="menu-title"><span>Examination Management</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.exams*', 'admin.exam-applications*']) ? 'active subdrop' : '' }}">
                                <i class="ti ti-clipboard-list"></i>
                                <span>Examinations</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="{{ isActiveMenu(['admin.exams*', 'admin.exam-applications*']) ? 'display: block;' : '' }}">
                                <li>
                                    <a href="{{ route('admin.exams') }}" class="menu-item {{ request()->routeIs('admin.exams*') ? 'active' : '' }}">
                                        <i class="ti ti-calendar-event"></i> Manage Exams
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.exam-applications') }}" class="menu-item {{ request()->routeIs('admin.exam-applications*') ? 'active' : '' }}">
                                        <i class="ti ti-file-check"></i> Exam Applications
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <!-- DOCUMENT MANAGEMENT -->
                <li class="menu-title"><span>Document Management</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="{{ route('admin.documents') }}" class="{{ request()->routeIs('admin.documents*') ? 'active' : '' }}">
                                <i class="ti ti-file-text"></i>
                                <span>DMS</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.mous') }}" class="{{ request()->routeIs('admin.mous*') ? 'active' : '' }}">
                                <i class="ti ti-file-certificate"></i>
                                <span>MoU Management</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- COMPLIANCE & REPORTS -->
                <li class="menu-title"><span>Compliance & Reports</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="{{ route('admin.compliance') }}" class="{{ request()->routeIs('admin.compliance*') ? 'active' : '' }}">
                                <i class="ti ti-shield-check"></i>
                                <span>Compliance Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                                <i class="ti ti-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @endhasrole

            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
