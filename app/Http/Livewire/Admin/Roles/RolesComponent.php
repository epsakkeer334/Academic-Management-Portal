<?php

namespace App\Http\Livewire\Admin\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\PermissionGroup;
use App\Models\Admin\Role; // Use custom Role model
use App\Models\Admin\Permission; // Use custom Permission model
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RolesComponent extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    // Role properties
    public $name, $display_name, $description, $guard_name = 'web';
    public $selectedPermissions = [];
    public $recordId;
    public $isEdit = false;

    // UI state
    public $search = '';
    public $filter = 'All';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $confirmingDeleteId = null;

    // Select all functionality
    public $selectAll = false;
    public $selectedGroup = null;
    public $expandedGroups = [];

    protected $listeners = [
        'editRecord' => 'edit',
        'deleteRecord' => 'confirmDelete',
        'refreshTable' => '$refresh',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->recordId,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|in:web',
            'selectedPermissions' => 'array',
        ];
    }

    protected $messages = [
        'name.required' => 'Role name is required.',
        'name.unique' => 'This role name already exists.',
        'display_name.required' => 'Display name is required.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->isEdit = false;
        $this->dispatchBrowserEvent('open-role-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $role = Role::with('permissions')->findOrFail($id);

        $this->fill([
            'recordId' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name,
            'description' => $role->description,
            'guard_name' => $role->guard_name,
            'selectedPermissions' => $role->permissions->pluck('name')->toArray(),
        ]);

        $this->dispatchBrowserEvent('open-role-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-role-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'display_name', 'description', 'guard_name',
            'selectedPermissions', 'isEdit', 'selectAll', 'selectedGroup'
        ]);
        $this->guard_name = 'web';
    }

    public function save()
    {
        $this->validate();

        $role = Role::create([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
        ]);

        if (!empty($this->selectedPermissions)) {
            $role->syncPermissions($this->selectedPermissions);
        }

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Role created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $role = Role::findOrFail($this->recordId);

        // Prevent editing system roles
        if ($role->is_system) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'System roles cannot be modified.',
            ]);
            return;
        }

        $role->update([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
        ]);

        if (!empty($this->selectedPermissions)) {
            $role->syncPermissions($this->selectedPermissions);
        } else {
            $role->syncPermissions([]);
        }

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Role updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $role = Role::find($id);

        if ($role && $role->is_system) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'System roles cannot be deleted.',
            ]);
            return;
        }

        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $role = Role::find($this->confirmingDeleteId);

        if ($role && !$role->is_system) {
            $role->delete();

            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'Role deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
        $this->emit('refreshTable');
    }

    public function toggleGroup($groupId)
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupId]);
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    public function selectGroupPermissions($groupId, $select)
    {
        $group = PermissionGroup::with('permissions')->find($groupId);

        if ($group && $group->permissions) {
            $permissionNames = $group->permissions->pluck('name')->toArray();

            if ($select) {
                $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $permissionNames));
            } else {
                $this->selectedPermissions = array_diff($this->selectedPermissions, $permissionNames);
            }
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedPermissions = Permission::pluck('name')->toArray();
        } else {
            $this->selectedPermissions = [];
        }
    }

    public function getRolesProperty()
    {
        $query = Role::query()->withCount('users');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('display_name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getPermissionGroupsProperty()
    {
        return PermissionGroup::with(['permissions' => function ($query) {
            $query->orderBy('sort_order')->orderBy('display_name');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
    }

    public function render()
    {
        return view('livewire.admin.roles.roles-component', [
            'roles' => $this->roles,
            'permissionGroups' => $this->permissionGroups,
        ])->layout('layouts.admin.master');
    }
}
