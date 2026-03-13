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
        'state_id',
        'country_id',
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

    protected $appends = ['logo_url', 'country_name', 'state_name', 'full_address'];

    /**
     * Get the state that the institute belongs to.
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the country that the institute belongs to.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the institute admin users.
     */
    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'institute-admin');
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
        return $this->country ? $this->country->name : 'India';
    }

    /**
     * Get the state name attribute.
     */
    public function getStateNameAttribute()
    {
        return $this->state ? $this->state->name : '-';
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
        if ($this->country_name) $parts[] = $this->country_name;
        if ($this->pincode) $parts[] = $this->pincode;

        return !empty($parts) ? implode(', ', $parts) : '-';
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
            return $query->where('state_id', $stateId);
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
