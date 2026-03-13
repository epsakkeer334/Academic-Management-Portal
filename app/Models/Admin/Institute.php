<?php

namespace App\Models\Admin;

use App\Models\User;

class Institute extends BaseModel
{
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'state',        // This stores the state ID
        'country',       // This stores the country ID
        'pincode',
        'logo',
        'website',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = ['logo_url', 'country_name', 'state_name', 'full_address', 'users_count'];

    /**
     * Get the state that the institute belongs to.
     * Note: The foreign key is 'state' (not 'state_id')
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    /**
     * Get the country that the institute belongs to.
     * Note: The foreign key is 'country' (not 'country_id')
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }

    /**
     * Get the institute admin users.
     */
    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'institute-admin');
    }

    /**
     * Get the users assigned to this institute.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'institute_user')
                    ->withPivot('role', 'is_primary', 'permissions', 'status')
                    ->withTimestamps();
    }

    /**
     * Get the primary users of the institute.
     */
    public function primaryUsers()
    {
        return $this->belongsToMany(User::class, 'institute_user')
                    ->withPivot('role', 'is_primary', 'permissions', 'status')
                    ->wherePivot('is_primary', true)
                    ->withTimestamps();
    }

    /**
     * Get the logo URL attribute.
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/institutes/' . $this->logo);
        }
        return asset('admin/assets/img/default-institute.png');
    }

    /**
     * Get the country name attribute.
     */
    public function getCountryNameAttribute()
    {
        // Check if relationship is loaded and exists
        if ($this->relationLoaded('country') && $this->country) {
            return $this->country->name;
        }

        // If not loaded, try to load it or return default
        if ($this->country) {
            $country = Country::find($this->country);
            return $country ? $country->name : 'India';
        }

        return 'India';
    }

    /**
     * Get the state name attribute.
     */
    public function getStateNameAttribute()
    {
        // Check if relationship is loaded and exists
        if ($this->relationLoaded('state') && $this->state) {
            return $this->state->name;
        }

        // If not loaded, try to load it or return default
        if ($this->state) {
            $state = State::find($this->state);
            return $state ? $state->name : '-';
        }

        return '-';
    }

    /**
     * Get full address attribute.
     */
    public function getFullAddressAttribute()
    {
        $parts = [];
        if ($this->address) $parts[] = $this->address;
        if ($this->city) $parts[] = $this->city;
        if ($this->state_name && $this->state_name !== '-') $parts[] = $this->state_name;
        if ($this->country_name && $this->country_name !== 'India') $parts[] = $this->country_name;
        if ($this->pincode) $parts[] = $this->pincode;

        return !empty($parts) ? implode(', ', $parts) : '-';
    }

    /**
     * Get users count attribute.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->wherePivot('status', 'active')->count();
    }

    /**
     * Get formatted created at.
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('d M Y') : '-';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->status ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Scope a query to search institutes.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope a query to filter by state.
     */
    public function scopeByState($query, $stateId)
    {
        if ($stateId) {
            return $query->where('state', $stateId);
        }
        return $query;
    }

    /**
     * Scope a query to filter by country (default India).
     */
    public function scopeByCountry($query, $countryId = null)
    {
        if ($countryId) {
            return $query->where('country', $countryId);
        }
        // Default to India (assuming India has ID 1)
        return $query->where('country', 1);
    }
}
