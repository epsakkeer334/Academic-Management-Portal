<?php

namespace App\Models\Admin;

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
    ];

    protected $casts = [
        'coverage_percentage' => 'decimal:2',
        'start_date' => 'date',
        'planned_completion' => 'date',
    ];

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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }
}
