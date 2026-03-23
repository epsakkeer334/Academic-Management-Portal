<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Syllabus Mapping</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Syllabus Mapping</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Mapping
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
            :model-class="\App\Models\Admin\SyllabusMapping::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'Curriculum', 'field' => 'curriculum.name', 'sortable' => true],
                ['label' => 'Subject', 'field' => 'subject.name', 'sortable' => true],
                ['label' => 'Institute', 'field' => 'institute.name', 'sortable' => true],
                ['label' => 'Coverage (%)', 'field' => 'coverage_percentage', 'type' => 'number', 'sortable' => true],
                ['label' => 'Status', 'field' => 'status', 'type' => 'badge', 'badge_map' => [
                    'in_progress' => 'bg-warning',
                    'completed' => 'bg-success'
                ], 'sortable' => true],
                ['label' => 'Start Date', 'field' => 'start_date', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Planned Completion', 'field' => 'planned_completion', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Remarks', 'field' => 'remarks', 'type' => 'text', 'limit' => 50, 'sortable' => false],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="[
                'All' => 'All',
                'in_progress' => 'In Progress',
                'completed' => 'Completed'
            ]"
            filterField="status"
            title="Syllabus Mappings"
        />
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="syllabusModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Syllabus Mapping' : 'Add Syllabus Mapping' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
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

                        <!-- Subject -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Subject <span class="text-danger">*</span></label>
                            <select class="form-select @error('subject_id') is-invalid @enderror" wire:model="subject_id">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" wire:model="start_date">
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Planned Completion -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Planned Completion <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('planned_completion') is-invalid @enderror" wire:model="planned_completion">
                            @error('planned_completion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Coverage Percentage -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Coverage Percentage (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('coverage_percentage') is-invalid @enderror"
                                       wire:model="coverage_percentage" min="0" max="100" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                            @error('coverage_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            <!-- Progress Bar Preview -->
                            @if($coverage_percentage > 0)
                                <div class="mt-2">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $coverage_percentage >= 100 ? 'bg-success' : 'bg-primary' }}"
                                             style="width: {{ $coverage_percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">Progress: {{ number_format($coverage_percentage, 2) }}% complete</small>
                                </div>
                            @endif
                        </div>

                        <!-- Remarks -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror"
                                      wire:model="remarks" rows="3"
                                      placeholder="Enter any additional remarks or notes"></textarea>
                            <small class="text-muted">Maximum 500 characters</small>
                            @error('remarks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Info Alert -->
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Note:</strong> Coverage percentage helps track syllabus completion progress.
                                When coverage reaches 100%, the status will automatically update to "Completed".
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveSyllabusMapping()">
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
                    <p class="text-muted mb-4">Are you sure you want to delete this syllabus mapping? This action cannot be undone.</p>
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
    // Save syllabus mapping function
    window.saveSyllabusMapping = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'syllabus', id: 'syllabusModal' },
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

    // Watch for coverage percentage changes to auto-update status
    @this.on('coverage_percentage_updated', (coverage) => {
        if (coverage >= 100 && @this.status !== 'completed') {
            if (confirm('Coverage is 100%. Would you like to mark this as completed?')) {
                @this.set('status', 'completed');
            }
        }
    });

    // Initialize tooltips
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(element => {
        new bootstrap.Tooltip(element);
    });
});

// Optional: Auto-detect when coverage reaches 100%
document.addEventListener('livewire:update', function () {
    const coverageInput = document.querySelector('[wire\\:model="coverage_percentage"]');
    if (coverageInput) {
        coverageInput.addEventListener('change', function() {
            if (parseFloat(this.value) >= 100) {
                @this.call('checkCoverageCompletion', parseFloat(this.value));
            }
        });
    }
});
</script>
