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
                            <a href="{{ route($dashboardRoute) }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="ti ti-layout-navbar"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </li>

                @hasrole('super-admin')
                    <!-- INSTITUTION MANAGEMENT -->
                    <li class="menu-title"><span>Institution Management</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.institutions*', 'admin.institute-admins*']) ? 'active subdrop' : '' }}">
                                    <i class="ti ti-building-community"></i>
                                    <span>Institutions</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ isActiveMenu(['admin.institutions*', 'admin.institute-admins*']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->routeIs('admin.institutions*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.institutions') }}">
                                            <i class="ti ti-list"></i> Manage Institutions
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.institute-admins*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.institute-admins') }}">
                                            <i class="ti ti-user-cog"></i> Institute Admins
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.institute-reports*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.institute-reports') }}">
                                            <i class="ti ti-report"></i> Institute Reports
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
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.students*', 'admin.enrollments*']) ? 'active subdrop' : '' }}">
                                    <i class="ti ti-users"></i>
                                    <span>Students</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ isActiveMenu(['admin.students*', 'admin.enrollments*']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.students') }}">
                                            <i class="ti ti-list"></i> All Students
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.enrollments*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.enrollments') }}">
                                            <i class="ti ti-file-check"></i> Enrollments
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.er-requests*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.er-requests') }}">
                                            <i class="ti ti-file-text"></i> ER Requests
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.student-reports*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.student-reports') }}">
                                            <i class="ti ti-report"></i> Student Reports
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- EXAMINATION MANAGEMENT -->
                    <li class="menu-title"><span>Examination</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.exams*', 'admin.results*']) ? 'active subdrop' : '' }}">
                                    <i class="ti ti-notes"></i>
                                    <span>Exams</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ isActiveMenu(['admin.exams*', 'admin.results*']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->routeIs('admin.exams*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.exams') }}">
                                            <i class="ti ti-calendar"></i> Manage Exams
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.exam-applications*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.exam-applications') }}">
                                            <i class="ti ti-file-check"></i> Applications
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.admit-cards*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.admit-cards') }}">
                                            <i class="ti ti-card"></i> Admit Cards
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.marks-entry*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.marks-entry') }}">
                                            <i class="ti ti-edit"></i> Marks Entry
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.results*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.results') }}">
                                            <i class="ti ti-chart-bar"></i> Results
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.marksheets*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.marksheets') }}">
                                            <i class="ti ti-file-certificate"></i> Marksheets
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
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.documents*', 'admin.dms*']) ? 'active subdrop' : '' }}">
                                    <i class="ti ti-file-text"></i>
                                    <span>DMS</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ isActiveMenu(['admin.documents*', 'admin.dms*']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->routeIs('admin.documents*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.documents') }}">
                                            <i class="ti ti-list"></i> All Documents
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.draft-documents*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.draft-documents') }}">
                                            <i class="ti ti-draft"></i> Draft Documents
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.pending-approvals*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.pending-approvals') }}">
                                            <i class="ti ti-clock"></i> Pending Approvals
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.archived-documents*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.archived-documents') }}">
                                            <i class="ti ti-archive"></i> Archive
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- MOU MANAGEMENT -->
                    <li class="menu-title"><span>MoU Management</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.mous*']) ? 'active subdrop' : '' }}">
                                    <i class="ti ti-handshake"></i>
                                    <span>MoUs</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ isActiveMenu(['admin.mous*']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->routeIs('admin.mous*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.mous') }}">
                                            <i class="ti ti-list"></i> All MoUs
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.active-mous*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.active-mous') }}">
                                            <i class="ti ti-check-circle"></i> Active MoUs
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.expiring-mous*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.expiring-mous') }}">
                                            <i class="ti ti-alert-triangle"></i> Expiring Soon
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- COMPLIANCE & AUDIT -->
                    <li class="menu-title"><span>Compliance & Audit</span></li>
                    <li>
                        <ul>
                            <li>
                                <a href="{{ route('admin.compliance-dashboard') }}" class="{{ request()->routeIs('admin.compliance-dashboard*') ? 'active' : '' }}">
                                    <i class="ti ti-checklist"></i>
                                    <span>Compliance Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.audit-logs') }}" class="{{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
                                    <i class="ti ti-history"></i>
                                    <span>Audit Logs</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- USER & ACCESS MANAGEMENT -->
                    <li class="menu-title"><span>Access Management</span></li>
                    <li>
                        <ul>
                            <!-- Users -->
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.users*', 'admin.roles*', 'admin.permissions*']) ? 'active subdrop' : '' }}">
                                    <i class="ti ti-users"></i>
                                    <span>Users & Roles</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ isActiveMenu(['admin.users*', 'admin.roles*', 'admin.permissions*']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.users') }}">
                                            <i class="ti ti-user"></i> Manage Users
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.roles') }}" class="menu-item {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                                            <i class="ti ti-shield"></i> Manage Roles
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.permissions*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.permissions') }}" class="menu-item {{ request()->routeIs('admin.permissions*') ? 'active' : '' }}">
                                            <i class="ti ti-key"></i> Manage Permissions
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- REPORTS & EXPORTS -->
                    <li class="menu-title"><span>Reports</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);" class="{{ isActiveMenu(['admin.reports*']) ? 'active subdrop' : '' }}">
                                    <i class="ti ti-report"></i>
                                    <span>All Reports</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul style="{{ isActiveMenu(['admin.reports*']) ? 'display: block;' : '' }}">
                                    <li class="{{ request()->routeIs('admin.reports.institution*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.reports.institution') }}">
                                            <i class="ti ti-building"></i> Institution Reports
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.reports.student*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.reports.student') }}">
                                            <i class="ti ti-users"></i> Student Reports
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.reports.exam*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.reports.exam') }}">
                                            <i class="ti ti-notes"></i> Exam Reports
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.reports.compliance*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.reports.compliance') }}">
                                            <i class="ti ti-checklist"></i> Compliance Reports
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('admin.reports.export*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.reports.export') }}">
                                            <i class="ti ti-download"></i> Export Data
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- SYSTEM SETTINGS -->
                    <li class="menu-title"><span>System</span></li>
                    <li>
                        <ul>
                            <li>
                                <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                                    <i class="ti ti-settings"></i>
                                    <span>System Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.notifications') }}" class="{{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                                    <i class="ti ti-bell"></i>
                                    <span>Notifications</span>
                                    @php
                                        $unreadCount = Auth::user()->unreadNotifications->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="badge bg-danger ms-auto">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.backup') }}" class="{{ request()->routeIs('admin.backup*') ? 'active' : '' }}">
                                    <i class="ti ti-database"></i>
                                    <span>Backup & Restore</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs*') ? 'active' : '' }}">
                                    <i class="ti ti-terminal"></i>
                                    <span>System Logs</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Regular User/Institute Admin Menu -->
                    <li class="menu-title"><span>Main Menu</span></li>
                    <li>
                        <ul>
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="ti ti-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                                    <i class="ti ti-user"></i>
                                    <span>My Profile</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endhasrole

                <!-- LOGOUT (always visible) -->
                <li class="menu-title"><span>Account</span></li>
                <li>
                    <ul>
                        <li>
                            <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                                <i class="ti ti-user-circle"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti ti-logout"></i>
                                <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
