# CAMP System - Code Implementation Reference

## Files Modified/Created in This Session

### 1. Database Seeder
**File**: `database/seeders/RolePermissionSeeder.php`  
**Status**: ✅ Created & Executed Successfully

**Contains**:
- 8 Roles: super-admin, institute-admin, accounts, training-manager, hot, bic, faculty, student
- 69 Permissions with descriptions across 7 categories
- Role-to-Permission assignments
- 3 Serial Number Generators seeded
- 6 Default Document Checklists

**Key Code**:
```php
// Create Roles
$superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
$instituteAdmin = Role::firstOrCreate(['name' => 'institute-admin', 'guard_name' => 'web']);
// ... (6 more roles)

// Define Permissions
$permissions = [
    'manage_institutes' => 'Create, edit, delete institutes',
    'verify_student_documents' => 'Verify KYC documents',
    'approve_exam_applications' => 'Approve/reject exam applications (Accounts gate)',
    // ... (66 more permissions)
];

// Assign to Roles
$superAdmin->syncPermissions(array_keys($permissions));
$instituteAdmin->syncPermissions([...]);
// ... (6 more role assignments)

// Seed Serial Number Generators
SerialNumberGenerator::firstOrCreate(['type' => 'marksheet'], [...]);
SerialNumberGenerator::firstOrCreate(['type' => 'er_number'], [...]);
SerialNumberGenerator::firstOrCreate(['type' => 'exam_code'], [...]);

// Seed Document Checklists
foreach ($checklists as $checklist) {
    DocumentChecklist::firstOrCreate([...], [...]);
}
```

---

### 2. Student Model (Enhanced)
**File**: `app/Models/Admin/Student.php`  
**Status**: ✅ Fully Enhanced with Dual-Gate Logic

**New Fields Added**:
- Parent details: `father_name`, `father_phone`, `mother_name`, `mother_phone`
- Document verification: `document_status`, `document_verified_by`, `document_verified_at`, `document_checklist`
- Fee approval: `fee_status`, `fee_approved_by`, `fee_approved_at`
- ID Card: `id_card_serial`, `id_card_signed`, `id_card_signed_by`
- ER Form: `er_form_path`, `er_form_signed`, `er_form_signed_by`

**Key Methods**:
```php
// Check eligibility
public function isDocumentVerified(): bool
public function isFeeApproved(): bool
public function isEligibleForER(): bool
public function hasCompletedEnrollment(): bool

// Gate 1: Admin verification
public function verifyDocuments(User $user, array $checklist = []): void
public function rejectDocuments(User $user, string $reason = ''): void

// Gate 2: Accounts approval
public function approveFees(User $user, string $reason = ''): void
public function rejectFees(User $user, string $reason = ''): void

// ER Generation
public function generateEnrollmentNumber(User $user): string

// Document signing
public function signIDCard(User $user, string $idCardPath): void
public function signERForm(User $user, string $erFormPath): void
```

**Activity Logging**: All methods log to `activity_logs` with action, module, reason, old/new values

**Accessors**:
```php
$student->dual_gate_status          // Array with complete approval status
$student->status_badge              // CSS badge class for UI
$student->enrollment_days_left      // Integer: days remaining in 30-day window
```

---

### 3. ExamApplication Model (Enhanced)
**File**: `app/Models/Admin/ExamApplication.php`  
**Status**: ✅ Multi-Level Approval Workflow Complete

**New Fields Added**:
- Attendance: `attendance_percentage`
- Accounts gate: `accounts_approval`, `accounts_approved_by`, `accounts_approved_at`, `accounts_rejection_reason`
- TM gate: `tm_approval`, `tm_approved_by`, `tm_approved_at`, `tm_rejection_reason`
- BiC override: `bic_override`, `bic_override_by`, `bic_override_at`, `bic_override_reason`
- Admit Card: `admit_card_serial`, `em_signature_user`, `em_signed_at`, `tm_signature_user`, `tm_signed_at`
- Tracking: `admit_card_printed`

