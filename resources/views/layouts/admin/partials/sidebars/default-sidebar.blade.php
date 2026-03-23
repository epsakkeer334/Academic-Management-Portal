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

                <!-- GENERAL MANAGEMENT (visible to users with manage/view permissions) -->
                @canany(['view_institutes', 'manage_institutes', 'manage_exams', 'manage_students'])
                <li class="menu-title"><span>General Management</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.courses*', 'admin.curriculums*', 'admin.subjects*', 'admin.institutes*']) ? 'active subdrop' : '' }}">
                                <i class="ti ti-book"></i>
                                <span>General Management</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="{{ isActiveMenu(['admin.courses*', 'admin.curriculums*', 'admin.subjects*', 'admin.institutes*']) ? 'display: block;' : '' }}">
                                @can('manage_exams')
                                <li>
                                    <a href="{{ route('admin.courses') }}" class="menu-item {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
                                        <i class="ti ti-book"></i> Manage Courses
                                    </a>
                                </li>
                                @endcan

                                @can('manage_exams')
                                <li>
                                    <a href="{{ route('admin.curriculums') }}" class="menu-item {{ request()->routeIs('admin.curriculums*') ? 'active' : '' }}">
                                        <i class="ti ti-books"></i> Manage Curriculums
                                    </a>
                                </li>
                                @endcan

                                @can('manage_exams')
                                <li>
                                    <a href="{{ route('admin.subjects') }}" class="menu-item {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}">
                                        <i class="ti ti-list"></i> Manage Subjects
                                    </a>
                                </li>
                                @endcan

                                @can('view_institutes')
                                <li>
                                    <a href="{{ route('admin.institutes') }}" class="menu-item {{ request()->routeIs('admin.institutes*') ? 'active' : '' }}">
                                        <i class="ti ti-building-community"></i> Manage Institutes
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                    </ul>
                </li>

                @endcanany

                <!-- ACCESS MANAGEMENT (super-admin only) -->
                @hasrole('super-admin')
                <li class="menu-title"><span>Access Management</span></li>
                <li>
                    <ul>
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
                @endhasrole

                <!-- STUDENT MANAGEMENT -->
                @can('manage_students')
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
                @endcan

                <!-- EXAMINATION MANAGEMENT -->
                @canany(['manage_exams','approve_exam_applications','map_subjects','tm_exam_approval'])
                <li class="menu-title"><span>Examination Management</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.exams*', 'admin.exam-applications*', 'admin.syllabus-mappings*']) ? 'active subdrop' : '' }}">
                                <i class="ti ti-clipboard-list"></i>
                                <span>Examinations</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul style="{{ isActiveMenu(['admin.exams*', 'admin.exam-applications*', 'admin.syllabus-mappings*']) ? 'display: block;' : '' }}">
                                @can('manage_exams')
                                <li>
                                    <a href="{{ route('admin.exams') }}" class="menu-item {{ request()->routeIs('admin.exams*') ? 'active' : '' }}">
                                        <i class="ti ti-calendar-event"></i> Manage Exams
                                    </a>
                                </li>
                                @endcan

                                @can('approve_exam_applications')
                                <li>
                                    <a href="{{ route('admin.exam-applications') }}" class="menu-item {{ request()->routeIs('admin.exam-applications*') ? 'active' : '' }}">
                                        <i class="ti ti-file-check"></i> Exam Applications
                                    </a>
                                </li>
                                @endcan

                                @can('map_subjects')
                                <li>
                                    <a href="{{ route('admin.syllabus-mappings') }}" class="menu-item {{ request()->routeIs('admin.syllabus-mappings*') ? 'active' : '' }}">
                                        <i class="ti ti-layout-grid"></i> Syllabus Mappings
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                    </ul>
                </li>
                @endcanany

                <!-- DOCUMENT MANAGEMENT -->
                @canany(['manage_documents','hot_approve_documents'])
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
                @endcanany

                <!-- COMPLIANCE & REPORTS -->
                @canany(['view_compliance_dashboard','view_reports'])
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
                @endcanany

            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
