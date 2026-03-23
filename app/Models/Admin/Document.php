<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Document extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'institute_id',
        'document_code',
        'title',
        'issue_number',
        'revision_number',
        'issue_date',
        'type',
        'description',
        'document_category',
        'draft_file_path',
        'draft_uploaded_by',
        'draft_uploaded_at',
        'hot_status',
        'hot_approved_by',
        'hot_approved_at',
        'hot_remarks',
        'hot_returned_reason',
        'external_status',
        'external_submitted_at',
        'external_submitted_by',
        'external_approver_name',
        'external_approved_at',
        'final_file_path',
        'final_uploaded_at',
        'final_uploaded_by',
        'seven_day_deadline',
        'seven_day_compliance_met',
        'archived_at',
        'archived_by',
        'status',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'draft_uploaded_at' => 'datetime',
        'hot_approved_at' => 'datetime',
        'external_submitted_at' => 'datetime',
        'external_approved_at' => 'datetime',
        'seven_day_deadline' => 'datetime',
        'final_uploaded_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    protected $appends = ['status_badge', 'hot_status_badge', 'external_status_badge', 'compliance_status', 'days_to_compliance_deadline'];

    // Relationships
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    public function draftUploader()
    {
        return $this->belongsTo(User::class, 'draft_uploaded_by');
    }

    public function hotApprover()
    {
        return $this->belongsTo(User::class, 'hot_approved_by');
    }

    public function externalSubmitter()
    {
        return $this->belongsTo(User::class, 'external_submitted_by');
    }

    public function finalUploader()
    {
        return $this->belongsTo(User::class, 'final_uploaded_by');
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeHOTReview($query)
    {
        return $query->where('status', 'hot_review')->where('hot_status', 'pending');
    }

    public function scopeHOTApproved($query)
    {
        return $query->where('hot_status', 'approved');
    }

    public function scopeHOTReturned($query)
    {
        return $query->where('hot_status', 'returned');
    }

    public function scopeExternalSubmitted($query)
    {
        return $query->where('status', 'external_approval');
    }

    public function scopeExternalApproved($query)
    {
        return $query->where('external_status', 'approved');
    }

    public function scopeComplianceAtrisk($query)
    {
        return $query->where('status', 'external_approval')
            ->where('external_status', 'approved')
            ->whereNotNull('seven_day_deadline')
            ->where('seven_day_compliance_met', false)
            ->where('seven_day_deadline', '<=', now());
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    // Document Management Workflow Methods

    /**
     * Upload draft document
     */
    public function uploadDraft(User $user, string $filePath): void
    {
        $this->update([
            'draft_file_path' => $filePath,
            'draft_uploaded_by' => $user->id,
            'draft_uploaded_at' => now(),
            'status' => 'hot_review',
        ]);

        ActivityLog::log([
            'action' => 'upload_draft_document',
            'module' => 'document_management',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Draft document uploaded: {$this->title} (Issue: {$this->issue_number}, Revision: {$this->revision_number})",
            'new_values' => ['status' => 'hot_review', 'draft_file_path' => $filePath],
        ]);
    }

    /**
     * Approve document as HoT
     */
    public function approveByHOT(User $user, string $remarks = ''): void
    {
        if (!$user->hasPermissionTo('hot_approve_documents')) {
            throw new \Exception('User does not have permission to approve documents as HoT');
        }

        $this->update([
            'hot_status' => 'approved',
            'hot_approved_by' => $user->id,
            'hot_approved_at' => now(),
            'hot_remarks' => $remarks,
            'status' => 'external_approval',
        ]);

        ActivityLog::log([
            'action' => 'approve_document_hot',
            'module' => 'document_management',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Document approved by HoT ({$user->name}): {$this->title}",
            'new_values' => ['hot_status' => 'approved', 'status' => 'external_approval'],
        ]);
    }

    /**
     * Return document to draft for revision (HoT)
     */
    public function returnForRevision(User $user, string $reason = ''): void
    {
        if (!$user->hasPermissionTo('hot_approve_documents')) {
            throw new \Exception('User does not have permission to return documents');
        }

        $this->update([
            'hot_status' => 'returned',
            'hot_approved_by' => $user->id,
            'hot_approved_at' => now(),
            'hot_returned_reason' => $reason,
            'status' => 'draft',
        ]);

        ActivityLog::log([
            'action' => 'return_document_revision',
            'module' => 'document_management',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'reason' => $reason,
            'description' => "Document returned for revision by HoT: {$this->title}",
            'new_values' => ['hot_status' => 'returned', 'status' => 'draft'],
        ]);
    }

    /**
     * Submit document for external approval
     */
    public function submitForExternalApproval(User $user): void
    {
        if ($this->hot_status !== 'approved') {
            throw new \Exception('Document must be approved by HoT before external submission');
        }

        if (!$user->hasPermissionTo('submit_external_approval')) {
            throw new \Exception('User does not have permission to submit for external approval');
        }

        // Calculate 7-day compliance deadline from external submission
        $sevenDayDeadline = now()->addDays(7);

        $this->update([
            'external_status' => 'submitted',
            'external_submitted_at' => now(),
            'external_submitted_by' => $user->id,
            'seven_day_deadline' => $sevenDayDeadline,
            'status' => 'external_approval',
        ]);

        ActivityLog::log([
            'action' => 'submit_external_approval',
            'module' => 'document_management',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Document submitted for external approval: {$this->title}. 7-day deadline: {$sevenDayDeadline->toDateString()}",
            'new_values' => ['external_status' => 'submitted', 'seven_day_deadline' => $sevenDayDeadline],
        ]);
    }

    /**
     * Mark external approval received
     */
    public function markExternalApproved(User $user, string $approverName = ''): void
    {
        $this->update([
            'external_status' => 'approved',
            'external_approved_at' => now(),
            'external_approver_name' => $approverName,
            'status' => 'awaiting_final_upload',
        ]);

        ActivityLog::log([
            'action' => 'mark_external_approved',
            'module' => 'document_management',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "External approval received for {$this->title} from {$approverName}",
            'new_values' => ['external_status' => 'approved'],
        ]);
    }

    /**
     * Upload final document (must be within 7 days of external approval)
     */
    public function uploadFinalDocument(User $user, string $filePath): void
    {
        if ($this->external_status !== 'approved') {
            throw new \Exception('External approval must be received before uploading final document');
        }

        if (now() > $this->seven_day_deadline) {
            throw new \Exception('7-day compliance deadline has expired. Compliance rule violation.');
        }

        $this->update([
            'final_file_path' => $filePath,
            'final_uploaded_by' => $user->id,
            'final_uploaded_at' => now(),
            'seven_day_compliance_met' => true,
            'status' => 'archive_ready',
        ]);

        ActivityLog::log([
            'action' => 'upload_final_document',
            'module' => 'document_management',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Final document uploaded: {$this->title} (Compliance: Met)",
            'new_values' => ['final_file_path' => $filePath, 'seven_day_compliance_met' => true],
        ]);
    }

    /**
     * Archive document
     */
    public function archive(User $user): void
    {
        if ($this->status !== 'archive_ready') {
            throw new \Exception('Document is not ready for archival');
        }

        if (!$user->hasPermissionTo('archive_documents')) {
            throw new \Exception('User does not have permission to archive documents');
        }

        $this->update([
            'status' => 'archived',
            'archived_at' => now(),
            'archived_by' => $user->id,
        ]);

        ActivityLog::log([
            'action' => 'archive_document',
            'module' => 'document_management',
            'model_type' => static::class,
            'model_id' => $this->id,
            'user_id' => $user->id,
            'institute_id' => $this->institute_id,
            'description' => "Document archived: {$this->title}",
            'new_values' => ['status' => 'archived'],
        ]);
    }

    /**
     * Check if document violates 7-day compliance rule
     */
    public function violates7DayRule(): bool
    {
        return $this->external_status === 'approved' &&
               $this->seven_day_compliance_met === false &&
               now() > $this->seven_day_deadline;
    }

    /**
     * Get days remaining until compliance deadline
     */
    public function getDaysUntilComplianceDeadline(): int
    {
        if (!$this->seven_day_deadline) {
            return 0;
        }

        $remaining = $this->seven_day_deadline->diffInDays(now(), false);
        return max(0, $remaining);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'hot_review' => 'bg-warning',
            'external_approval' => 'bg-info',
            'awaiting_final_upload' => 'bg-warning',
            'archive_ready' => 'bg-primary',
            'archived' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    public function getHotStatusBadgeAttribute()
    {
        return match($this->hot_status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'returned' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    public function getExternalStatusBadgeAttribute()
    {
        return match($this->external_status) {
            'pending' => 'badge-warning',
            'submitted' => 'badge-info',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    public function getComplianceStatusAttribute()
    {
        return [
            'meets_7day_rule' => $this->seven_day_compliance_met,
            'deadline' => $this->seven_day_deadline?->toDateString(),
            'days_remaining' => $this->getDaysUntilComplianceDeadline(),
            'is_atrisk' => $this->violates7DayRule(),
            'compliance_status' => $this->violates7DayRule() ? 'at_risk' : ($this->seven_day_compliance_met ? 'met' : 'pending'),
        ];
    }

    public function getDaysToComplianceDeadlineAttribute()
    {
        return $this->getDaysUntilComplianceDeadline();
    }
}
