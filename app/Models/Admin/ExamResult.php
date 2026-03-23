<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamResult extends BaseModel
{
    use HasFactory;

    const PASSING_PERCENTAGE = 75;

    protected $fillable = [
        'exam_application_id',
        'subject_id',
        'exam_type',
        'marks_obtained',
        'total_marks',
        'percentage',
        'grade',
        'status',
        'remarks',
        'marked_by',
        'marked_at',
        'is_overridden',
        'overridden_marks',
        'override_reason',
        'overridden_by',
        'overridden_at',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'marked_at' => 'datetime',
        'overridden_at' => 'datetime',
        'is_overridden' => 'boolean',
    ];

    protected $appends = ['pass_status', 'grade_letter', 'marks_display'];

    // Relationships
    public function examApplication()
    {
        return $this->belongsTo(ExamApplication::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    public function overriddenBy()
    {
        return $this->belongsTo(User::class, 'overridden_by');
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->where('percentage', '>=', static::PASSING_PERCENTAGE);
    }

    public function scopeFailed($query)
    {
        return $query->where('percentage', '<', static::PASSING_PERCENTAGE);
    }

    public function scopeOverridden($query)
    {
        return $query->where('is_overridden', true);
    }

    public function scopeByExamType($query, $examType)
    {
        return $query->where('exam_type', $examType);
    }

    // Mark Entry Methods

    /**
     * Enter marks for exam result
     */
    public function enterMarks(User $faculty, float $marksObtained, float $totalMarks, string $examType = 'online', string $remarks = ''): void
    {
        if (!$faculty->hasPermissionTo('submit_exam_results')) {
            throw new \Exception('User does not have permission to submit exam results');
        }

        $percentage = $this->calculatePercentage($marksObtained, $totalMarks);
        $status = $percentage >= static::PASSING_PERCENTAGE ? 'pass' : 'fail';

        $this->update([
            'marks_obtained' => $marksObtained,
            'total_marks' => $totalMarks,
            'percentage' => $percentage,
            'exam_type' => $examType,
            'status' => $status,
            'remarks' => $remarks,
            'marked_by' => $faculty->id,
            'marked_at' => now(),
            'is_overridden' => false,
        ]);

        $this->generateGrade();

        ActivityLog::log([
            'action' => 'enter_exam_marks',
            'module' => 'exam_results',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $faculty->id,
            'institute_id' => $this->examApplication->institute_id,
            'description' => "Marks entered for student {$this->examApplication->student->name} - {$this->subject->name}: {$marksObtained}/{$totalMarks} ({$percentage}%)",
            'new_values' => [
                'marks_obtained' => $marksObtained,
                'total_marks' => $totalMarks,
                'percentage' => $percentage,
                'status' => $status,
            ],
        ]);
    }

    /**
     * Override marks by Super Admin
     */
    public function overrideMarks(User $admin, float $newMarks, string $reason = ''): void
    {
        if (!$admin->hasPermissionTo('override_marks')) {
            throw new \Exception('User does not have permission to override marks');
        }

        if (!$reason) {
            throw new \Exception('Override reason is mandatory');
        }

        $oldPercentage = $this->percentage;
        $oldStatus = $this->status;
        $percentage = $this->calculatePercentage($newMarks, $this->total_marks);
        $newStatus = $percentage >= static::PASSING_PERCENTAGE ? 'pass' : 'fail';

        $this->update([
            'is_overridden' => true,
            'overridden_marks' => $newMarks,
            'override_reason' => $reason,
            'overridden_by' => $admin->id,
            'overridden_at' => now(),
            'marks_obtained' => $newMarks,
            'percentage' => $percentage,
            'status' => $newStatus,
        ]);

        $this->generateGrade();

        ActivityLog::log([
            'action' => 'override_exam_marks',
            'module' => 'exam_results',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $admin->id,
            'institute_id' => $this->examApplication->institute_id,
            'reason' => $reason,
            'description' => "Marks overridden for student {$this->examApplication->student->name} - {$this->subject->name}: {$oldPercentage}% → {$percentage}% (Reason: {$reason})",
            'old_values' => [
                'marks_obtained' => $this->getOriginal('marks_obtained'),
                'percentage' => $oldPercentage,
                'status' => $oldStatus,
            ],
            'new_values' => [
                'marks_obtained' => $newMarks,
                'percentage' => $percentage,
                'status' => $newStatus,
            ],
        ]);
    }

    /**
     * Calculate percentage
     */
    private function calculatePercentage(float $obtained, float $total): float
    {
        return $total > 0 ? round(($obtained / $total) * 100, 2) : 0;
    }

    /**
     * Generate letter grade based on percentage
     */
    private function generateGrade(): void
    {
        $grade = match (true) {
            $this->percentage >= 90 => 'A+',
            $this->percentage >= 80 => 'A',
            $this->percentage >= 70 => 'B+',
            $this->percentage >= 60 => 'B',
            $this->percentage >= 50 => 'C+',
            $this->percentage >= 40 => 'C',
            default => 'F'
        };

        $this->update(['grade' => $grade]);
    }

    /**
     * Check if result passes 75% criteria
     */
    public function hasPassed(): bool
    {
        return $this->percentage >= static::PASSING_PERCENTAGE;
    }

    // Accessors
    public function getPassStatusAttribute()
    {
        return [
            'passed' => $this->hasPassed(),
            'percentage' => $this->percentage,
            'minimum_required' => static::PASSING_PERCENTAGE,
            'status' => $this->status,
        ];
    }

    public function getGradeLetterAttribute()
    {
        return $this->grade ?? 'N/A';
    }

    public function getMarksDisplayAttribute()
    {
        $display = "{$this->marks_obtained}/{$this->total_marks}";

        if ($this->is_overridden) {
            return $display . ' (Overridden)';
        }

        return $display;
    }
}
