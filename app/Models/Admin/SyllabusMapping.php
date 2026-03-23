<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SyllabusMapping extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id',
        'subject_id',
        'institute_id',
        'coverage_percentage',
        'status',
        'start_date',
        'planned_completion',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'coverage_percentage' => 'decimal:2',
        'start_date' => 'date',
        'planned_completion' => 'date',
    ];

    protected $appends = ['formatted_start_date', 'formatted_planned_completion', 'status_badge'];

    // Relationships
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d M Y') : '-';
    }

    public function getFormattedPlannedCompletionAttribute()
    {
        return $this->planned_completion ? $this->planned_completion->format('d M Y') : '-';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'in_progress' => 'bg-warning',
            'completed' => 'bg-success',
            default => 'bg-secondary',
        };
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
