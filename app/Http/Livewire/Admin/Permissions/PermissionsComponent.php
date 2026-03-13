<?php

namespace App\Http\Livewire\Admin\Permissions;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Admin\PermissionGroup;
use App\Models\Admin\Permission; // FIXED: Use custom Permission model, not Spatie's
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PermissionsComponent extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    // Permission properties
    public $name, $display_name, $description, $guard_name = 'web';
    public $group_id, $module, $sort_order = 0;
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // UI state
    public $search = '';
    public $groupFilter = '';
    public $moduleFilter = '';
    public $sortField = 'group_id';
    public $sortDirection = 'asc';
    public $perPage = 20;

    // View mode
    public $viewMode = 'table'; // 'table' or 'grid'

    protected $listeners = [
        'editRecord' => 'edit',
        'deleteRecord' => 'confirmDelete',
        'refreshTable' => '$refresh',
    ];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255|unique:permissions,name,' . $this->recordId,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'guard_name' => 'required|string|in:web',
            'group_id' => 'nullable|exists:permission_groups,id',
            'module' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
        ];

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Permission name is required.',
        'name.unique' => 'This permission name already exists.',
        'display_name.required' => 'Display name is required.',
        'group_id.exists' => 'Selected group does not exist.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingGroupFilter()
    {
        $this->resetPage();
    }

    public function updatingModuleFilter()
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
        $this->dispatchBrowserEvent('open-permission-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $permission = Permission::with('group')->findOrFail($id);

        $this->fill([
            'recordId' => $permission->id,
            'name' => $permission->name,
            'display_name' => $permission->display_name,
            'description' => $permission->description,
            'guard_name' => $permission->guard_name,
            'group_id' => $permission->group_id,
            'module' => $permission->module,
            'sort_order' => $permission->sort_order,
        ]);

        $this->dispatchBrowserEvent('open-permission-modal');
    }

    public function closeModal()
    {
        $this->resetFields();
        $this->dispatchBrowserEvent('close-permission-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'display_name', 'description', 'guard_name',
            'group_id', 'module', 'sort_order', 'isEdit'
        ]);
        $this->guard_name = 'web';
        $this->sort_order = 0;
    }

    public function save()
    {
        $this->validate();

        Permission::create([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
            'group_id' => $this->group_id,
            'module' => $this->module,
            'sort_order' => $this->sort_order,
        ]);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Permission created successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $permission = Permission::findOrFail($this->recordId);

        $permission->update([
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'guard_name' => $this->guard_name,
            'group_id' => $this->group_id,
            'module' => $this->module,
            'sort_order' => $this->sort_order,
        ]);

        $this->closeModal();
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Permission updated successfully.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $permission = Permission::find($this->confirmingDeleteId);

        if ($permission) {
            // Check if permission is used by any roles
            if ($permission->roles()->count() > 0) {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'Cannot delete permission as it is assigned to roles.',
                ]);
                $this->confirmingDeleteId = null;
                $this->dispatchBrowserEvent('close-delete-modal');
                return;
            }

            $permission->delete();

            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'Permission deleted successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
        $this->emit('refreshTable');
    }

    public function getPermissionsProperty()
    {
        $query = Permission::with('group'); // Now this will work with custom model

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('display_name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhere('module', 'like', "%{$this->search}%");
            });
        }

        // Group filter
        if ($this->groupFilter) {
            $query->where('group_id', $this->groupFilter);
        }

        // Module filter
        if ($this->moduleFilter) {
            $query->where('module', $this->moduleFilter);
        }

        // Sort
        $query->orderBy($this->sortField, $this->sortDirection)
              ->orderBy('sort_order')
              ->orderBy('display_name');

        return $query->paginate($this->perPage);
    }

    public function getGroupsProperty()
    {
        return PermissionGroup::orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getModulesProperty()
    {
        return Permission::select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');
    }

    public function getGroupedPermissionsProperty()
    {
        if ($this->viewMode !== 'grid') {
            return collect();
        }

        return PermissionGroup::with(['permissions' => function ($query) {
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('display_name', 'like', "%{$this->search}%")
                      ->orWhere('description', 'like', "%{$this->search}%");
                });
            }
            $query->orderBy('sort_order')->orderBy('display_name');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get()
        ->filter(function ($group) {
            return $group->permissions->isNotEmpty();
        });
    }

    // Add this method to debug which model is being used (optional - remove after fixing)
    public function debug()
    {
        $permission = new Permission();
        dd([
            'class' => get_class($permission),
            'parent_class' => get_parent_class($permission),
            'traits' => class_uses($permission),
            'methods' => get_class_methods($permission),
            'group_relationship_exists' => method_exists($permission, 'group'),
        ]);
    }

    public function render()
    {
        // Uncomment this line to debug (remove after fixing)
        // $this->debug();

        return view('livewire.admin.permissions.permissions-component', [
            'permissions' => $this->permissions,
            'groups' => $this->groups,
            'modules' => $this->modules,
            'groupedPermissions' => $this->groupedPermissions,
        ])->layout('layouts.admin.master');
    }
}
