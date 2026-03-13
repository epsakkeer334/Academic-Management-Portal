<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Admin\Institute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'role',
        'user_image',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the profile photo URL.
     */
    public function getUserImageUrlAttribute()
    {
        if ($this->user_image) {
            return asset('storage/users/' . $this->user_image);
        }
        return asset('admin/assets/img/default-avatar.png');
    }

    /**
     * Get the user's role badge class.
     */
    public function getRoleBadgeClassAttribute()
    {
        return match($this->role) {
            'super-admin' => 'bg-danger',
            'institute-admin' => 'bg-primary',
            'accounts' => 'bg-success',
            'training-manager' => 'bg-info',
            'hot' => 'bg-warning',
            'bic' => 'bg-secondary',
            'faculty' => 'bg-purple',
            'student' => 'bg-teal',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the user's status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->status ? 'bg-success' : 'bg-danger';
    }

    /**
     * Get the user's status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Get formatted last login.
     */
    public function getFormattedLastLoginAttribute()
    {
        if (!$this->last_login_at) {
            return 'Never logged in';
        }

        return $this->last_login_at->diffForHumans() . ' (' .
               $this->last_login_at->format('d M Y, h:i A') . ')';
    }

    /**
     * Check if user is online (within last 15 minutes).
     */
    public function getIsOnlineAttribute()
    {
        return $this->last_login_at && $this->last_login_at->gt(now()->subMinutes(15));
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope a query to filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to get online users.
     */
    public function scopeOnline($query)
    {
        return $query->where('last_login_at', '>', now()->subMinutes(15));
    }

    /**
     * Scope a query to get recently logged in users.
     */
    public function scopeRecentlyLoggedIn($query, $days = 7)
    {
        return $query->where('last_login_at', '>', now()->subDays($days));
    }

    public function institutes()
    {
        return $this->belongsToMany(Institute::class, 'institute_user')
                    ->withPivot('role', 'is_primary', 'permissions', 'status')
                    ->withTimestamps();
    }

    /**
     * Get the primary institute for the user.
     */
    public function primaryInstitute()
    {
        return $this->belongsToMany(Institute::class, 'institute_user')
                    ->withPivot('role', 'is_primary', 'permissions', 'status')
                    ->wherePivot('is_primary', true)
                    ->withTimestamps();
    }
}