**Key Methods**:
```php
// Level 1: Accounts Gate (No-dues)
public function approveByAccounts(User $user, string $reason = ''): void
public function rejectByAccounts(User $user, string $reason = ''): void

// Level 2: TM Gate (Attendance ≥40%)
public function approveByTM(User $user, string $reason = ''): void
public function rejectByTM(User $user, string $reason = ''): void

// Override: BiC Bypass (with mandatory reason)
public function bicOverride(User $user, string $reason = ''): void

// Admit Card Management
public function generateAdmitCard(): void
public function signByEM(User $user): void
public function signByTM(User $user): void
public function markAdmitCardPrinted(): void
```

**Scopes**:
```php
->accountsPending()      // Awaiting Accounts approval
->accountsApproved()     // Passed Accounts gate
->tmPending()            // Awaiting TM approval
->tmApproved()           // Passed TM gate
->admitCardGenerated()   // Admit card ready
->bicOverridden()        // BiC override applied
```

**Accessors**:
```php
$application->approval_status        // Complete workflow status object
$application->admission_eligibility  // Eligibility with metrics
```

---

### 4. Document Model (Enhanced)
**File**: `app/Models/Admin/Document.php`  
**Status**: ✅ Full DMS Lifecycle with 7-Day Compliance

**New Fields Added**:
- Document tracking: `document_code`, `document_category`, `institute_id`
- Draft phase: `draft_file_path`, `draft_uploaded_by`, `draft_uploaded_at`
- HoT phase: `hot_status`, `hot_approved_by`, `hot_remarks`, `hot_returned_reason`
- External phase: `external_status`, `external_submitted_at`, `external_submitted_by`, `external_approver_name`, `external_approved_at`
- Compliance: `seven_day_deadline`, `seven_day_compliance_met`, `final_file_path`, `final_uploaded_at`, `final_uploaded_by`
- Archive: `archived_at`, `archived_by`

**Key Methods**:
```php
// Phase A: Upload Draft
public function uploadDraft(User $user, string $filePath): void

// Phase B: HoT Review & Approval
public function approveByHOT(User $user, string $remarks = ''): void
public function returnForRevision(User $user, string $reason = ''): void

// Phase C: External Submission (7-day clock starts)
public function submitForExternalApproval(User $user): void
public function markExternalApproved(User $user, string $approverName = ''): void

// Phase D: Final Upload (within 7 days)
public function uploadFinalDocument(User $user, string $filePath): void

// Phase E: Archive
public function archive(User $user): void

// Compliance Checking
public function violates7DayRule(): bool
public function getDaysUntilComplianceDeadline(): int
```

**Scopes**:
```php
->draft()               // Draft status
->hotReview()           // Pending HoT review
->hotApproved()         // HoT approved
->externalSubmitted()   // External submission
->externalApproved()    // External approved
->complianceAtrisk()    // Violated 7-day rule
->archived()            // Archived
```

**Accessors**:
```php
$document->status_badge              // CSS badge class
$document->hot_status_badge          // HoT status badge
$document->external_status_badge     // External status badge
$document->compliance_status         // Complete compliance object
$document->days_to_compliance_deadline  // Days remaining
```

---

### 5. ExamResult Model (Enhanced)
**File**: `app/Models/Admin/ExamResult.php`  
**Status**: ✅ Grading with Super Admin Override

**New Fields Added**:
- Exam type: `exam_type` (online/offline/practical)
- Entry tracking: `marked_by`, `marked_at`
- Override capability: `is_overridden`, `overridden_marks`, `override_reason`, `overridden_by`, `overridden_at`

**Constants**:
```php
const PASSING_PERCENTAGE = 75;  // 75% required to pass
```

**Key Methods**:
```php
// Faculty marks entry (auto-calculates percentage & grade)
public function enterMarks(User $faculty, float $marksObtained, float $totalMarks, 
                           string $examType = 'online', string $remarks = ''): void

// Super Admin override (mandatory reason required)
public function overrideMarks(User $admin, float $newMarks, string $reason = ''): void

// Check passing status
public function hasPassed(): bool
```

**Private Methods**:
```php
private function calculatePercentage(float $obtained, float $total): float  // Returns 0-100
private function generateGrade(): void  // Auto-generates A+, A, B+, B, C+, C, F
```

