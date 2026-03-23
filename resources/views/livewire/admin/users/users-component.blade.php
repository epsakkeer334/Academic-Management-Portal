<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">User Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item">Access Management</li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add User
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
                            <i class="ti ti-users text-warning fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Users</h6>
                            <h4 class="mb-0">{{ $stats['total'] ?? 0 }}</h4>
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
                            <i class="ti ti-user-check text-success fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Active Users</h6>
                            <h4 class="mb-0">{{ $stats['active'] ?? 0 }}</h4>
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
                            <i class="ti ti-user-x text-danger fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Inactive Users</h6>
                            <h4 class="mb-0">{{ $stats['inactive'] ?? 0 }}</h4>
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
                            <i class="ti ti-shield-lock text-info fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Roles</h6>
                            <h4 class="mb-0">{{ $rolesCount ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table using common component -->
    <div class="card shadow-sm border-0">
        <livewire:admin.components.table.data-table
            :model-class="\App\Models\User::class"
            :columns="[
                ['label' => '#', 'field' => 'id', 'sortable' => true],
                ['label' => 'Name', 'field' => 'name', 'sortable' => true],
                ['label' => 'Email', 'field' => 'email', 'sortable' => true],
                ['label' => 'Phone', 'field' => 'phone', 'sortable' => true],
                ['label' => 'Role', 'field' => 'role', 'type' => 'badge', 'badge_class' => 'bg-info', 'sortable' => true],
                ['label' => 'Status', 'field' => 'status', 'type' => 'status', 'sortable' => true],
                ['label' => 'Last Login', 'field' => 'last_login_at', 'type' => 'datetime', 'format' => 'd M Y H:i', 'sortable' => true],
                ['label' => 'Joined', 'field' => 'created_at', 'type' => 'datetime', 'format' => 'd M Y', 'sortable' => true],
                ['label' => 'Actions', 'field' => 'actions', 'type' => 'actions', 'actions' => ['edit', 'delete']]
            ]"
            :filters="['All' => 'All', 'active' => 'Active', 'inactive' => 'Inactive']"
            filterField="status"
            title="Users List"
        />
    </div>

    <!-- Role Distribution Card (Unique to Users) -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="fw-semibold mb-0">Role Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($roles)
                            @foreach($roles as $value => $label)
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <div>
                                            <span class="badge {{ $value === 'super-admin' ? 'bg-danger' : 'bg-info' }} badge-sm">
                                                {{ $label }}
                                            </span>
                                        </div>
                                        <span class="fw-bold">{{ $stats['byRole'][$value] ?? 0 }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit User' : 'Create New User' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Full Name *</label>
                            <input type="text" class="form-control" wire:model="name" placeholder="Enter full name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email Address *</label>
                            <input type="email" class="form-control" wire:model="email" placeholder="Enter email">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone Number</label>
                            <input type="text" class="form-control" wire:model="phone" placeholder="Enter phone number">
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">User Role *</label>
                            <select class="form-select" wire:model="role">
                                <option value="">Select Role</option>
                                @if($roles)
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Status</label>
                            <select class="form-select" wire:model="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <!-- Profile Photo -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Profile Photo</label>
                            <input type="file" class="form-control" wire:model="user_image" accept="image/*">
                            <small class="text-muted">Max 2MB (JPG, PNG, GIF, WEBP)</small>
                            @error('user_image') <small class="text-danger">{{ $message }}</small> @enderror

                            <div class="mt-2">
                                <!-- New Image Preview -->
                                @if ($user_image && !is_string($user_image))
                                    <div class="mb-2">
                                        <label class="form-label fw-medium small">New Photo Preview:</label>
                                        <img src="{{ $user_image->temporaryUrl() }}" class="img-thumbnail rounded-circle" style="max-height: 80px; max-width: 80px;">
                                    </div>
                                @endif

                                <!-- Current Image -->
                                @if($isEdit && $recordId)
                                    @php $user = \App\Models\Admin\User::find($recordId) @endphp
                                    @if($user && $user->user_image)
                                        <div>
                                            <label class="form-label fw-medium small">Current Photo:</label>
                                            <img src="{{ $user->user_image_url }}" class="img-thumbnail rounded-circle" style="max-height: 80px; max-width: 80px;">
                                            <small class="text-muted d-block mt-1">Leave empty to keep current photo</small>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @if(!$isEdit)
                            <!-- Password -->
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Password *</label>
                                <input type="password" class="form-control" wire:model="password" placeholder="Enter password">
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label class="form-label fw-medium">Confirm Password *</label>
                                <input type="password" class="form-control" wire:model="password_confirmation" placeholder="Confirm password">
                                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Password Requirements -->
                            <div class="col-12">
                                <div class="alert alert-info py-2 small">
                                    <div class="d-flex align-items-center">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <div>
                                            <p class="mb-1 fw-medium">Password Requirements:</p>
                                            <ul class="mb-0 ps-3">
                                                <li>Minimum 8 characters</li>
                                                <li>At least one uppercase letter</li>
                                                <li>At least one lowercase letter</li>
                                                <li>At least one number</li>
                                                <li>At least one special character</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveUser()">
                        <i class="ti ti-device-floppy me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">Change Password</h4>
                    <button type="button" class="btn-close" wire:click="closePasswordModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- New Password -->
                        <div class="col-12">
                            <label class="form-label fw-medium">New Password *</label>
                            <input type="password" class="form-control" wire:model="newPassword" placeholder="Enter new password">
                            @error('newPassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Confirm New Password *</label>
                            <input type="password" class="form-control" wire:model="newPasswordConfirmation" placeholder="Confirm new password">
                            @error('newPasswordConfirmation') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Password Requirements -->
                        <div class="col-12">
                            <div class="alert alert-info py-2 small">
                                <div class="d-flex align-items-start">
                                    <i class="ti ti-info-circle me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-medium">Password Requirements:</p>
                                        <ul class="mb-0 ps-3">
                                            <li>Minimum 8 characters</li>
                                            <li>At least one uppercase letter</li>
                                            <li>At least one lowercase letter</li>
                                            <li>At least one number</li>
                                            <li>At least one special character</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closePasswordModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="updatePassword()">
                        <i class="ti ti-key me-1"></i> Update Password
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
                    <p class="text-muted mb-4">Are you sure you want to delete this user? This action cannot be undone.</p>
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
    window.saveUser = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    window.updatePassword = function() {
        @this.call('updatePassword');
    }

    const modalConfigs = [
        { name: 'user', id: 'userModal' },
        { name: 'password', id: 'passwordModal' },
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

    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(element => {
        new bootstrap.Tooltip(element);
    });
});
</script>

<style>
.avatar-sm {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-purple {
    background-color: #6f42c1 !important;
}

.bg-teal {
    background-color: #20c997 !important;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.04);
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}

.img-thumbnail.rounded-circle {
    border-radius: 50% !important;
    padding: 2px;
}

.badge.bg-danger { background-color: #dc3545 !important; }
.badge.bg-primary { background-color: #0d6efd !important; }
.badge.bg-success { background-color: #198754 !important; }
.badge.bg-info { background-color: #0dcaf0 !important; color: #000; }
.badge.bg-warning { background-color: #ffc107 !important; color: #000; }
.badge.bg-secondary { background-color: #6c757d !important; }
.badge.bg-purple { background-color: #6f42c1 !important; }
.badge.bg-teal { background-color: #20c997 !important; }

.badge-soft-success {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.badge-soft-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}
</style>
