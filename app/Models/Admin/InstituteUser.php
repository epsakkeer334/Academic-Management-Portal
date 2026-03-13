<?php
// app/Models/Admin/InstituteUser.php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Models\Admin\Role;

class InstituteUser extends Pivot
{
    protected $table = 'institute_user';

    protected $fillable = [
        'institute_id',
        'user_id',
        'role',         // This stores the role ID as an integer
        'is_primary',
        'permissions',
        'status'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_primary' => 'boolean',
        'role' => 'integer', // Cast role to integer
    ];

    protected $appends = [
        'user_email',
        'role_name',
        'primary_badge',
        'status_badge',
        'role_badge',
        'formatted_created_at',
        'user_details'
    ];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    public function getUserEmailAttribute()
    {
        return $this->user ? $this->user->email : '-';
    }

    public function getRoleNameAttribute()
    {
        // If the role relationship is loaded and we have a role object
        if ($this->relationLoaded('role')) {
            if ($this->role instanceof Role) {
                return $this->role->name;
            }
        }

        // If we have a numeric role ID
        if ($this->role && is_numeric($this->role)) {
            $role = Role::find($this->role);
            return $role ? $role->name : '-';
        }

        return '-';
    }

    public function getPrimaryBadgeAttribute()
    {
        return $this->is_primary
            ? '<span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill"><i class="ti ti-star-filled me-1"></i> Yes</span>'
            : '<span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill">No</span>';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => '<span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill"><i class="ti ti-circle-check me-1"></i> Active</span>',
            'inactive' => '<span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill"><i class="ti ti-circle-x me-1"></i> Inactive</span>',
            'suspended' => '<span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill"><i class="ti ti-alert-triangle me-1"></i> Suspended</span>',
            default => '<span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill">-</span>'
        };
    }

    public function getRoleBadgeAttribute()
    {
        $roleName = $this->role_name;
        return '<span class="badge bg-info bg-opacity-10 text-info px-3 py-1 rounded-pill"><i class="ti ti-badge me-1"></i> ' . $roleName . '</span>';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y') : '-';
    }

    public function getUserDetailsAttribute()
    {
        if (!$this->user) {
            return '<div class="d-flex align-items-center gap-2">
                <span class="text-muted">No User</span>
            </div>';
        }

        $imageUrl = $this->user->user_image_url ?? asset('admin/assets/img/default-avatar.png');
        $userName = e($this->user->name);
        $userId = $this->user->id;

        return '<div class="d-flex align-items-center gap-2">
            <img src="' . $imageUrl . '"
                 alt="' . $userName . '"
                 class="rounded-circle"
                 style="width: 40px; height: 40px; object-fit: cover;">
            <div>
                <h6 class="mb-0">' . $userName . '</h6>
                <small class="text-muted">ID: ' . $userId . '</small>
            </div>
        </div>';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeByRole($query, $roleId)
    {
        if ($roleId) {
            return $query->where('role', $roleId);
        }
        return $query;
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeByInstitute($query, $instituteId)
    {
        if ($instituteId) {
            return $query->where('institute_id', $instituteId);
        }
        return $query;
    }
}
