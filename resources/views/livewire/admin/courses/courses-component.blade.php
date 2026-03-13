<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Courses Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item">Courses Management</li>
                    <li class="breadcrumb-item active">Courses</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Course
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
            :model-class="\App\Models\Admin\Course::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'Image', 'field' => 'image_url', 'type' => 'image-url', 'sortable' => false],
                ['label' => 'Course Name', 'field' => 'name', 'sortable' => true],
                ['label' => 'Duration', 'field' => 'duration', 'sortable' => true],
                ['label' => 'Description', 'field' => 'description', 'type' => 'text', 'limit' => 50, 'sortable' => false],
                ['label' => 'Status', 'field' => 'status', 'type' => 'status', 'sortable' => true],
                ['label' => 'Created At', 'field' => 'formatted_created_at', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="['All' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive']"
            title="Courses List"
        />
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Course' : 'Add Course' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Course Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Course Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Enter course name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Duration -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Duration</label>
                            <input type="text" class="form-control @error('duration') is-invalid @enderror" wire:model="duration" placeholder="e.g., 3 months, 6 weeks">
                            @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Course Image @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" wire:model="image" accept="image/*">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            <div class="mt-2">
                                @if ($image && !is_string($image))
                                    <div class="mb-2">
                                        <label class="form-label fw-medium small">New Image Preview:</label>
                                        <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                @elseif($isEdit && $recordId)
                                    @php $course = \App\Models\Admin\Course::find($recordId) @endphp
                                    @if($course && $course->image)
                                        <div>
                                            <label class="form-label fw-medium small">Current Image:</label>
                                            <img src="{{ $course->image_url }}" class="img-thumbnail" style="max-height: 200px;">
                                            <div class="alert alert-warning py-2 small mt-2">
                                                <i class="ti ti-alert-triangle me-1"></i>
                                                Leave empty to keep current image
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-info py-2 small mt-2">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Please upload a course image (Recommended: 800x600 pixels)
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="4"
                                      placeholder="Enter course description (optional)"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveCourse()">
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
                    <p class="text-muted mb-4">Are you sure you want to delete this course? This action cannot be undone.</p>
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
    // Save course function
    window.saveCourse = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'course', id: 'courseModal' },
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

    // Initialize tooltips
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(element => {
        new bootstrap.Tooltip(element);
    });
});
</script>

