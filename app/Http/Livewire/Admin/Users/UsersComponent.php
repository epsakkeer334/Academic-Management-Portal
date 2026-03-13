<?php

namespace App\Http\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class UsersComponent extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    // User properties
    public $name, $email, $phone, $password, $password_confirmation;
    public $role = 'student';
    public $user_image;
    public $status = 1;
    public $recordId;
    public $isEdit = false;
    public $confirmingDeleteId = null;

    // Password change modal
    public $showPasswordModal = false;
    public $passwordUserId;
    public $newPassword;
    public $newPasswordConfirmation;

    // UI state
    public $search = '';
    public $roleFilter = '';
    public $filter = 'All';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Available roles from your system
    public $roles = [
        'super-admin' => 'Super Admin',
        'institute-admin' => 'Institute Admin',
        'accounts' => 'Accounts',
        'training-manager' => 'Training Manager',
        'hot' => 'Head of Training',
        'bic' => 'Base In-Charge',
        'faculty' => 'Faculty',
        'student' => 'Student',
    ];

    protected $listeners = [
        'editRecord' => 'edit',
        'deleteRecord' => 'confirmDelete',
        'refreshTable' => '$refresh',
        'openChangePasswordModal' => 'openChangePasswordModal',
    ];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->recordId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users')->ignore($this->recordId),
            ],
            'role' => ['required', 'string', 'max:50'],
            'status' => 'boolean',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        // Add password rules only for new users
        if (!$this->isEdit) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()];
            $rules['password_confirmation'] = 'required';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already registered.',
            'phone.unique' => 'This phone number is already registered.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Please select a role.',
            'role.max' => 'Role must not exceed 50 characters.',
            'user_image.max' => 'Profile photo size must not exceed 2MB.',
            'user_image.image' => 'File must be an image.',
            'user_image.mimes' => 'Profile photo must be a jpeg, png, jpg, gif, or webp.',
        ];
    }

    // Custom validation to ensure role exists in roles array
    protected function validateRole()
    {
        if (!array_key_exists($this->role, $this->roles)) {
            $this->addError('role', 'Invalid role selected.');
            return false;
        }
        return true;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'role') {
            $this->validateRole();
        } else {
            $this->validateOnly($propertyName);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
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
        $this->dispatchBrowserEvent('open-user-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->isEdit = true;

        try {
            $user = User::findOrFail($id);

            $this->fill([
                'recordId' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role ?? 'student',
                'status' => $user->status,
            ]);

            $this->dispatchBrowserEvent('open-user-modal');
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'User not found.',
            ]);
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetFields();
        $this->dispatchBrowserEvent('close-user-modal');
    }

    protected function resetFields()
    {
        $this->reset([
            'recordId', 'name', 'email', 'phone', 'password',
            'password_confirmation', 'role', 'status', 'user_image', 'isEdit'
        ]);
        $this->role = 'student';
        $this->status = 1;
    }

    public function save()
    {
        // Validate role separately
        if (!$this->validateRole()) {
            return;
        }

        $this->validate();

        try {
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role, // This will be a string value
                'status' => $this->status,
                'password' => Hash::make($this->password),
            ];

            // Handle profile photo upload
            if ($this->user_image) {
                $photoName = time() . '_' . uniqid() . '.' . $this->user_image->getClientOriginalExtension();
                $this->user_image->storeAs('public/users', $photoName);
                $data['user_image'] = $photoName;
            }

            // Debug: Log the data before insert
            \Log::info('Creating user with data:', $data);

            User::create($data);

            $this->closeModal();
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'User created successfully.',
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'Duplicate entry. This email or phone number is already registered.',
                ]);
            } else {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'Database error occurred while creating the user: ' . $e->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    public function update()
    {
        // Validate role separately
        if (!$this->validateRole()) {
            return;
        }

        $this->validate();

        try {
            $user = User::findOrFail($this->recordId);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role, // This will be a string value
                'status' => $this->status,
            ];

            // Handle profile photo upload
            if ($this->user_image) {
                // Delete old photo
                if ($user->user_image) {
                    Storage::delete('public/users/' . $user->user_image);
                }

                $photoName = time() . '_' . uniqid() . '.' . $this->user_image->getClientOriginalExtension();
                $this->user_image->storeAs('public/users', $photoName);
                $data['user_image'] = $photoName;
            }

            // Debug: Log the update data
            \Log::info('Updating user:', ['id' => $this->recordId, 'data' => $data]);

            // Make sure role is a string and properly quoted
            $data['role'] = (string) $data['role'];

            $user->update($data);

            $this->closeModal();
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'success',
                'message' => 'User updated successfully.',
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'Duplicate entry. This email or phone number is already registered by another user.',
                ]);
            } else {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'Database error occurred while updating the user: ' . $e->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        }
    }

    public function confirmDelete($id)
    {
        // Prevent deleting yourself
        if ($id == auth()->id()) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'You cannot delete your own account.',
            ]);
            return;
        }

        $this->confirmingDeleteId = $id;
        $this->dispatchBrowserEvent('open-delete-modal');
    }

    public function delete()
    {
        try {
            $user = User::find($this->confirmingDeleteId);

            if ($user) {
                // Delete profile photo if exists
                if ($user->user_image) {
                    Storage::delete('public/users/' . $user->user_image);
                }

                $user->delete();

                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'success',
                    'message' => 'User deleted successfully.',
                ]);
            }

            $this->confirmingDeleteId = null;
            $this->dispatchBrowserEvent('close-delete-modal');
            $this->emit('refreshTable');

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'An error occurred while deleting the user.',
            ]);
        }
    }

    // Password Change Methods
    public function openChangePasswordModal($id)
    {
        $this->resetValidation();
        $this->resetPasswordFields();
        $this->passwordUserId = $id;
        $this->showPasswordModal = true;
        $this->dispatchBrowserEvent('open-password-modal');
    }

    public function closePasswordModal()
    {
        $this->resetValidation();
        $this->resetPasswordFields();
        $this->showPasswordModal = false;
        $this->dispatchBrowserEvent('close-password-modal');
    }

    protected function resetPasswordFields()
    {
        $this->reset(['newPassword', 'newPasswordConfirmation', 'passwordUserId']);
    }

    public function updatePassword()
    {
        $this->validate([
            'newPassword' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'newPasswordConfirmation' => 'required',
        ]);

        try {
            $user = User::find($this->passwordUserId);

            if ($user) {
                $user->update([
                    'password' => Hash::make($this->newPassword)
                ]);

                $this->closePasswordModal();
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'success',
                    'message' => 'Password updated successfully for ' . $user->name,
                ]);
            } else {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'User not found.',
                ]);
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'An error occurred while updating the password.',
            ]);
        }
    }

    // Toggle user status
    public function toggleStatus($id)
    {
        try {
            $user = User::find($id);

            if ($user && $id != auth()->id()) {
                $user->update([
                    'status' => !$user->status
                ]);

                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'success',
                    'message' => 'User status updated successfully.',
                ]);
            } elseif ($id == auth()->id()) {
                $this->dispatchBrowserEvent('show-toast', [
                    'type' => 'error',
                    'message' => 'You cannot change your own status.',
                ]);
            }

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('show-toast', [
                'type' => 'error',
                'message' => 'An error occurred while updating status.',
            ]);
        }
    }

    // Get users with filters
    public function getUsersProperty()
    {
        $query = User::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        // Role filter
        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        // Status filter
        if ($this->filter !== 'All') {
            $query->where('status', $this->filter === 'Active' ? 1 : 0);
        }

        // Sort
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    // Get counts for stats
    public function getStatsProperty()
    {
        return [
            'total' => User::count(),
            'active' => User::where('status', true)->count(),
            'inactive' => User::where('status', false)->count(),
            'byRole' => collect($this->roles)->mapWithKeys(function ($label, $role) {
                return [$role => User::where('role', $role)->count()];
            }),
        ];
    }

    public function render()
    {
        return view('livewire.admin.users.users-component', [
            'users' => $this->users,
            'stats' => $this->stats,
        ])->layout('layouts.admin.master');
    }
}
