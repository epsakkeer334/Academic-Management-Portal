<?php

namespace App\Models\Admin;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Models\Admin\PermissionGroup;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'group_id',
        'module',
        'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    /**
     * Get the group that owns the permission.
     */
    public function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'group_id');
    }

    /**
     * Get the module name with fallback
     */
    public function getModuleAttribute($value)
    {
        return $value ?? ($this->group ? $this->group->name : 'General');
    }

    /**
     * Get icon from group or default
     */
    public function getIconAttribute()
    {
        return $this->group ? $this->group->icon : 'ti ti-shield';
    }

    /**
     * Scope a query to filter by module.
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeInGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope a query to order by group and sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('group_id')->orderBy('sort_order')->orderBy('display_name');
    }
}
