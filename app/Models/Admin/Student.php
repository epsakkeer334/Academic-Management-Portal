<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'er_number',
        'name',
        'email',
        'phone',
        'dob',
        'gender',
        'address',
        'city',
        'state_id',
        'country_id',
        'pincode',
        'qualification',
        'course_applied',
        'institute_id',
        'kyc_documents',
        'certificates',
        'document_checklist',
        'document_status',
        'document_verified_by',
        'document_verified_at',
        'fee_status',
        'fee_approved_by',
        'fee_approved_at',
        'er_number',
        'er_form_path',
        'er_form_signed',
        'er_form_signed_by',
        'id_card_path',
        'id_card_serial',
        'id_card_signed',
        'id_card_signed_by',
        'father_name',
        'father_phone',
        'mother_name',
        'mother_phone',
        'er_status',
        'er_approved_at',
        'approved_by',
    ];

    protected $casts = [
        'kyc_documents' => 'array',
        'certificates' => 'array',
        'document_checklist' => 'array',
        'er_approved_at' => 'datetime',
        'document_verified_at' => 'datetime',
        'fee_approved_at' => 'datetime',
        'id_card_signed' => 'boolean',
        'er_form_signed' => 'boolean',
    ];

    protected $appends = ['full_address', 'status_badge', 'dual_gate_status', 'enrollment_days_left'];

    // Relationships
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function documentVerifier()
    {
        return $this->belongsTo(User::class, 'document_verified_by');
    }

    public function feeApprover()
    {
        return $this->belongsTo(User::class, 'fee_approved_by');
    }

    public function idCardSignedBy()
    {
        return $this->belongsTo(User::class, 'id_card_signed_by');
    }

    public function erFormSignedBy()
    {
        return $this->belongsTo(User::class, 'er_form_signed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function examApplications()
    {
        return $this->hasMany(ExamApplication::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function marksheets()
    {
        return $this->hasMany(Marksheet::class);
    }

    // Scopes
    public function scopeDocumentPending($query)
    {
        return $query->where('document_status', 'pending');
    }

    public function scopeDocumentVerified($query)
    {
        return $query->where('document_status', 'verified');
    }

    public function scopeDocumentRejected($query)
    {
        return $query->where('document_status', 'rejected');
    }

    public function scopeFeePending($query)
    {
        return $query->where('fee_status', 'pending');
    }

    public function scopeFeeApproved($query)
    {
        return $query->where('fee_status', 'approved');
    }

    public function scopeFeeRejected($query)
    {
        return $query->where('fee_status', 'rejected');
    }

    public function scopeEligibleForEnrollment($query)
    {
        return $query
            ->where('document_status', 'verified')
            ->where('fee_status', 'approved')
            ->whereNull('er_number');
    }

    // Dual-Gate Approval Methods
    /**
     * Check if student has passed document verification gate
     */
    public function isDocumentVerified(): bool
    {
        return $this->document_status === 'verified';
    }

    /**
     * Check if student has passed fee approval gate
     */
    public function isFeeApproved(): bool
    {
        return $this->fee_status === 'approved';
    }

    /**
     * Check if student is eligible for ER generation (passed both gates)
     */
    public function isEligibleForER(): bool
    {
        return $this->isDocumentVerified() && $this->isFeeApproved() && !$this->er_number;
    }

    /**
     * Check if student has completed enrollment
     */
    public function hasCompletedEnrollment(): bool
    {
        return !is_null($this->er_number) && !is_null($this->id_card_signed_by) && !is_null($this->er_form_signed_by);
    }

    /**
     * Verify student documents (Admin gate)
     */
    public function verifyDocuments(User $user, array $checklist = []): void
    {
        $this->update([
            'document_status' => 'verified',
            'document_verified_by' => $user->id,
            'document_verified_at' => now(),
            'document_checklist' => $checklist,
        ]);

        // Log the activity
        ActivityLog::log([
            'action' => 'verify_documents',
            'module' => 'student_enrollment',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Documents verified for student {$this->name} (ER: {$this->er_number})",
            'old_values' => ['document_status' => 'pending'],
            'new_values' => ['document_status' => 'verified'],
        ]);
    }

    /**
     * Reject student documents
     */
    public function rejectDocuments(User $user, string $reason = ''): void
    {
        $this->update([
            'document_status' => 'rejected',
            'document_verified_by' => $user->id,
            'document_verified_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'reject_documents',
            'module' => 'student_enrollment',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Documents rejected for student {$this->name}",
            'old_values' => ['document_status' => 'pending'],
            'new_values' => ['document_status' => 'rejected'],
        ]);
    }

    /**
     * Approve student fees (Accounts gate)
     */
    public function approveFees(User $user, string $reason = ''): void
    {
        $this->update([
            'fee_status' => 'approved',
            'fee_approved_by' => $user->id,
            'fee_approved_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'approve_fees',
            'module' => 'student_enrollment',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Fees approved for student {$this->name}",
            'old_values' => ['fee_status' => 'pending'],
            'new_values' => ['fee_status' => 'approved'],
        ]);
    }

    /**
     * Reject student fees
     */
    public function rejectFees(User $user, string $reason = ''): void
    {
        $this->update([
            'fee_status' => 'rejected',
            'fee_approved_by' => $user->id,
            'fee_approved_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'reject_fees',
            'module' => 'student_enrollment',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Fees rejected for student {$this->name}",
            'old_values' => ['fee_status' => 'pending'],
            'new_values' => ['fee_status' => 'rejected'],
        ]);
    }

    /**
     * Generate ER number and ID card (after both approvals)
     */
    public function generateEnrollmentNumber(User $user): string
    {
        if (!$this->isEligibleForER()) {
            throw new \Exception('Student is not eligible for ER generation. Both document verification and fee approval are required.');
        }

        $erNumber = SerialNumberGenerator::generate('er_number');

        $this->update([
            'er_number' => $erNumber,
            'er_status' => 'generated',
            'approved_by' => $user->id,
            'er_approved_at' => now(),
        ]);

        ActivityLog::log([
            'action' => 'generate_er_number',
            'module' => 'student_enrollment',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "ER number generated: {$erNumber} for student {$this->name}",
            'old_values' => ['er_number' => null, 'er_status' => 'pending'],
            'new_values' => ['er_number' => $erNumber, 'er_status' => 'generated'],
        ]);

        return $erNumber;
    }

    /**
     * Sign ID card (Physical signature by TM)
     */
    public function signIDCard(User $user, string $idCardPath): void
    {
        $idCardSerial = 'ID-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);

        $this->update([
            'id_card_path' => $idCardPath,
            'id_card_serial' => $idCardSerial,
            'id_card_signed' => true,
            'id_card_signed_by' => $user->id,
        ]);

        ActivityLog::log([
            'action' => 'sign_id_card',
            'module' => 'student_enrollment',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "ID card signed by {$user->name} (Serial: {$idCardSerial})",
            'old_values' => ['id_card_signed' => false],
            'new_values' => ['id_card_signed' => true, 'id_card_serial' => $idCardSerial],
        ]);
    }

    /**
     * Sign ER form (Physical signature by TM)
     */
    public function signERForm(User $user, string $erFormPath): void
    {
        $this->update([
            'er_form_path' => $erFormPath,
            'er_form_signed' => true,
            'er_form_signed_by' => $user->id,
        ]);

        ActivityLog::log([
            'action' => 'sign_er_form',
            'module' => 'student_enrollment',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "ER form signed by {$user->name} for student {$this->name}",
            'old_values' => ['er_form_signed' => false],
            'new_values' => ['er_form_signed' => true],
        ]);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        return $this->address . ', ' . $this->city . ', ' . ($this->state?->name ?? '') . ', ' . ($this->country?->name ?? '') . ' - ' . $this->pincode;
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->hasCompletedEnrollment()) {
            return 'bg-success'; // Enrolled
        }

        if ($this->isEligibleForER()) {
            return 'bg-info'; // Ready for ER
        }

        if ($this->isDocumentVerified() && !$this->isFeeApproved()) {
            return 'bg-warning'; // Awaiting fee approval
        }

        if (!$this->isDocumentVerified() && $this->isFeeApproved()) {
            return 'bg-warning'; // Awaiting document verification
        }

        if ($this->document_status === 'rejected' || $this->fee_status === 'rejected') {
            return 'bg-danger'; // Rejected
        }

        return 'bg-secondary'; // Pending
    }

    public function getDualGateStatusAttribute()
    {
        return [
            'documents' => $this->document_status,
            'documents_verified_by' => $this->documentVerifier?->name,
            'documents_verified_at' => $this->document_verified_at?->toDateTimeString(),
            'fees' => $this->fee_status,
            'fees_approved_by' => $this->feeApprover?->name,
            'fees_approved_at' => $this->fee_approved_at?->toDateTimeString(),
            'eligible_for_er' => $this->isEligibleForER(),
            'status' => match (true) {
                $this->hasCompletedEnrollment() => 'enrolled',
                $this->isEligibleForER() => 'ready_for_er',
                $this->isDocumentVerified() && $this->isFeeApproved() => 'approved',
                default => 'pending',
            },
        ];
    }

    public function getEnrollmentDaysLeftAttribute()
    {
        if ($this->created_at->diffInDays(now()) > 30) {
            return 0; // Enrollment window closed
        }

        return 30 - $this->created_at->diffInDays(now());
    }
}
