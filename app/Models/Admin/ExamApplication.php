<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamApplication extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'curriculum_id',
        'institute_id',
        'subjects_applied',
        'attendance_percentage',
        'accounts_approval',
        'accounts_rejection_reason',
        'accounts_approved_by',
        'accounts_approved_at',
        'tm_approval',
        'tm_rejection_reason',
        'tm_approved_by',
        'tm_approved_at',
        'bic_override',
        'bic_override_reason',
        'bic_override_by',
        'bic_override_at',
        'admit_card_serial',
        'admit_card_path',
        'em_signature_user',
        'em_signed_at',
        'tm_signature_user',
        'tm_signed_at',
        'admit_card_printed',
        'status',
    ];

    protected $casts = [
        'subjects_applied' => 'array',
        'accounts_approved_at' => 'datetime',
        'tm_approved_at' => 'datetime',
        'bic_override_at' => 'datetime',
        'em_signed_at' => 'datetime',
        'tm_signed_at' => 'datetime',
        'admit_card_printed' => 'boolean',
    ];

    protected $appends = ['status_badge', 'approval_status', 'admission_eligibility'];

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

    public function accountsApprover()
    {
        return $this->belongsTo(User::class, 'accounts_approved_by');
    }

    public function tmApprover()
    {
        return $this->belongsTo(User::class, 'tm_approved_by');
    }

    public function bicOverrideUser()
    {
        return $this->belongsTo(User::class, 'bic_override_by');
    }

    public function emSignature()
    {
        return $this->belongsTo(User::class, 'em_signature_user');
    }

    public function tmSignature()
    {
        return $this->belongsTo(User::class, 'tm_signature_user');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }

    // Scopes
    public function scopeAccountsPending($query)
    {
        return $query->whereNull('accounts_approved_at');
    }

    public function scopeAccountsApproved($query)
    {
        return $query->where('accounts_approval', 'approved')->whereNotNull('accounts_approved_at');
    }

    public function scopeAccountsRejected($query)
    {
        return $query->where('accounts_approval', 'rejected')->whereNotNull('accounts_approved_at');
    }

    public function scopeTMPending($query)
    {
        return $query->where('accounts_approval', 'approved')->whereNull('tm_approved_at');
    }

    public function scopeTMApproved($query)
    {
        return $query->where('tm_approval', 'approved')->whereNotNull('tm_approved_at');
    }

    public function scopeAdmitCardGenerated($query)
    {
        return $query->whereNotNull('admit_card_path');
    }

    public function scopeBICOverridden($query)
    {
        return $query->where('bic_override', true);
    }

    // Multi-Level Approval Methods

    /**
     * Check if application can go to Accounts gate
     */
    public function canSubmitToAccounts(): bool
    {
        return $this->student->isEligibleForER() &&
               !$this->accounts_approved_at &&
               !$this->tm_approved_at;
    }

    /**
     * Check if Accounts can approve
     */
    public function canAccountsApprove(): bool
    {
        return $this->accounts_approval !== 'approved' &&
               $this->accounts_approved_at === null;
    }

    /**
     * Accounts gate approval (No-dues check)
     */
    public function approveByAccounts(User $user, string $reason = ''): void
    {
        if (!$user->hasPermissionTo('approve_exam_applications')) {
            throw new \Exception('User does not have permission to approve exam applications');
        }

        $this->update([
            'accounts_approval' => 'approved',
            'accounts_approved_by' => $user->id,
            'accounts_approved_at' => now(),
            'accounts_rejection_reason' => null,
        ]);

        ActivityLog::log([
            'action' => 'approve_exam_accounts',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Exam application for student {$this->student->name} approved by Accounts ({$user->name})",
            'old_values' => ['accounts_approval' => null],
            'new_values' => ['accounts_approval' => 'approved'],
        ]);
    }

    /**
     * Accounts gate rejection
     */
    public function rejectByAccounts(User $user, string $reason = ''): void
    {
        if (!$user->hasPermissionTo('approve_exam_applications')) {
            throw new \Exception('User does not have permission to reject exam applications');
        }

        $this->update([
            'accounts_approval' => 'rejected',
            'accounts_approved_by' => $user->id,
            'accounts_approved_at' => now(),
            'accounts_rejection_reason' => $reason,
        ]);

        ActivityLog::log([
            'action' => 'reject_exam_accounts',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Exam application for student {$this->student->name} rejected by Accounts ({$user->name})",
            'old_values' => ['accounts_approval' => null],
            'new_values' => ['accounts_approval' => 'rejected'],
        ]);
    }

    /**
     * Check if TM can approve
     */
    public function canTMApprove(): bool
    {
        return $this->accounts_approval === 'approved' &&
               !$this->tm_approved_at;
    }

    /**
     * TM gate approval (Attendance check - 40% mid-sem OR 100% semester requirement)
     */
    public function approveByTM(User $user, string $reason = ''): void
    {
        if (!$user->hasPermissionTo('tm_exam_approval')) {
            throw new \Exception('User does not have permission to approve exams as TM');
        }

        if (!$this->canTMApprove()) {
            throw new \Exception('Application cannot be approved by TM at this stage');
        }

        $this->update([
            'tm_approval' => 'approved',
            'tm_approved_by' => $user->id,
            'tm_approved_at' => now(),
            'tm_rejection_reason' => null,
        ]);

        ActivityLog::log([
            'action' => 'approve_exam_tm',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Exam application for student {$this->student->name} approved by TM ({$user->name}) with {$this->attendance_percentage}% attendance",
            'old_values' => ['tm_approval' => null],
            'new_values' => ['tm_approval' => 'approved'],
        ]);

        // Auto-generate admit card if both approvals complete
        $this->generateAdmitCard();
    }

    /**
     * TM gate rejection
     */
    public function rejectByTM(User $user, string $reason = ''): void
    {
        if (!$user->hasPermissionTo('tm_exam_approval')) {
            throw new \Exception('User does not have permission to reject exams as TM');
        }

        $this->update([
            'tm_approval' => 'rejected',
            'tm_approved_by' => $user->id,
            'tm_approved_at' => now(),
            'tm_rejection_reason' => $reason,
        ]);

        ActivityLog::log([
            'action' => 'reject_exam_tm',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Exam application for student {$this->student->name} rejected by TM ({$user->name})",
            'old_values' => ['tm_approval' => null],
            'new_values' => ['tm_approval' => 'rejected'],
        ]);
    }

    /**
     * BiC override capability (bypasses Accounts/TM approvals)
     */
    public function bicOverride(User $user, string $reason = ''): void
    {
        if (!$user->hasPermissionTo('bic_exam_override')) {
            throw new \Exception('User does not have permission to override exam approvals');
        }

        if (!$reason) {
            throw new \Exception('BiC override requires a mandatory reason');
        }

        $this->update([
            'bic_override' => true,
            'bic_override_by' => $user->id,
            'bic_override_at' => now(),
            'bic_override_reason' => $reason,
            'accounts_approval' => 'approved',
            'tm_approval' => 'approved',
            'tm_approved_by' => $user->id,
            'tm_approved_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'bic_override_exam',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "BiC Override applied by {$user->name} for student {$this->student->name} - Reason: {$reason}",
            'old_values' => ['bic_override' => false],
            'new_values' => ['bic_override' => true, 'bic_override_reason' => $reason],
        ]);

        // Auto-generate admit card
        $this->generateAdmitCard();
    }

    /**
     * Generate admit card (only after both approvals)
     */
    public function generateAdmitCard(): void
    {
        if ($this->accounts_approval !== 'approved' || $this->tm_approval !== 'approved') {
            throw new \Exception('Both Accounts and TM approvals required before generating admit card');
        }

        $admitCardSerial = SerialNumberGenerator::generate('exam_code');

        $this->update([
            'admit_card_serial' => $admitCardSerial,
            'admit_card_path' => 'admit_cards/' . $this->student_id . '_' . $this->exam_id . '_' . now()->timestamp . '.pdf',
            'status' => 'admit_card_generated',
        ]);

        ActivityLog::log([
            'action' => 'generate_admit_card',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => auth()->id(),
            'institute_id' => $this->institute_id,
            'description' => "Admit card generated (Serial: {$admitCardSerial}) for student {$this->student->name}",
            'old_values' => ['admit_card_path' => null],
            'new_values' => ['admit_card_path' => $this->admit_card_path, 'admit_card_serial' => $admitCardSerial],
        ]);
    }

    /**
     * Sign admit card by Exam Manager (EM)
     */
    public function signByEM(User $user): void
    {
        $this->update([
            'em_signature_user' => $user->id,
            'em_signed_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'em_sign_admit_card',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Admit card signed by Exam Manager ({$user->name}) for student {$this->student->name}",
        ]);
    }

    /**
     * Sign admit card by Training Manager (TM)
     */
    public function signByTM(User $user): void
    {
        if ($this->tm_approved_by !== $user->id && !$user->hasPermissionTo('tm_exam_approval')) {
            throw new \Exception('Only the approving TM can sign the admit card');
        }

        $this->update([
            'tm_signature_user' => $user->id,
            'tm_signed_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'tm_sign_admit_card',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Admit card signed by Training Manager ({$user->name}) for student {$this->student->name}",
        ]);
    }

    /**
     * Mark admit card as printed
     */
    public function markAdmitCardPrinted(): void
    {
        $this->update([
            'admit_card_printed' => true,
            'status' => 'admit_card_printed',
        ]);

        ActivityLog::log([
            'action' => 'print_admit_card',
            'module' => 'exam_applications',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => auth()->id(),
            'institute_id' => $this->institute_id,
            'description' => "Admit card printed for student {$this->student->name}",
        ]);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'admit_card_printed' => 'bg-success',
            'admit_card_generated' => 'bg-info',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'pending' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    public function getApprovalStatusAttribute()
    {
        return [
            'accounts' => [
                'status' => $this->accounts_approval,
                'approved_by' => $this->accountsApprover?->name,
                'approved_at' => $this->accounts_approved_at?->toDateTimeString(),
                'rejection_reason' => $this->accounts_rejection_reason,
            ],
            'tm' => [
                'status' => $this->tm_approval,
                'approved_by' => $this->tmApprover?->name,
                'approved_at' => $this->tm_approved_at?->toDateTimeString(),
                'rejection_reason' => $this->tm_rejection_reason,
            ],
            'bic_override' => [
                'applied' => $this->bic_override,
                'applied_by' => $this->bicOverrideUser?->name,
                'applied_at' => $this->bic_override_at?->toDateTimeString(),
                'reason' => $this->bic_override_reason,
            ],
        ];
    }

    public function getAdmissionEligibilityAttribute()
    {
        return [
            'attendance_percentage' => $this->attendance_percentage,
            'meets_40_percent' => $this->attendance_percentage >= 40,
            'meets_100_percent' => $this->attendance_percentage >= 100,
            'can_appear' => $this->accounts_approval === 'approved' && ($this->tm_approval === 'approved' || $this->bic_override),
        ];
    }
}
