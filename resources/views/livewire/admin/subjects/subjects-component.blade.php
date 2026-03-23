<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Subjects Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Subjects</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Subject
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
                            <i class="ti ti-book text-primary fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Subjects</h6>
                            <h4 class="mb-0">{{ \App\Models\Admin\Subject::count() }}</h4>
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
                            <h6 class="mb-1">Active Subjects</h6>
                            <h4 class="mb-0">{{ \App\Models\Admin\Subject::active()->count() }}</h4>
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
                            <i class="ti ti-alert-circle text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Inactive Subjects</h6>
                            <h4 class="mb-0">{{ \App\Models\Admin\Subject::where('is_active', false)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-info bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-chart-bar text-info fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Avg Credits</h6>
                            <h4 class="mb-0">{{ number_format(\App\Models\Admin\Subject::avg('credits') ?? 0, 1) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table using common component -->
    <div class="card shadow-sm border-0">
        <livewire:admin.components.table.data-table
            :model-class="\App\Models\Admin\Subject::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'Name', 'field' => 'name', 'sortable' => true],
                ['label' => 'Code', 'field' => 'code', 'type' => 'badge', 'badge_class' => 'bg-info', 'sortable' => true],
                ['label' => 'Course', 'field' => 'course.name', 'sortable' => true],
                ['label' => 'Curriculum', 'field' => 'curriculum.name', 'sortable' => true],
                ['label' => 'Credits', 'field' => 'credits', 'type' => 'number', 'sortable' => true],
                ['label' => 'Type', 'field' => 'type', 'type' => 'badge', 'badge_map' => [
                    'theory' => 'bg-primary',
                    'practical' => 'bg-success',
                    'lab' => 'bg-warning',
                    'project' => 'bg-info'
                ], 'sortable' => true],
                ['label' => 'Status', 'field' => 'is_active', 'type' => 'status', 'sortable' => true],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="['All' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive']"
            filterField="is_active"
            title="Subjects List"
        />
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Subject' : 'Add Subject' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Subject Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   wire:model="name" placeholder="Enter subject name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Subject Code -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Subject Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   wire:model="code" placeholder="e.g., CS101, MATH201">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Unique code for the subject</small>
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

                        <!-- Credits -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Credits <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('credits') is-invalid @enderror"
                                       wire:model="credits" min="0" max="10" step="1">
                                <span class="input-group-text">credits</span>
                            </div>
                            @error('credits') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Number of credits (0-10)</small>
                        </div>

                        <!-- Subject Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Subject Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                <option value="theory">Theory</option>
                                <option value="practical">Practical</option>
                                <option value="lab">Lab</option>
                                <option value="project">Project</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="is_active" role="switch">
                                <label class="form-check-label" for="is_active">
                                    {{ $is_active ? 'Active' : 'Inactive' }}
                                </label>
                            </div>
                            @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      wire:model="description" rows="4"
                                      placeholder="Enter subject description (optional)"></textarea>
                            <small class="text-muted">Maximum 1000 characters</small>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Note:</strong> Subjects can be associated with specific courses and curriculums.
                                Make sure to select the correct course and curriculum for proper syllabus mapping.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveSubject()">
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
                    <p class="text-muted mb-4">Are you sure you want to delete this subject? This action cannot be undone.</p>
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
    // Save subject function
    window.saveSubject = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'subject', id: 'subjectModal' },
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

// Auto-generate subject code suggestion (optional)
document.addEventListener('livewire:update', function () {
    const nameInput = document.querySelector('[wire\\:model="name"]');
    const codeInput = document.querySelector('[wire\\:model="code"]');

    if (nameInput && codeInput && !@this.isEdit) {
        nameInput.addEventListener('blur', function() {
            if (!codeInput.value) {
                // Generate code from name (first 3 letters + random number)
                const name = this.value.trim();
                if (name) {
                    const prefix = name.substring(0, 3).toUpperCase();
                    const randomNum = Math.floor(Math.random() * 900) + 100;
                    const suggestedCode = `${prefix}${randomNum}`;
                    @this.set('code', suggestedCode);
                }
            }
        });
    }
});
</script>
