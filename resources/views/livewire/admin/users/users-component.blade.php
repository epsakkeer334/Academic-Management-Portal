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

    <!-- Stats Cards (Unique to Users) -->
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
                            <i class="ti ti-user-check text-success fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Active Users</h6>
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
                        <div class="avatar-sm bg-danger bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-user-x text-danger fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Inactive Users</h6>
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
                        <div class="avatar-sm bg-info bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-shield-lock text-info fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Roles</h6>
                            <h4 class="mb-0">{{ count($roles) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Data Table -->
    <div class="card shadow-sm border-0">
        <!-- Card Header with Filter -->
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <h5 class="fw-semibold mb-0">Users List</h5>

                <!-- Using table-filter component - exactly like category -->
                @include('livewire.common.components.table.table-filter', [
                    'filter' => $filter,
                    'options' => [
                        'All' => 'All',
                        'Active' => 'Active',
                        'Inactive' => 'Inactive'
                    ]
                ])
            </div>
        </div>

        <!-- Sub-header with Search, Per Page and Role Filter -->
        <div class="border-bottom px-3 py-2 bg-light">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <!-- Using table-search-sort component for search and per page -->
                    @include('livewire.common.components.table.table-search-sort')

                    <!-- Additional Role Filter Dropdown -->
                    <select class="form-select form-select-sm" wire:model="roleFilter" style="width: 150px;">
                        <option value="">All Roles</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <!-- Using table-headers component -->
                    @include('livewire.common.components.table.table-headers', [
                        'columns' => [
                            ['field' => 'id', 'label' => '#', 'sortable' => true],
                            ['field' => 'user_image', 'label' => 'Photo', 'sortable' => false],
                            ['field' => 'name', 'label' => 'Name', 'sortable' => true],
                            ['field' => 'email', 'label' => 'Email', 'sortable' => true],
                            ['field' => 'phone', 'label' => 'Phone', 'sortable' => true],
                            ['field' => 'role', 'label' => 'Role', 'sortable' => true],
                            ['field' => 'status', 'label' => 'Status', 'sortable' => true],
                            ['field' => 'last_login_at', 'label' => 'Last Login', 'sortable' => true],
                            ['field' => 'created_at', 'label' => 'Joined', 'sortable' => true],
                            ['field' => 'action', 'label' => 'Actions', 'sortable' => false],
                        ]
                    ])
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="text-center text-muted small">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>
                                <td class="text-center">
                                    <img src="{{ $user->user_image_url }}"
                                         alt="{{ $user->name }}"
                                         class="rounded-circle"
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->email }}" class="text-primary">
                                        <i class="ti ti-mail me-1"></i>{{ $user->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($user->phone)
                                        <a href="tel:{{ $user->phone }}" class="text-muted">
                                            <i class="ti ti-phone me-1"></i>{{ $user->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $user->role_badge_class }} badge-sm">
                                        <i class="ti ti-shield me-1"></i>
                                        {{ $roles[$user->role] ?? $user->role }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($user->status)
                                        <span class="badge badge-soft-success d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-checks me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-x me-1"></i> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="ti ti-clock {{ $user->is_online ? 'text-success' : 'text-muted' }}"></i>
                                            </div>
                                            <div>
                                                <div class="small">{{ $user->last_login_at->format('d M Y') }}</div>
                                                <div class="small text-muted">{{ $user->last_login_at->format('h:i A') }}</div>
                                                @if($user->last_login_ip)
                                                    <small class="text-muted">
                                                        <i class="ti ti-world"></i> {{ $user->last_login_ip }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Never logged in</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-muted small">{{ $user->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- Edit Button -->
                                        <button wire:click="edit({{ $user->id }})"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="tooltip"
                                                title="Edit User">
                                            <i class="ti ti-edit"></i>
                                        </button>

                                        <!-- Change Password Button -->
                                        <button wire:click="openChangePasswordModal({{ $user->id }})"
                                                class="btn btn-sm btn-outline-warning"
                                                data-bs-toggle="tooltip"
                                                title="Change Password">
                                            <i class="ti ti-key"></i>
                                        </button>

                                        @if($user->id != auth()->id())
                                            <!-- Toggle Status Button -->
                                            <button wire:click="toggleStatus({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-{{ $user->status ? 'danger' : 'success' }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ $user->status ? 'Deactivate' : 'Activate' }} User">
                                                <i class="ti ti-{{ $user->status ? 'user-x' : 'user-check' }}"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <button wire:click="confirmDelete({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete User">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="ti ti-users fs-2"></i><br>
                                    No users found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center p-3 bg-white border-top">
            <div class="text-muted small">
                Showing <strong>{{ $users->firstItem() ?? 0 }}</strong> to
                <strong>{{ $users->lastItem() ?? 0 }}</strong> of
                <strong>{{ $users->total() }}</strong> entries
            </div>
            <div>{{ $users->links() }}</div>
        </div>
    </div>

    <!-- Role Distribution Card (Keep as is - unique to users) -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="fw-semibold mb-0">Role Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
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
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
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
                                    @php $user = \App\Models\User::find($recordId) @endphp
                                    @if($user && $user->user_image)
                                        <div>
                                            <label class="form-label fw-medium small">Current Photo:</label>
                                            <img src="{{ $user->user_image_url }}" class="img-thumbnail rounded-circle" style="max-height: 80px; max-width: 80px;">
                                            <small class="text-muted d-block mt-1">
                                                Leave empty to keep current photo
                                            </small>
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
                        @endif

                        <!-- Password Requirements -->
                        @if(!$isEdit)
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
    // Save user function
    window.saveUser = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Update password function
    window.updatePassword = function() {
        @this.call('updatePassword');
    }

    // Modal management
    const modalConfigs = [
        { name: 'user', id: 'userModal' },
        { name: 'password', id: 'passwordModal' },
        { name: 'delete', id: 'deleteModal' }
    ];

    modalConfigs.forEach(config => {
        // Open modal
        window.addEventListener(`open-${config.name}-modal`, () => {
            const modal = new bootstrap.Modal(document.getElementById(config.id));
            modal.show();
        });

        // Close modal
        window.addEventListener(`close-${config.name}-modal`, () => {
            const modalElement = document.getElementById(config.id);
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (modalInstance) {
                modalInstance.hide();
            }

            // Clean up backdrop
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

/* Role badge colors */
.bg-purple {
    background-color: #6f42c1 !important;
}

.bg-teal {
    background-color: #20c997 !important;
}

/* Table row hover effect */
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.04);
}

/* Modal styles */
.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}

/* Image preview */
.img-thumbnail.rounded-circle {
    border-radius: 50% !important;
    padding: 2px;
}

/* Badge styles */
.badge.bg-danger { background-color: #dc3545 !important; }
.badge.bg-primary { background-color: #0d6efd !important; }
.badge.bg-success { background-color: #198754 !important; }
.badge.bg-info { background-color: #0dcaf0 !important; color: #000; }
.badge.bg-warning { background-color: #ffc107 !important; color: #000; }
.badge.bg-secondary { background-color: #6c757d !important; }
.badge.bg-purple { background-color: #6f42c1 !important; }
.badge.bg-teal { background-color: #20c997 !important; }

/* Soft badge variants */
.badge-soft-success {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
}

.badge-soft-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}
</style>
