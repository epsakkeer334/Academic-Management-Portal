<div class="content">
    <!-- Page Header -->
    <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
        <div class="my-auto mb-2">
            <h2 class="mb-1 fw-semibold">Permissions Management</h2>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                    <li class="breadcrumb-item">Admin</li>
                    <li class="breadcrumb-item">Roles & Permissions</li>
                    <li class="breadcrumb-item active">Permissions</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="button" wire:click="openModal" class="btn btn-primary d-flex align-items-center shadow-sm">
                <i class="ti ti-circle-plus me-2"></i> Add Permission
            </button>
        </div>
    </div>

    <!-- View Toggle and Filters -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="search-box">
                        <i class="ti ti-search search-icon"></i>
                        <input type="text" class="form-control ps-5" wire:model="search"
                               placeholder="Search permissions...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="groupFilter">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="moduleFilter">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module }}">{{ $module }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button wire:click="$set('viewMode', 'table')"
                                class="btn btn-outline-secondary {{ $viewMode === 'table' ? 'active' : '' }}"
                                data-bs-toggle="tooltip" title="Table View">
                            <i class="ti ti-table"></i>
                        </button>
                        <button wire:click="$set('viewMode', 'grid')"
                                class="btn btn-outline-secondary {{ $viewMode === 'grid' ? 'active' : '' }}"
                                data-bs-toggle="tooltip" title="Grid View">
                            <i class="ti ti-layout-grid"></i>
                        </button>
                        <button wire:click="$refresh" class="btn btn-outline-secondary"
                                data-bs-toggle="tooltip" title="Refresh">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table View -->
    @if($viewMode === 'table')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="fw-semibold mb-0">Permissions List</h5>
        </div>

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
                                Permission
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
                            <th wire:click="sortBy('group_id')" style="cursor: pointer;">
                                Group
                                @if($sortField === 'group_id')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('module')" style="cursor: pointer;">
                                Module
                                @if($sortField === 'module')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Description</th>
                            <th wire:click="sortBy('sort_order')" style="cursor: pointer; width: 100px;">
                                Order
                                @if($sortField === 'sort_order')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td class="text-center">{{ $permission->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $permission->display_name }}</div>
                                </td>
                                <td>
                                    <code>{{ $permission->name }}</code>
                                </td>
                                <td>
                                    @if($permission->group)
                                        <span class="badge bg-info">
                                            <i class="{{ $permission->group->icon }} me-1"></i>
                                            {{ $permission->group->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($permission->module)
                                        <span class="badge bg-secondary">{{ $permission->module }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $permission->description ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $permission->sort_order < 10 ? 'success' : ($permission->sort_order < 50 ? 'warning' : 'secondary') }}">
                                        {{ $permission->sort_order }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button wire:click="edit({{ $permission->id }})"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="tooltip"
                                                title="Edit Permission">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $permission->id }})"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip"
                                                title="Delete Permission"
                                                @if($permission->roles_count > 0) disabled @endif>
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="ti ti-shield-lock fs-2"></i><br>
                                    No permissions found
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
                Showing <strong>{{ $permissions->firstItem() ?? 0 }}</strong> to
                <strong>{{ $permissions->lastItem() ?? 0 }}</strong> of
                <strong>{{ $permissions->total() }}</strong> entries
            </div>
            <div>{{ $permissions->links() }}</div>
        </div>
    </div>
    @endif

    <!-- Grid View -->
    @if($viewMode === 'grid')
    <div class="row g-3">
        @forelse($groupedPermissions as $group)
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-light border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="{{ $group->icon }} fs-16 text-primary"></i>
                            <div>
                                <h6 class="fw-semibold mb-0">{{ $group->name }}</h6>
                                <small class="text-muted">{{ $group->permissions->count() }} permissions</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($group->permissions as $permission)
                                <div class="list-group-item px-3 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-medium">{{ $permission->display_name }}</div>
                                            <small class="text-muted d-block">
                                                <code>{{ $permission->name }}</code>
                                            </small>
                                            @if($permission->description)
                                                <small class="text-muted">{{ $permission->description }}</small>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button wire:click="edit({{ $permission->id }})"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="tooltip"
                                                    title="Edit Permission">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="ti ti-shield-lock fs-1 text-muted mb-3"></i>
                        <h5>No Permissions Found</h5>
                        <p class="text-muted">No permissions match your search criteria.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    @endif

    <!-- Permission Modal -->
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h4 class="modal-title fw-semibold">{{ $isEdit ? 'Edit Permission' : 'Create New Permission' }}</h4>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Display Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Display Name *</label>
                            <input type="text" class="form-control" wire:model="display_name"
                                   placeholder="e.g., View Dashboard">
                            <small class="text-muted">Human-readable name</small>
                            @error('display_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- System Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">System Name *</label>
                            <input type="text" class="form-control" wire:model="name"
                                   placeholder="e.g., view_dashboard">
                            <small class="text-muted">Unique identifier (lowercase, underscore)</small>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Permission Group -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Permission Group</label>
                            <select class="form-select" wire:model="group_id">
                                <option value="">Select Group</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Group for better organization</small>
                            @error('group_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Module -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Module</label>
                            <input type="text" class="form-control" wire:model="module"
                                   placeholder="e.g., Dashboard, Users, Reports">
                            <small class="text-muted">Module name (optional)</small>
                            @error('module') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Sort Order</label>
                            <input type="number" class="form-control" wire:model="sort_order" min="0" value="0">
                            <small class="text-muted">Lower numbers appear first</small>
                            @error('sort_order') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Guard (hidden) -->
                        <div class="col-md-6">
                            <input type="hidden" wire:model="guard_name" value="web">
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Description</label>
                            <textarea class="form-control" wire:model="description" rows="3"
                                      placeholder="Describe what this permission allows..."></textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <!-- Naming Convention Info -->
                        <div class="col-12">
                            <div class="alert alert-info py-2 small">
                                <div class="d-flex align-items-start">
                                    <i class="ti ti-info-circle me-2 mt-1"></i>
                                    <div>
                                        <p class="mb-1 fw-medium">Permission Naming Convention:</p>
                                        <ul class="mb-0 ps-3">
                                            <li>Use <strong>action_resource</strong> format (e.g., view_users, create_roles)</li>
                                            <li>Common actions: view, create, edit, delete, approve, export</li>
                                            <li>Keep it lowercase with underscores</li>
                                            <li>Be specific and consistent</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-12">
                            <label class="form-label fw-medium">Common Permission Patterns</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="$set('name', 'view_')"
                                        onclick="this.nextElementSibling.focus()">
                                    view_
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="$set('name', 'create_')">
                                    create_
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="$set('name', 'edit_')">
                                    edit_
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="$set('name', 'delete_')">
                                    delete_
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="$set('name', 'approve_')">
                                    approve_
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                        wire:click="$set('name', 'export_')">
                                    export_
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        wire:click="$set('name', '')">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light px-4" wire:click="closeModal">Cancel</button>
                    <button type="button" class="btn btn-primary px-4" onclick="savePermission()">
                        <i class="ti ti-device-floppy me-1"></i> {{ $isEdit ? 'Update Permission' : 'Create Permission' }}
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
                    <p class="text-muted mb-4">Are you sure you want to delete this permission? This action cannot be undone.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger px-4" wire:click="delete">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary bg-opacity-10 rounded p-3 me-3">
                            <i class="ti ti-shield-lock text-warning fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Permissions</h6>
                            <h4 class="mb-0">{{ \Spatie\Permission\Models\Permission::count() }}</h4>
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
                            <i class="ti ti-folders text-success fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Permission Groups</h6>
                            <h4 class="mb-0">{{ $groups->count() }}</h4>
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
                            <i class="ti ti-users text-info fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Roles</h6>
                            <h4 class="mb-0">{{ \Spatie\Permission\Models\Role::count() }}</h4>
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
                            <i class="ti ti-shield-lock text-info fs-16"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Modules</h6>
                            <h4 class="mb-0">{{ $modules->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('livewire:load', function () {
    // Save permission function
    window.savePermission = function() {
        if (@this.isEdit) {
            @this.call('update');
        } else {
            @this.call('save');
        }
    }

    // Modal management
    const modalConfigs = [
        { name: 'permission', id: 'permissionModal' },
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

// Clean up on Livewire update
document.addEventListener('livewire:update', function () {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(element => {
        const tooltip = bootstrap.Tooltip.getInstance(element);
        if (tooltip) {
            tooltip.dispose();
        }
    });
});
</script>

<!-- Additional CSS -->
<style>
.search-box {
    position: relative;
}

.search-box .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 1.1rem;
    z-index: 10;
}

.search-box .form-control {
    padding-left: 40px;
}

.avatar-sm {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.list-group-item {
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.btn-outline-secondary.active {
    background-color: #e9ecef;
    color: #000;
    border-color: #ced4da;
}

/* Grid view card styles */
.card-header {
    background-color: #f8f9fa;
}

/* Badge colors */
.badge.bg-info {
    background-color: #0dcaf0 !important;
    color: #000;
}

.badge.bg-success {
    background-color: #198754 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
}

/* Code styling */
code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 4px;
    font-size: 0.875rem;
    color: #d63384;
}
</style>
