{{-- resources/views/livewire/admin/institute-users/institute-users-component.blade.php --}}
<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Institute Users: {{ $institute->name }}</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.institutes') }}">Institutes</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Assign User
            </button>

            <a href="{{ route('admin.institutes') }}" class="btn btn-outline-secondary d-flex align-items-center">
                <i class="ti ti-arrow-left me-2"></i> Back to Institutes
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
                            <i class="ti ti-users text-warning fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Users</h6>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
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
                            <i class="ti ti-circle-check text-success fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Active</h6>
                            <h4 class="mb-0">{{ $stats['active'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-secondary bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-circle-minus text-secondary fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Inactive</h6>
                            <h4 class="mb-0">{{ $stats['inactive'] }}</h4>
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
                            <i class="ti ti-alert-triangle text-warning fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Suspended</h6>
                            <h4 class="mb-0">{{ $stats['suspended'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Component -->
    <div class="card shadow-sm border-0">
        <livewire:admin.components.table.data-table
            :model-class="\App\Models\Admin\InstituteUser::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'User', 'field' => 'user_details', 'type' => 'html', 'sortable' => false],
                ['label' => 'Email', 'field' => 'user.email', 'sortable' => true],
                ['label' => 'Role', 'field' => 'role_badge', 'type' => 'html', 'sortable' => false],
                ['label' => 'Primary', 'field' => 'primary_badge', 'type' => 'html', 'sortable' => false],
                ['label' => 'Status', 'field' => 'status_badge', 'type' => 'html', 'sortable' => true],
                ['label' => 'Assigned At', 'field' => 'formatted_created_at', 'type' => 'html', 'sortable' => false],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => [
                    'edit',
                    'delete'
                ]]
            ]"
            :filters="[
                '' => 'All Status',
                'active' => 'Active',
                'inactive' => 'Inactive',
                'suspended' => 'Suspended'
            ]"
            :filter-field="'status'"
            title="Institute Users List"
            :searchable="true"
            :search-fields="['user.name', 'user.email']"
            :sortable="true"
            :default-sort-field="'created_at'"
            :default-sort-direction="'desc'"
            :per-page="10"
            :per-page-options="[10, 25, 50, 100]"
            :extra-filters="[
                [
                    'type' => 'select',
                    'field' => 'roleFilter',
                    'label' => 'Role',
                    'db_field' => 'role',
                    'options' => $roles->mapWithKeys(fn($role) => [$role->id => $role->name])->toArray()
                ],
                [
                    'type' => 'hidden',
                    'field' => 'instituteId',
                    'db_field' => 'institute_id',
                    'value' => $instituteId
                ]
            ]"
        />
    </div>

    <!-- Assign/Edit Modal -->
    <div class="modal fade" id="instituteUserModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Assignment' : 'Assign User to Institute' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'save' }}">
                        <div class="mb-3">
                            <label class="form-label fw-medium">User <span class="text-danger">*</span></label>
                            @if($isEdit)
                                <input type="text" class="form-control"
                                       value="{{ $availableUsers->where('id', $userId)->first()?->name ?? 'User' }}"
                                       readonly disabled>
                                <input type="hidden" wire:model="userId">
                            @else
                                <select class="form-select @error('userId') is-invalid @enderror" wire:model="userId">
                                    <option value="">Select User</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('userId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('roleId') is-invalid @enderror" wire:model="roleId">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('roleId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model="isPrimary" id="isPrimary">
                                <label class="form-check-label" for="isPrimary">Primary Contact for Institute</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" wire:click="{{ $isEdit ? 'update' : 'save' }}">
                        <i class="ti ti-device-floppy me-1"></i> {{ $isEdit ? 'Update' : 'Assign' }}
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
                    <h5 class="fw-semibold">Confirm Removal</h5>
                    <p class="text-muted mb-4">Are you sure you want to remove this user from the institute?</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                        <button class="btn btn-danger px-4" wire:click="delete">
                            <i class="ti ti-trash me-1"></i> Yes, Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('livewire:load', function() {
    // Modal management
    const modalConfigs = [
        { name: 'institute-user', id: 'instituteUserModal' },
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

    // Toast messages
    window.addEventListener('show-toast', (event) => {
        // You can implement your toast notification here
        console.log('Toast:', event.detail);
    });
});
</script>
