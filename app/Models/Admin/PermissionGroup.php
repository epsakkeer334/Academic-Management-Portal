<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'group_id');
    }

    public function getIconAttribute($value)
    {
        return $value ?? 'ti ti-shield';
    }
}
