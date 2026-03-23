<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Student Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Students</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Student
            </button>

            <a href="javascript:void(0);" class="btn btn-outline-light border d-flex align-items-center justify-content-center p-2"
               data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse Header" id="collapse-header">
                <i class="ti ti-chevrons-up text-secondary"></i>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-users text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Students</h6>
                            <h4 class="mb-0">{{ $totalStudents }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-success bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-circle-check text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Approved</h6>
                            <h4 class="mb-0">{{ $approvedStudents }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-warning bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-clock text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Pending</h6>
                            <h4 class="mb-0">{{ $pendingStudents }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-danger bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-circle-x text-danger fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Rejected</h6>
                            <h4 class="mb-0">{{ $rejectedStudents }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table using common component -->
    <div class="card shadow-sm border-0">
        <livewire:admin.components.table.data-table
            :model-class="\App\Models\Admin\Student::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'ER Number', 'field' => 'er_number', 'type' => 'badge', 'badge_class' => 'bg-info', 'sortable' => false],
                ['label' => 'Name', 'field' => 'name', 'sortable' => true],
                ['label' => 'Email', 'field' => 'email', 'sortable' => true],
                ['label' => 'Phone', 'field' => 'phone', 'sortable' => true],
                ['label' => 'Institute', 'field' => 'institute.name', 'sortable' => false],
                ['label' => 'Course Applied', 'field' => 'course_applied', 'sortable' => true],
                ['label' => 'Status', 'field' => 'er_status', 'type' => 'badge', 'badge_map' => [
                    'pending' => 'bg-warning',
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    'generated' => 'bg-info'
                ], 'sortable' => true],
                ['label' => 'Created At', 'field' => 'created_at', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="[
                'All' => 'All',
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'generated' => 'Generated'
            ]"
            filterField="er_status"
            title="Students List"
        />
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Student' : 'Add Student' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Personal Information Section -->
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Enter full name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="Enter email address">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" placeholder="Enter phone number">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('dob') is-invalid @enderror" wire:model="dob">
                            @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Gender <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" wire:model="gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select @error('er_status') is-invalid @enderror" wire:model="er_status">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="generated">Generated</option>
                            </select>
                            @error('er_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Academic Information Section -->
                        <div class="col-12 mt-2">
                            <h5 class="border-bottom pb-2 mb-3">Academic Information</h5>
                        </div>

                        <!-- Qualification -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Qualification <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('qualification') is-invalid @enderror" wire:model="qualification" placeholder="e.g., B.Sc, M.Sc">
                            @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Course Applied -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Course Applied <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('course_applied') is-invalid @enderror" wire:model="course_applied" placeholder="Enter course name">
                            @error('course_applied') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Institute -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Institute <span class="text-danger">*</span></label>
                            <select class="form-select @error('institute_id') is-invalid @enderror" wire:model="institute_id">
                                <option value="">Select Institute</option>
                                @foreach($institutes as $institute)
                                    <option value="{{ $institute->id }}">{{ $institute->name }}</option>
                                @endforeach
                            </select>
                            @error('institute_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Parent/Guardian Information Section -->
                        <div class="col-12 mt-2">
                            <h5 class="border-bottom pb-2 mb-3">Parent/Guardian Information</h5>
                        </div>

                        <!-- Father Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Father's Name</label>
                            <input type="text" class="form-control" wire:model="father_name" placeholder="Enter father's name">
                        </div>

                        <!-- Father Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Father's Phone</label>
                            <input type="text" class="form-control" wire:model="father_phone" placeholder="Enter father's phone">
                        </div>

                        <!-- Mother Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Mother's Name</label>
                            <input type="text" class="form-control" wire:model="mother_name" placeholder="Enter mother's name">
                        </div>

                        <!-- Mother Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Mother's Phone</label>
                            <input type="text" class="form-control" wire:model="mother_phone" placeholder="Enter mother's phone">
                        </div>

                        <!-- Address Information Section -->
                        <div class="col-12 mt-2">
                            <h5 class="border-bottom pb-2 mb-3">Address Information</h5>
                        </div>

                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address" rows="2" placeholder="Enter street address"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- City -->
                        <div class="col-md-4">
                            <label class="form-label fw-medium">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" wire:model="city" placeholder="Enter city">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- State -->
                        <div class="col-md-4">
                            <label class="form-label fw-medium">State <span class="text-danger">*</span></label>
                            <select class="form-select @error('state_id') is-invalid @enderror" wire:model="state_id">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Country <span class="text-danger">*</span></label>
                            <select class="form-select @error('country_id') is-invalid @enderror" wire:model="country_id">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Pincode -->
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Pincode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror" wire:model="pincode" placeholder="Enter pincode">
                            @error('pincode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Document Upload Section (Optional) -->
                        <div class="col-12 mt-2">
                            <h5 class="border-bottom pb-2 mb-3">Documents (Optional)</h5>
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                You can upload documents after creating the student record.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveStudent()">
                        <i class="ti ti-device-floppy me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <i class="ti ti-alert-triangle text-danger fs-1 mb-3"></i>
                    <h5>Confirm Delete</h5>
                    <p class="text-muted mb-4">Are you sure you want to delete this student? This action cannot be undone.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger px-4" wire:click="delete">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('livewire:load', function () {
    // Save student function
    window.saveStudent = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'student', id: 'studentModal' },
        { name: 'delete', id: 'deleteModal' }
    ];

    modalConfigs.forEach(config => {
        window.addEventListener(`open-${config.name}-modal`, () => {
            const modal = new bootstrap.Modal(document.getElementById(config.id));
            modal.show();
        });

        window.addEventListener(`close-${config.name}-modal`, () => {
            const modalElement = document.getElementById(config.id);
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (modalInstance) {
                modalInstance.hide();
            }

            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
        });
    });

    // Show toast notifications
    window.addEventListener('show-toast', (event) => {
        const toastHtml = `
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                <div class="toast show" role="alert">
                    <div class="toast-header">
                        <i class="ti ti-${event.detail.type === 'success' ? 'check-circle text-success' : 'alert-circle text-danger'} me-2"></i>
                        <strong class="me-auto">${event.detail.type === 'success' ? 'Success' : 'Error'}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${event.detail.message}
                    </div>
                </div>
            </div>
        `;

        // Remove existing toasts
        document.querySelectorAll('.position-fixed.top-0.end-0').forEach(toast => toast.remove());

        // Add new toast
        document.body.insertAdjacentHTML('beforeend', toastHtml);

        // Auto remove after 3 seconds
        setTimeout(() => {
            const toast = document.querySelector('.position-fixed.top-0.end-0');
            if (toast) toast.remove();
        }, 3000);
    });

    // Initialize tooltips
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(element => {
        new bootstrap.Tooltip(element);
    });
});
</script>
