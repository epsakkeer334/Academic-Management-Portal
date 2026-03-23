<div class="content">
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Reports & Analytics</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <select class="form-select" wire:model.live="reportType">
                <option value="students">Student Reports</option>
                <option value="exams">Exam Reports</option>
                <option value="documents">Document Reports</option>
                <option value="mous">MoU Reports</option>
            </select>
        </div>
    </div>

    <div class="row g-3">
        @if($reportType == 'students')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary">{{ $data['total_students'] ?? 0 }}</h3>
                        <p>Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success">{{ $data['approved_students'] ?? 0 }}</h3>
                        <p>Approved Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning">{{ $data['pending_students'] ?? 0 }}</h3>
                        <p>Pending Students</p>
                    </div>
                </div>
            </div>
        @elseif($reportType == 'exams')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary">{{ $data['total_exams'] ?? 0 }}</h3>
                        <p>Total Exams</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success">{{ $data['completed_exams'] ?? 0 }}</h3>
                        <p>Completed Exams</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-info">{{ $data['upcoming_exams'] ?? 0 }}</h3>
                        <p>Upcoming Exams</p>
                    </div>
                </div>
            </div>
        @elseif($reportType == 'documents')
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary">{{ $data['total_documents'] ?? 0 }}</h3>
                        <p>Total Documents</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success">{{ $data['archived_documents'] ?? 0 }}</h3>
                        <p>Archived Documents</p>
                    </div>
                </div>
            </div>
        @elseif($reportType == 'mous')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary">{{ $data['total_mous'] ?? 0 }}</h3>
                        <p>Total MoUs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success">{{ $data['active_mous'] ?? 0 }}</h3>
                        <p>Active MoUs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning">{{ $data['expiring_mous'] ?? 0 }}</h3>
                        <p>Expiring Soon</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
