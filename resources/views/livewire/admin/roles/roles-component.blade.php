<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Role & Permission Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item active">Roles & Permissions</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Role
            </button>
            <a href="javascript:void(0);" class="btn btn-outline-light border d-flex align-items-center justify-content-center p-2"
               data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse Header" id="collapse-header">
                <i class="ti ti-chevrons-up text-secondary"></i>
            </a>
        </div>
    </div>

    <!-- Roles List Card -->
    <div class="card shadow-sm border-0">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <h5 class="fw-semibold mb-0">Roles List</h5>
                <div class="d-flex gap-2">
                    <div class="search-input">
                        <input type="text" class="form-control" wire:model="search" placeholder="Search roles...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('id')" style="cursor: pointer; width: 80px;">
                                ID
                                @if($sortField === 'id')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('display_name')" style="cursor: pointer;">
                                Role
                                @if($sortField === 'display_name')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                System Name
                                @if($sortField === 'name')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Type</th>
                            <th>Users</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="text-center">{{ $role->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $role->display_name }}</div>
                                </td>
                                <td>
                                    <code>{{ $role->name }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $role->description ?? '-' }}</small>
                                </td>
                                <td>
                                    @php
                                        $permissionCount = $role->permissions->count();
                                    @endphp
                                    <span class="badge bg-info">
                                        {{ $permissionCount }} permission{{ $permissionCount !== 1 ? 's' : '' }}
                                    </span>
                                    @if($permissionCount > 0)
                                        <button type="button"
                                                class="btn btn-sm btn-link p-0 ms-2"
                                                data-bs-toggle="popover"
                                                data-bs-trigger="hover focus"
                                                title="Permissions"
                                                data-bs-content="{{ $role->permissions->pluck('display_name')->implode(', ') }}">
                                            <i class="ti ti-info-circle"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    @if($role->is_system)
                                        <span class="badge bg-warning">System</span>
                                    @else
                                        <span class="badge bg-success">Custom</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $role->users_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button wire:click="edit({{ $role->id }})"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="tooltip"
                                                title="Edit Role"
                                                {{ $role->is_system ? 'disabled' : '' }}>
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $role->id }})"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip"
                                                title="Delete Role"
                                                {{ $role->is_system ? 'disabled' : '' }}>
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="ti ti-shield fs-2"></i><br>
                                    No roles found
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
                Showing <strong>{{ $roles->firstItem() ?? 0 }}</strong> to
                <strong>{{ $roles->lastItem() ?? 0 }}</strong> of
                <strong>{{ $roles->total() }}</strong> entries
            </div>
            <div>{{ $roles->links() }}</div>
        </div>
    </div>

    <!-- Role Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Role' : 'Create New Role' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Role Details Column -->
                        <div class="col-md-4 border-end">
                            <h6 class="fw-semibold mb-3">Role Details</h6>

                            <!-- Display Name -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Display Name *</label>
                                <input type="text" class="form-control" wire:model="display_name"
                                       placeholder="e.g., Institute Admin">
                                @error('display_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- System Name -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">System Name *</label>
                                <input type="text" class="form-control" wire:model="name"
                                       placeholder="e.g., institute-admin">
                                <small class="text-muted">Unique identifier (lowercase, hyphenated)</small>
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Description</label>
                                <textarea class="form-control" wire:model="description" rows="3"
                                          placeholder="Describe the role's purpose..."></textarea>
                                @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Guard (hidden usually) -->
                            <input type="hidden" wire:model="guard_name" value="web">

                            <div class="alert alert-info mt-3">
                                <i class="ti ti-info-circle me-2"></i>
                                <small>Permissions define what actions users with this role can perform.</small>
                            </div>
                        </div>

                        <!-- Permissions Column -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-semibold mb-0">Assign Permissions</h6>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input"
                                           wire:model="selectAll" id="selectAll">
                                    <label class="form-check-label" for="selectAll">
                                        Select All Permissions
                                    </label>
                                </div>
                            </div>

                            <!-- Permissions List -->
                            <div class="permissions-container" style="max-height: 500px; overflow-y: auto;">
                                @foreach($permissionGroups as $group)
                                    <div class="card mb-3 border">
                                        <div class="card-header bg-light py-2 px-3"
                                             style="cursor: pointer;"
                                             wire:click="toggleGroup({{ $group->id }})">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="{{ $group->icon }}"></i>
                                                    <span class="fw-semibold">{{ $group->name }}</span>
                                                    <span class="badge bg-secondary">{{ $group->permissions->count() }}</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="form-check me-2" onclick="event.stopPropagation()">
                                                        <input type="checkbox"
                                                               class="form-check-input"
                                                               wire:click="selectGroupPermissions({{ $group->id }}, $event.target.checked)"
                                                               {{ $group->permissions->pluck('name')->every(fn($permission) => in_array($permission, $selectedPermissions)) ? 'checked' : '' }}>
                                                    </div>
                                                    <i class="ti ti-chevron-{{ in_array($group->id, $expandedGroups) ? 'up' : 'down' }}"></i>
                                                </div>
                                            </div>
                                        </div>

                                        @if(in_array($group->id, $expandedGroups))
                                            <div class="card-body p-3">
                                                <div class="row g-3">
                                                    @foreach($group->permissions->sortBy('display_name') as $permission)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                       class="form-check-input"
                                                                       wire:model="selectedPermissions"
                                                                       value="{{ $permission->name }}"
                                                                       id="perm_{{ $permission->id }}">
                                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                    {{ $permission->display_name }}
                                                                    <small class="text-muted d-block">{{ $permission->name }}</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Selected Count -->
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted">
                                    Selected: <span class="fw-semibold">{{ count($selectedPermissions) }}</span> permissions
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="saveRole()">
                        <i class="ti ti-device-floppy me-1"></i> {{ $isEdit ? 'Update Role' : 'Create Role' }}
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
                    <p class="text-muted mb-4">Are you sure you want to delete this role? This action cannot be undone.</p>
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
    // Save role function
    window.saveRole = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'role', id: 'roleModal' },
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

    // Initialize popovers
    const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
    popoverElements.forEach(element => {
        new bootstrap.Popover(element, {
            html: true,
            placement: 'top'
        });
    });

    // Initialize tooltips
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(element => {
        new bootstrap.Tooltip(element);
    });
});

// Clean up popovers on Livewire updates
document.addEventListener('livewire:update', function () {
    const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');
    popovers.forEach(element => {
        const popover = bootstrap.Popover.getInstance(element);
        if (popover) {
            popover.dispose();
        }
    });
});
</script>

<!-- Additional CSS -->
<style>
.permissions-container::-webkit-scrollbar {
    width: 6px;
}

.permissions-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.permissions-container::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.permissions-container::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.card-header {
    transition: background-color 0.2s;
}

.card-header:hover {
    background-color: #e9ecef !important;
}

/* Badge styles */
.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}
</style>