**Scopes**:
```php
->passed()                // Passed (percentage >= 75%)
->failed()                // Failed (percentage < 75%)
->overridden()            // Overridden by Super Admin
->byExamType($type)       // Filter by exam type
```

**Accessors**:
```php
$result->pass_status              // Detailed pass/fail object
$result->grade_letter             // Letter grade (A+, A, etc.)
$result->marks_display            // Formatted with override indicator
```

---

### 6. Marksheet Model (Enhanced)
**File**: `app/Models/Admin/Marksheet.php`  
**Status**: ✅ Serial Generation & Dual Signatures

**New Fields Added**:
- Serial tracking: `serial_number`, `curriculum_id`, `academic_year`, `semester`
- Results: `results` (JSON array of exam results)
- Consolidation: `is_consolidated`, `consolidated_from` (array of exam IDs)
- Grading: `grade`
- Signatures: `tm_signature_user`, `tm_signed_at`, `em_signature_user`, `em_signed_at`, `is_signed`
- Generation: `generated_by`, `generated_at`

**Key Methods**:
```php
// Generate from single exam
public static function generateFromResults(ExamApplication $examApplication, User $generatedBy): static

// Generate consolidated (semester-end aggregation)
public static function generateConsolidated(Student $student, string $semester, User $generatedBy): static

// Signature collection
public function signByTM(User $user): void
public function signByEM(User $user): void
private function checkAndMarkAsSigned(): void  // Auto-marks when both signed

// Grading calculation
private static function calculateGrade(float $percentage): string
```

**Scopes**:
```php
->byStudent($studentId)    // Filter by student
->byExam($examId)          // Filter by exam
->consolidated()           // Semester-end marksheets
->singleExam()             // Individual exam marksheets
->signed()                 // Both signatures complete
->unsigned()               // Missing signatures
```

**Accessors**:
```php
$marksheet->signature_status      // Complete signature tracking object
$marksheet->result_summary        // Complete marksheet details
$marksheet->grade_display         // Letter grade or N/A
```

---

### 7. ActivityLog Model
**File**: `app/Models/Admin/ActivityLog.php`  
**Status**: ✅ Comprehensive Audit Trail

**Columns**:
```php
- id
- action: approve_exam_accounts, reject_exam_accounts, approve_exam_tm, 
          bic_override_exam, generate_er_number, verify_documents, etc.
- module: student_enrollment, exam_applications, document_management, exam_results
- model_type: Model class name (ExamApplication::class, Student::class, etc.)
- model_id: Related model ID
- user_id: User performing the action
- institute_id: Institute context
- description: Human-readable description
- reason: Optional reason (for rejections, overrides, etc.)
- old_values: JSON - previous values
- new_values: JSON - new values
- user_ip: IP address of user
- performed_at: Timestamp
```

**Methods**:
```php
// Static factory for easy logging
public static function log(array $data): void {
    // Auto-populate timestamps, user_ip, etc.
    // Insert into database
}
```

**Scopes**:
```php
->byAction('approve_exam_accounts')     // Filter by action
->byModule('exam_applications')         // Filter by module
->byUser($userId)                       // Filter by user
->byInstitute($instituteId)             // Filter by institute
```

---

### 8. SerialNumberGenerator Model
**File**: `app/Models/Admin/SerialNumberGenerator.php`  
**Status**: ✅ Centralized Unique ID Generation

**Fields**:
```php
- type: unique identifier (marksheet, er_number, exam_code)
- current_number: current counter
- prefix: letter prefix (MS, ER, EXM)
- padding: number padding (6, 8, 10)
- separator: separator character (-, /)
- format: format template ({prefix}-{year}-{number})
```

**Methods**:
```php
// Generate next number (increments counter)
public static function generate($type): string
// Returns formatted number, e.g., MS-2026-00000001

// Format without incrementing
public function formatNumber($number): string

// Get next number without incrementing
public function getNextNumber(): string
```

---

### 9. DocumentChecklist Model
**File**: `app/Models/Admin/DocumentChecklist.php`  
**Status**: ✅ KYC Requirements Management

