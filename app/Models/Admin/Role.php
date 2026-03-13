<?php

namespace App\Models\Admin;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'is_system'
    ];

    protected $casts = [
        'is_system' => 'boolean'
    ];

    /**
     * Get the users count attribute.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Get the permissions count attribute.
     */
    public function getPermissionsCountAttribute()
    {
        return $this->permissions()->count();
    }

    /**
     * Scope a query to exclude system roles.
     */
    public function scopeNonSystem($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope a query to include only system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }
}
