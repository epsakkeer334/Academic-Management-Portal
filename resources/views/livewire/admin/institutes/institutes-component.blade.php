<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Institute Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Institutes</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Institute
            </button>

            <a href="javascript:void(0);" class="btn btn-outline-light border d-flex align-items-center justify-content-center p-2"
               data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse Header" id="collapse-header">
                <i class="ti ti-chevrons-up text-secondary"></i>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-building-community text-warning fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Institutes</h6>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-success bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-circle-check text-success fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Active Institutes</h6>
                            <h4 class="mb-0">{{ $stats['active'] }}</h4>
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
                            <i class="ti ti-circle-x text-danger fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Inactive Institutes</h6>
                            <h4 class="mb-0">{{ $stats['inactive'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table using common component -->
    <div class="card shadow-sm border-0">
        <livewire:admin.components.table.data-table
            :model-class="\App\Models\Admin\Institute::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'Logo', 'field' => 'logo', 'type' => 'image', 'sortable' => false],
                ['label' => 'Institute', 'field' => 'name', 'sortable' => true],
                ['label' => 'Code', 'field' => 'code', 'sortable' => true],
                ['label' => 'Contact', 'field' => 'email', 'sortable' => true],
                ['label' => 'Location', 'field' => 'city', 'sortable' => true],
                ['label' => 'State', 'field' => 'state_name', 'sortable' => false],
                ['label' => 'Status', 'field' => 'status', 'type' => 'status', 'sortable' => true],
                ['label' => 'Created', 'field' => 'created_at', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="['All' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive']"
            title="Institutes List"
            :searchable="true"
            :search-fields="['name', 'code', 'email', 'phone', 'city']"
            :sortable="true"
            :default-sort-field="'name'"
            :default-sort-direction="'asc'"
            :per-page="10"
            :per-page-options="[5, 10, 25, 50]"
            :extra-filters="[
                ['type' => 'select', 'field' => 'stateFilter', 'label' => 'State', 'options' => $states->pluck('name', 'id')->toArray()]
            ]"
        />
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="instituteModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Institute' : 'Add Institute' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Institute Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   wire:model="name" placeholder="Enter institute name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Institute Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                   wire:model="code" placeholder="Enter unique code">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   wire:model="email" placeholder="Enter email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   wire:model="phone" placeholder="Enter phone number">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                   wire:model="website" placeholder="https://example.com">
                            @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Address Information -->
                        <div class="col-12 mt-3">
                            <h6 class="border-bottom pb-2 mb-3">Address Information</h6>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      wire:model="address" rows="2" placeholder="Enter street address"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                   wire:model="city" placeholder="Enter city">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">State</label>
                            <select class="form-select @error('state_id') is-invalid @enderror" wire:model="state_id">
                                <option value="">Select State</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                            @error('state_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Country</label>
                            <input type="text" class="form-control" value="India" readonly disabled>
                            <input type="hidden" wire:model="country_id" value="1">
                            <small class="text-muted">Default: India</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-medium">Pincode</label>
                            <input type="text" class="form-control @error('pincode') is-invalid @enderror"
                                   wire:model="pincode" placeholder="Enter pincode">
                            @error('pincode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Logo Upload -->
                        <div class="col-12 mt-3">
                            <h6 class="border-bottom pb-2 mb-3">Institute Logo</h6>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium">Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror"
                                   wire:model="logo" accept="image/*">
                            <small class="text-muted">Max 2MB (JPG, PNG, GIF, WEBP)</small>
                            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            <div class="mt-2">
                                @if ($logo && !is_string($logo))
                                    <div class="mb-2">
                                        <label class="form-label fw-medium small">New Logo Preview:</label>
                                        <img src="{{ $logo->temporaryUrl() }}" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @elseif($isEdit && $recordId)
                                    @php $institute = \App\Models\Admin\Institute::find($recordId) @endphp
                                    @if($institute && $institute->logo)
                                        <div>
                                            <label class="form-label fw-medium small">Current Logo:</label>
                                            <img src="{{ $institute->logo_url }}" class="img-thumbnail" style="max-height: 100px;">
                                            <div class="alert alert-warning py-2 small mt-2">
                                                <i class="ti ti-alert-triangle me-1"></i>
                                                Leave empty to keep current logo
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-info py-2 small mt-2">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Upload a logo for the institute (Recommended: 200x200 pixels)
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveInstitute()">
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
                    <p class="text-muted mb-4">Are you sure you want to delete this institute? This action cannot be undone.</p>
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
    // Save institute function
    window.saveInstitute = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'institute', id: 'instituteModal' },
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

<!-- Additional CSS -->
<style>
.avatar-sm {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fs-4 {
    font-size: 1.5rem !important;
}

/* Modal styles */
.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}

/* Image preview */
.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 2px;
}

/* Section headers */
h6.border-bottom {
    border-color: #dee2e6 !important;
    color: #495057;
    font-weight: 600;
}
</style>
