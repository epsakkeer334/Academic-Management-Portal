<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mou extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'title',
        'practical_accessor_name',
        'scope_of_training',
        'validity_start',
        'validity_end',
        'institute_id',
        'status',
        'termination_reason',
        'renewal_alert_sent',
        'renewal_alert_sent_at',
        'renewal_initiated_by',
        'renewal_initiated_at',
        'created_by',
    ];

    protected $casts = [
        'validity_start' => 'date',
        'validity_end' => 'date',
        'renewal_alert_sent' => 'boolean',
        'renewal_alert_sent_at' => 'datetime',
        'renewal_initiated_at' => 'datetime',
    ];

    protected $appends = ['status_badge', 'days_to_expiry', 'is_expiring_soon'];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function renewalInitiatedBy()
    {
        return $this->belongsTo(User::class, 'renewal_initiated_by');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'expired' => 'bg-danger',
            'terminated' => 'bg-secondary',
            'renewal_pending' => 'bg-warning',
            default => 'bg-info'
        };
    }

    public function getDaysToExpiryAttribute()
    {
        return $this->validity_end->diffInDays(now(), false);
    }

    public function getIsExpiringsSoonAttribute()
    {
        return $this->days_to_expiry <= 60 && $this->days_to_expiry > 0;
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('validity_end', '<=', now()->addDays(60))
                     ->where('validity_end', '>', now());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
