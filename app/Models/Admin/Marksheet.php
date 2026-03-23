<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Marksheet extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'student_id',
        'exam_id',
        'curriculum_id',
        'institute_id',
        'academic_year',
        'semester',
        'results',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'overall_result',
        'is_consolidated',
        'consolidated_from',
        'pdf_path',
        'is_signed',
        'tm_signature_user',
        'tm_signed_at',
        'em_signature_user',
        'em_signed_at',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'results' => 'array',
        'consolidated_from' => 'array',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'tm_signed_at' => 'datetime',
        'em_signed_at' => 'datetime',
        'generated_at' => 'datetime',
        'is_consolidated' => 'boolean',
        'is_signed' => 'boolean',
    ];

    protected $appends = ['signature_status', 'result_summary', 'grade_display'];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function tmSignature()
    {
        return $this->belongsTo(User::class, 'tm_signature_user');
    }

    public function emSignature()
    {
        return $this->belongsTo(User::class, 'em_signature_user');
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeConsolidated($query)
    {
        return $query->where('is_consolidated', true);
    }

    public function scopeSingleExam($query)
    {
        return $query->where('is_consolidated', false);
    }

    public function scopeSigned($query)
    {
        return $query->where('is_signed', true);
    }

    public function scopeUnsigned($query)
    {
        return $query->where('is_signed', false);
    }

    // Marksheet Generation Methods

    /**
     * Generate marksheet from exam results
     */
    public static function generateFromResults(ExamApplication $examApplication, User $generatedBy): static
    {
        // Calculate totals from all results
        $results = $examApplication->results;
        $totalMarks = $results->sum('total_marks');
        $obtainedMarks = $results->sum('marks_obtained');
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
        $overallResult = $percentage >= 75 ? 'Pass' : 'Fail';

        $serialNumber = SerialNumberGenerator::generate('marksheet');

        $marksheet = static::create([
            'serial_number' => $serialNumber,
            'student_id' => $examApplication->student_id,
            'exam_id' => $examApplication->exam_id,
            'curriculum_id' => $examApplication->curriculum_id,
            'institute_id' => $examApplication->institute_id,
            'academic_year' => date('Y') . '-' . (date('Y') + 1),
            'semester' => $examApplication->exam->semester ?? 1,
            'results' => $results->toArray(),
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
            'overall_result' => $overallResult,
            'is_consolidated' => false,
            'generated_by' => $generatedBy->id,
            'generated_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'generate_marksheet',
            'module' => 'exam_results',
            'model_type' => static::class,
            'model_id' => $marksheet->id,
            'user_id' => $generatedBy->id,
            'institute_id' => $examApplication->institute_id,
            'description' => "Marksheet generated for student {$examApplication->student->name} (Serial: {$serialNumber})",
            'new_values' => ['serial_number' => $serialNumber],
        ]);

        return $marksheet;
    }

    /**
     * Generate consolidated marksheet (semester-end aggregation)
     */
    public static function generateConsolidated(Student $student, string $semester, User $generatedBy): static
    {
        $exams = Exam::where('semester', $semester)->get();
        $marksheets = static::byStudent($student->id)
            ->whereIn('exam_id', $exams->pluck('id'))
            ->singleExam()
            ->get();

        if ($marksheets->isEmpty()) {
            throw new \Exception('No marksheets found for consolidation');
        }

        $totalMarks = $marksheets->sum('total_marks');
        $obtainedMarks = $marksheets->sum('obtained_marks');
        $percentage = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 2) : 0;
        $overallResult = $percentage >= 75 ? 'Pass' : 'Fail';

        $serialNumber = SerialNumberGenerator::generate('marksheet');

        $consolidatedMarksheet = static::create([
            'serial_number' => $serialNumber,
            'student_id' => $student->id,
            'curriculum_id' => $student->course_applied,
            'institute_id' => $student->institute_id,
            'academic_year' => date('Y') . '-' . (date('Y') + 1),
            'semester' => $semester,
            'results' => $marksheets->map(fn($m) => $m->results)->flatten(1)->toArray(),
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
            'grade' => static::calculateGrade($percentage),
            'overall_result' => $overallResult,
            'is_consolidated' => true,
            'consolidated_from' => $marksheets->pluck('id')->toArray(),
            'generated_by' => $generatedBy->id,
            'generated_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'generate_consolidated_marksheet',
            'module' => 'exam_results',
            'model_type' => static::class,
            'model_id' => $consolidatedMarksheet->id,
            'user_id' => $generatedBy->id,
            'institute_id' => $student->institute_id,
            'description' => "Consolidated marksheet generated for student {$student->name} - Semester {$semester} (Serial: {$serialNumber})",
            'new_values' => ['serial_number' => $serialNumber, 'is_consolidated' => true],
        ]);

        return $consolidatedMarksheet;
    }

    /**
     * Sign marksheet by Training Manager
     */
    public function signByTM(User $user): void
    {
        $this->update([
            'tm_signature_user' => $user->id,
            'tm_signed_at' => now(),
        ]);

        $this->checkAndMarkAsSigned();

        ActivityLog::log([
            'action' => 'sign_marksheet_tm',
            'module' => 'exam_results',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Marksheet signed by TM ({$user->name}) - Serial: {$this->serial_number}",
        ]);
    }

    /**
     * Sign marksheet by Exam Manager
     */
    public function signByEM(User $user): void
    {
        $this->update([
            'em_signature_user' => $user->id,
            'em_signed_at' => now(),
        ]);

        $this->checkAndMarkAsSigned();

        ActivityLog::log([
            'action' => 'sign_marksheet_em',
            'module' => 'exam_results',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Marksheet signed by EM ({$user->name}) - Serial: {$this->serial_number}",
        ]);
    }

    /**
     * Check if both signatures complete and mark as signed
     */
    private function checkAndMarkAsSigned(): void
    {
        if ($this->tm_signature_user && $this->em_signature_user) {
            $this->update(['is_signed' => true]);
        }
    }

    /**
     * Calculate grade based on percentage
     */
    private static function calculateGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C+',
            $percentage >= 40 => 'C',
            default => 'F'
        };
    }

    // Accessors
    public function getSignatureStatusAttribute()
    {
        return [
            'tm_signed' => !is_null($this->tm_signature_user),
            'tm_signed_by' => $this->tmSignature?->name,
            'tm_signed_at' => $this->tm_signed_at?->toDateTimeString(),
            'em_signed' => !is_null($this->em_signature_user),
            'em_signed_by' => $this->emSignature?->name,
            'em_signed_at' => $this->em_signed_at?->toDateTimeString(),
            'both_signed' => $this->is_signed,
        ];
    }

    public function getResultSummaryAttribute()
    {
        return [
            'serial_number' => $this->serial_number,
            'student' => $this->student->name,
            'exam' => $this->exam->title,
            'semester' => $this->semester,
            'total_marks' => $this->total_marks,
            'obtained_marks' => $this->obtained_marks,
            'percentage' => $this->percentage,
            'grade' => $this->grade ?? 'N/A',
            'result' => $this->overall_result,
            'is_consolidated' => $this->is_consolidated,
        ];
    }

    public function getGradeDisplayAttribute()
    {
        return $this->grade ?? 'N/A';
    }
}
