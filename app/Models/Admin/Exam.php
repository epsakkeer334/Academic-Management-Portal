<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'institute_id',
        'course_id',
        'curriculum_id',
        'exam_date',
        'start_time',
        'end_time',
        'passing_percentage',
        'status',
        'description',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'passing_percentage' => 'decimal:2',
    ];

    protected $appends = ['status_badge'];

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function applications()
    {
        return $this->hasMany(ExamApplication::class);
    }

    public function marksheets()
    {
        return $this->hasMany(Marksheet::class);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'scheduled' => 'bg-info',
            'ongoing' => 'bg-warning',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /* Scope to get active institutes only
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
