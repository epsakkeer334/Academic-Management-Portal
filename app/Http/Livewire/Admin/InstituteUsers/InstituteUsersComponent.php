<?php
// app/Http/Livewire/Admin/InstituteUsers/InstituteUsersComponent.php

namespace App\Http\Livewire\Admin\InstituteUsers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\Institute;
use App\Models\User;
use App\Models\Admin\InstituteUser;
use App\Models\Admin\Role;

class InstituteUsersComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $institute;
    public $instituteId;

    // For adding/editing
    public $userId;
    public $roleId;
    public $isPrimary = false;
    public $status = 'active';
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // For DataTable integration
    public $roleFilter = '';
    public $stats = [];

    protected $listeners = [
        'editRecord' => 'edit',
        'deleteRecord' => 'confirmDelete',
        'refresh' => '$refresh',
        'refreshTable' => '$refresh',
    ];

    protected function rules()
    {
        return [
            'userId' => 'required|exists:users,id',
            'roleId' => 'required|exists:roles,id',
            'isPrimary' => 'boolean',
            'status' => 'required|in:active,inactive,suspended',
        ];
    }

    protected $messages = [
        'userId.required' => 'Please select a user.',
        'userId.exists' => 'Selected user does not exist.',
        'roleId.required' => 'Please select a role.',
        'roleId.exists' => 'Selected role does not exist.',
        'status.required' => 'Status is required.',
        'status.in' => 'Invalid status selected.',
    ];

    public function mount($instituteId)
    {
        $this->instituteId = $instituteId;
        $this->institute = Institute::findOrFail($instituteId);
        $this->loadStats();
    }

    protected function loadStats()
    {
        $this->stats = [
            'total' => InstituteUser::where('institute_id', $this->instituteId)->count(),
            'active' => InstituteUser::where('institute_id', $this->instituteId)->where('status', 'active')->count(),
            'inactive' => InstituteUser::where('institute_id', $this->instituteId)->where('status', 'inactive')->count(),
            'suspended' => InstituteUser::where('institute_id', $this->instituteId)->where('status', 'suspended')->count(),
        ];
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->isEdit = false;
        $this->dispatchBrowserEvent('open-institute-user-modal');
    }

    public function edit($pivotId)
    {
        $this->resetValidation();
        $this->isEdit = true;

        $pivot = InstituteUser::where('institute_id', $this->instituteId)
                    ->with('user')
                    ->where('id', $pivotId)
                    ->firstOrFail();

        $this->fill([
            'recordId' => $pivot->id,
            'userId' => $pivot->user_id,
            'roleId' => $pivot->role,
            'isPrimary' => $pivot->is_primary,
            'status' => $pivot->status,
        ]);

        $this->dispatchBrowserEvent('open-institute-user-modal');
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->dispatchBrowserEvent('close-institute-user-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'userId', 'roleId', 'isPrimary', 'status', 'isEdit'
        ]);
        $this->roleId = null;
        $this->isPrimary = false;
        $this->status = 'active';
    }

    public function save()
    {
        $this->validate();

        // Check if user already assigned to this institute
        $exists = InstituteUser::where('institute_id', $this->instituteId)
                    ->where('user_id', $this->userId)
                    ->exists();

        if ($exists) {
            $this->addError('userId', 'This user is already assigned to this institute.');
            return;
        }

        InstituteUser::create([
            'institute_id' => $this->instituteId,
            'user_id' => $this->userId,
            'role' => $this->roleId,
            'is_primary' => $this->isPrimary,
            'status' => $this->status,
        ]);

        $this->closeModal();
        $this->loadStats();
        $this->emit('refreshTable');
        $this->emit('refresh');

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'User assigned to institute successfully.',
        ]);
    }

    public function update()
    {
        $this->validate();

        $pivot = InstituteUser::where('institute_id', $this->instituteId)
                    ->where('id', $this->recordId)
                    ->firstOrFail();

        // Check if another record exists with same user (excluding current)
        $exists = InstituteUser::where('institute_id', $this->instituteId)
                    ->where('user_id', $this->userId)
                    ->where('id', '!=', $this->recordId)
                    ->exists();

        if ($exists) {
            $this->addError('userId', 'This user is already assigned to this institute.');
            return;
        }

        $pivot->update([
            'user_id' => $this->userId,
            'role' => $this->roleId,
            'is_primary' => $this->isPrimary,
            'status' => $this->status,
        ]);

        $this->closeModal();
        $this->loadStats();
        $this->emit('refreshTable');
        $this->emit('refresh');

        $this->dispatchBrowserEvent('show-toast', [
            'type' => 'success',
            'message' => 'Assignment updated successfully.',
        ]);
    }

    public function confirmDelete($pivotId)
    {
        $this->confirmingDeleteId = $pivotId;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        $pivot = InstituteUser::where('institute_id', $this->instituteId)
                    ->where('id', $this->confirmingDeleteId)
                    ->first();

        if ($pivot) {
            $pivot->delete();
            $this->loadStats();
            $this->emit('refreshTable');
            $this->emit('refresh');

            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'User removed from institute successfully.',
            ]);
        }

        $this->confirmingDeleteId = null;
        $this->dispatchBrowserEvent('close-delete-modal');
    }

    public function toggleStatus($pivotId)
    {
        $pivot = InstituteUser::where('institute_id', $this->instituteId)
                    ->where('id', $pivotId)
                    ->first();

        if ($pivot) {
            $newStatus = match($pivot->status) {
                'active' => 'inactive',
                'inactive' => 'active',
                default => 'active'
            };
            $pivot->update(['status' => $newStatus]);

            $this->loadStats();
            $this->emit('refreshTable');
            $this->emit('refresh');

            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'User status updated to ' . ucfirst($newStatus) . '.',
            ]);
        }
    }

    public function getAvailableUsersProperty()
    {
        $assignedUserIds = InstituteUser::where('institute_id', $this->instituteId)
                            ->pluck('user_id');

        return User::whereNotIn('id', $assignedUserIds)
                    ->where('status', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'email']);
    }

    public function getRolesProperty()
    {
        return Role::orderBy('name')->get(['id', 'name']);
    }

    public function render()
    {
        return view('livewire.admin.institute-users.institute-users-component', [
            'availableUsers' => $this->availableUsers,
            'roles' => $this->roles,
            'stats' => $this->stats,
        ])->layout('layouts.admin.master');
    }
}
