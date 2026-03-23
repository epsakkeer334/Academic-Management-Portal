<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Exam Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Exams</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Exam
            </button>

            <a href="javascript:void(0);" class="btn btn-outline-light border d-flex align-items-center justify-content-center p-2"
               data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse Header" id="collapse-header">
                <i class="ti ti-chevrons-up text-secondary"></i>
            </a>
        </div>
    </div>

    <!-- Data Table using common component -->
    <div class="card shadow-sm border-0">
        <livewire:admin.components.table.data-table
            :model-class="\App\Models\Admin\Exam::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'Exam Code', 'field' => 'code', 'sortable' => true],
                ['label' => 'Exam Name', 'field' => 'name', 'sortable' => true],
                ['label' => 'Type', 'field' => 'type', 'type' => 'badge', 'badge_class' => 'bg-info', 'sortable' => true],
                ['label' => 'Institute', 'field' => 'institute.name', 'sortable' => false],
                ['label' => 'Course', 'field' => 'course.name', 'sortable' => false],
                ['label' => 'Curriculum', 'field' => 'curriculum.name', 'sortable' => false],
                ['label' => 'Exam Date', 'field' => 'exam_date', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Start Time', 'field' => 'start_time', 'type' => 'time', 'sortable' => true],
                ['label' => 'End Time', 'field' => 'end_time', 'type' => 'time', 'sortable' => true],
                ['label' => 'Passing %', 'field' => 'passing_percentage', 'type' => 'number', 'sortable' => true],
                ['label' => 'Status', 'field' => 'status', 'type' => 'badge', 'badge_map' => [
                    'draft' => 'bg-secondary',
                    'scheduled' => 'bg-info',
                    'ongoing' => 'bg-warning',
                    'completed' => 'bg-success',
                    'cancelled' => 'bg-danger'
                ], 'sortable' => true],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="[
                'All' => 'All',
                'draft' => 'Draft',
                'scheduled' => 'Scheduled',
                'ongoing' => 'Ongoing',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled'
            ]"
            filterField="status"
            title="Exams List"
        />
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="examModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Exam' : 'Add Exam' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Exam Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Exam Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Enter exam name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Exam Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Exam Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" wire:model="code" placeholder="e.g., EXAM001">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Exam Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Exam Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                <option value="">Select Type</option>
                                <option value="mid-sem">Mid Semester</option>
                                <option value="semester">Semester</option>
                                <option value="practical">Practical</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="draft">Draft</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

                        <!-- Course -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Course <span class="text-danger">*</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" wire:model="course_id">
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                            @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Curriculum -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Curriculum <span class="text-danger">*</span></label>
                            <select class="form-select @error('curriculum_id') is-invalid @enderror" wire:model="curriculum_id">
                                <option value="">Select Curriculum</option>
                                @foreach($curriculums as $curriculum)
                                    <option value="{{ $curriculum->id }}">{{ $curriculum->name }}</option>
                                @endforeach
                            </select>
                            @error('curriculum_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Exam Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Exam Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('exam_date') is-invalid @enderror" wire:model="exam_date">
                            @error('exam_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Start Time -->
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" wire:model="start_time">
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- End Time -->
                        <div class="col-md-3">
                            <label class="form-label fw-medium">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" wire:model="end_time">
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Passing Percentage -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Passing Percentage (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('passing_percentage') is-invalid @enderror"
                                   wire:model="passing_percentage" min="0" max="100" step="0.01">
                            @error('passing_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      wire:model="description" rows="4"
                                      placeholder="Enter exam description (optional)"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveExam()">
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
                    <p class="text-muted mb-4">Are you sure you want to delete this exam? This action cannot be undone.</p>
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
    // Save exam function
    window.saveExam = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'exam', id: 'examModal' },
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
