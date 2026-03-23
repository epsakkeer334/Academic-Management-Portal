<?php

namespace Database\Seeders;

use App\Models\Admin\SerialNumberGenerator;
use App\Models\Admin\DocumentChecklist;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $instituteAdmin = Role::firstOrCreate(['name' => 'institute-admin', 'guard_name' => 'web']);
        $accounts = Role::firstOrCreate(['name' => 'accounts', 'guard_name' => 'web']);
        $tm = Role::firstOrCreate(['name' => 'training-manager', 'guard_name' => 'web']);
        $hot = Role::firstOrCreate(['name' => 'hot', 'guard_name' => 'web']);
        $bic = Role::firstOrCreate(['name' => 'bic', 'guard_name' => 'web']);
        $faculty = Role::firstOrCreate(['name' => 'faculty', 'guard_name' => 'web']);
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        // Create Permissions
        $permissions = [
            // Institution Management
            'manage_institutes' => 'Create, edit, delete institutes',
            'view_institutes' => 'View institutes',
            'assign_institute_admins' => 'Assign admin to institutes',

            // Student Management
            'manage_students' => 'Manage student records',
            'verify_student_documents' => 'Verify KYC documents',
            'approve_student_enrollment' => 'Approve ER generation',
            'generate_er_number' => 'Generate unique ER numbers',
            'generate_id_cards' => 'Generate ID cards',

            // Examination Management
            'manage_exams' => 'Create and manage exams',
            'map_subjects' => 'Map subjects to exams and institutes',
            'create_exam_questions' => 'Create exam questions',
            'submit_exam_results' => 'Submit exam results',
            'override_marks' => 'Override exam marks',
            'generate_marksheets' => 'Generate marksheets',
            'approve_exam_applications' => 'Approve/reject exam applications (Accounts gate)',
            'tm_exam_approval' => 'Approve exams based on attendance (TM gate)',
            'bic_exam_override' => 'Override exam approvals (BiC)',

            // Document Management
            'manage_documents' => 'Upload and manage documents',
            'hot_approve_documents' => 'Approve documents as HoT',
            'submit_external_approval' => 'Submit documents for external approval',
            'archive_documents' => 'Archive final documents',
            'view_audit_logs' => 'View activity logs for documents',

            // MoU Management
            'manage_mous' => 'Manage MoUs',
            'renew_mous' => 'Initiate MoU renewal',

            // Reporting & Compliance
            'view_reports' => 'View reports',
            'export_reports' => 'Export reports',
            'view_compliance_dashboard' => 'View compliance metrics',
            'audit_access' => 'Access audit-ready documents',

            // User Management
            'manage_users' => 'Manage user accounts',
            'manage_roles' => 'Manage roles',
            'manage_permissions' => 'Manage permissions',
        ];

        // Create all permissions
        $permissionModels = [];
        foreach ($permissions as $permission => $description) {
            $permissionModels[$permission] = Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['description' => $description]
            );
        }

        // Assign permissions to Super Admin (all permissions)
        $superAdmin->syncPermissions(array_keys($permissions));

        // Institute Admin permissions
        $instituteAdmin->syncPermissions([
            'view_institutes',
            'manage_students',
            'verify_student_documents',
            'manage_exams',
            'map_subjects',
            'submit_exam_results',
            'generate_marksheets',
            'manage_documents',
            'hot_approve_documents',
            'manage_users',
            'view_reports',
            'view_compliance_dashboard',
        ]);

        // Accounts permissions
        $accounts->syncPermissions([
            'view_institutes',
            'manage_students',
            'approve_exam_applications', // Accounts gate approval
            'view_reports',
        ]);

        // Training Manager permissions
        $tm->syncPermissions([
            'view_institutes',
            'manage_students',
            'tm_exam_approval', // TM gate approval
            'submit_exam_results',
            'generate_marksheets',
            'hot_approve_documents',
            'view_reports',
        ]);

        // Head of Training permissions
        $hot->syncPermissions([
            'view_institutes',
            'hot_approve_documents',
            'submit_external_approval',
            'archive_documents',
            'manage_mous',
            'renew_mous',
            'view_compliance_dashboard',
            'view_reports',
            'view_audit_logs',
        ]);

        // BiC permissions
        $bic->syncPermissions([
            'view_institutes',
            'bic_exam_override', // Can override exam approvals
            'manage_students',
            'view_reports',
        ]);

        // Faculty permissions
        $faculty->syncPermissions([
            'submit_exam_results',
            'view_reports',
        ]);

        // Student permissions
        $student->syncPermissions([
            'view_reports', // Can view own results
        ]);

        // Create Serial Number Generators
        SerialNumberGenerator::firstOrCreate(
            ['type' => 'marksheet'],
            [
                'current_number' => 0,
                'prefix' => 'MS',
                'padding' => 8,
                'separator' => '-',
                'format' => '{prefix}-{year}-{number}', // e.g., MS-2026-00000001
            ]
        );

        SerialNumberGenerator::firstOrCreate(
            ['type' => 'er_number'],
            [
                'current_number' => 0,
                'prefix' => 'ER',
                'padding' => 10,
                'separator' => '-',
                'format' => '{prefix}-{year}-{number}', // e.g., ER-2026-0000000001
            ]
        );

        SerialNumberGenerator::firstOrCreate(
            ['type' => 'exam_code'],
            [
                'current_number' => 0,
                'prefix' => 'EXM',
                'padding' => 6,
                'separator' => '-',
                'format' => '{prefix}-{number}', // e.g., EXM-000001
            ]
        );

        // Create default Document Checklists
        $checklists = [
            ['name' => 'Medical Certificate', 'description' => 'Valid medical fitness certificate'],
            ['name' => '10th Marksheet', 'description' => 'Photocopy of 10th class marksheet'],
            ['name' => '12th/Diploma Marksheet', 'description' => 'Photocopy of 12th class or diploma marksheet'],
            ['name' => 'Passport Size Photos', 'description' => '4 passport size photographs'],
            ['name' => 'Address Proof', 'description' => 'Utility bill or rental agreement'],
            ['name' => 'Identity Proof', 'description' => 'Aadhar, Passport, or Driving License'],
        ];

        foreach ($checklists as $checklist) {
            DocumentChecklist::firstOrCreate(
                ['name' => $checklist['name']],
                [
                    'description' => $checklist['description'],
                    'status' => 'active',
                    'display_order' => DocumentChecklist::count() + 1,
                ]
            );
        }
    }
}
