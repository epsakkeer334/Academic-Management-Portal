<?php

namespace App\Models\Admin;

use App\Models\User;

class Curriculum extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'curriculums'; // Specify the correct table name

    protected $fillable = [
        'course_id',
        'name',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = ['course_name'];

    /**
     * Get the course that owns the curriculum.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the course name attribute.
     */
    public function getCourseNameAttribute()
    {
        return $this->course ? $this->course->name : '-';
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
     * Scope a query to search curriculums.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        return $query;
    }

    /**
     * Scope a query to filter by course.
     */
    public function scopeByCourse($query, $courseId)
    {
        if ($courseId) {
            return $query->where('course_id', $courseId);
        }
        return $query;
    }

}
