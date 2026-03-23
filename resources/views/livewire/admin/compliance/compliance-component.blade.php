<div class="content">
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Compliance Dashboard</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Compliance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-success bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-file-check text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Active SOPs</h6>
                            <h4 class="mb-0">{{ $activeSops }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-clock text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Pending HoT Approvals</h6>
                            <h4 class="mb-0">{{ $pendingHotApprovals }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-info bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-send text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Pending External Submission</h6>
                            <h4 class="mb-0">{{ $pendingExternal }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-danger bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-calendar-x text-danger fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">MoUs Expiring Soon</h6>
                            <h4 class="mb-0">{{ $mousExpiring }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-secondary bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-refresh text-secondary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Revision Due</h6>
                            <h4 class="mb-0">{{ $revisionDue }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