**Fields**:
```php
- name: Checklist item name
- description: Item description
- institute_id: NULL for global, or specific institute
- status: active/inactive
- display_order: Sort order
```

**Methods**:
```php
// Scope for active checklists
public function scopeActive($query): Builder
```

---

### 10. Mou Model (Enhanced)
**File**: `app/Models/Admin/Mou.php`  
**Status**: ✅ MoU Management with Renewal Tracking

**Methods**:
```php
// Status badge for UI
public function getStatusBadgeAttribute(): string
// Returns 'Active', 'Expired', 'Expiring Soon', 'Pending Renewal'

// Days to expiry
public function getDaysToExpiryAttribute(): int

// Check if expiring soon (≤60 days)
public function getIsExpiringAttribute(): bool
```

**Scopes**:
```php
->expiringSoon()   // MoUs expiring within 60 days
->active()         // Currently active MoUs
```

---

## Migration Files (Database Schema)

All 14 migrations executed successfully:

1. ✅ `2026_03_23_114211_create_students_table.php`
2. ✅ `2026_03_23_114656_create_exams_table.php`
3. ✅ `2026_03_23_114721_create_subjects_table.php`
4. ✅ `2026_03_23_114735_create_exam_applications_table.php`
5. ✅ `2026_03_23_114750_create_exam_results_table.php`
6. ✅ `2026_03_23_114804_create_marksheets_table.php`
7. ✅ `2026_03_23_114817_create_documents_table.php`
8. ✅ `2026_03_23_114830_create_mous_table.php`
9. ✅ `2026_03_23_114844_create_notifications_table.php`
10. ✅ `2026_03_23_114857_create_audit_logs_table.php`
11. ✅ `2026_03_23_120104_create_document_checklists_table.php`
12. ✅ `2026_03_23_120114_create_activity_logs_table.php`
13. ✅ `2026_03_23_120118_create_exam_subjects_table.php`
14. ✅ `2026_03_23_120122_create_syllabus_mappings_table.php`
15. ✅ `2026_03_23_120127_create_serial_number_generators_table.php`

---

## Documentation Files Created

1. **CAMP_IMPLEMENTATION_SUMMARY.md** - Comprehensive 400+ line implementation guide
2. **CAMP_COMPLETION_REPORT.md** - Executive summary and deployment readiness
3. **CODE_IMPLEMENTATION_REFERENCE.md** - This file - code reference for all models

---

## Testing Recommendations

### Unit Tests
```php
// Student dual-gate logic
StudentTest::testDocumentVerification()
StudentTest::testFeeApproval()
StudentTest::testERGeneration()

// Exam application workflow
ExamApplicationTest::testAccountsApproval()
ExamApplicationTest::testTMApproval()
ExamApplicationTest::testBiCOverride()
ExamApplicationTest::testAdmitCardGeneration()

// Document compliance
DocumentTest::test7DayComplianceRule()
DocumentTest::testHOTApproval()
DocumentTest::testExternalSubmission()

// Grading logic
ExamResultTest::test75PercentPassRule()
ExamResultTest::testSuperAdminOverride()
ExamResultTest::testGradeGeneration()
```

### Integration Tests
```php
// End-to-end workflows
StudentEnrollmentWorkflowTest::testCompleteEnrollment()
ExamApplicationWorkflowTest::testCompletApprovalFlow()
DocumentLifecycleTest::testFullDMSWorkflow()
ResultPublicationTest::testMarksheetGeneration()
```

---

## Deployment Checklist

- [x] Database migrations executed
- [x] Roles and permissions seeded
- [x] Serial number generators initialized
- [x] Document checklists created
- [x] Models enhanced with business logic
- [x] Activity logging system implemented
- [x] No compile/lint errors
- [ ] Livewire components created
- [ ] PDF generation implemented
- [ ] Email notifications configured
- [ ] Unit tests written and passing
- [ ] Integration tests written and passing
- [ ] Performance testing completed
- [ ] Security audit completed
- [ ] User documentation prepared
- [ ] Staff training completed
- [ ] Production deployment

---

**Last Updated**: During CAMP Implementation Phase 1-4  
**Version**: 1.0  
**Status**: Ready for Frontend Development
