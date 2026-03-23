<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSubject extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'subject_id',
        'question_count',
        'total_marks',
        'exam_mode',
        'is_mandatory',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
