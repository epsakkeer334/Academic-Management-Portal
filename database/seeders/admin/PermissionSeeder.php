<?php

namespace Database\Seeders\admin;

use Illuminate\Database\Seeder;
use App\Models\Admin\PermissionGroup;
use App\Models\Admin\Permission; // Use custom Permission model
use App\Models\Admin\Role; // Use custom Role model
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permission Groups (with check)
        $groups = [
            [
                'name' => 'Dashboard',
                'icon' => 'ti ti-dashboard',
                'sort_order' => 1,
                'permissions' => [
                    ['name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
                ]
            ],
            [
                'name' => 'Institution Management',
                'icon' => 'ti ti-building-community',
                'sort_order' => 2,
                'permissions' => [
                    ['name' => 'view_institutions', 'display_name' => 'View Institutions'],
                    ['name' => 'create_institutions', 'display_name' => 'Create Institutions'],
                    ['name' => 'edit_institutions', 'display_name' => 'Edit Institutions'],
                    ['name' => 'delete_institutions', 'display_name' => 'Delete Institutions'],
                    ['name' => 'assign_institute_admins', 'display_name' => 'Assign Institute Admins'],
                    ['name' => 'view_institute_reports', 'display_name' => 'View Institute Reports'],
                ]
            ],
            [
                'name' => 'Student Management',
                'icon' => 'ti ti-users',
                'sort_order' => 3,
                'permissions' => [
                    ['name' => 'view_students', 'display_name' => 'View Students'],
                    ['name' => 'verify_student_documents', 'display_name' => 'Verify Student Documents'],
                    ['name' => 'approve_student_enrollment', 'display_name' => 'Approve Student Enrollment'],
                    ['name' => 'generate_er_number', 'display_name' => 'Generate ER Number'],
                    ['name' => 'view_student_reports', 'display_name' => 'View Student Reports'],
                ]
            ],
            [
                'name' => 'Examination',
                'icon' => 'ti ti-notes',
                'sort_order' => 4,
                'permissions' => [
                    ['name' => 'view_exams', 'display_name' => 'View Exams'],
                    ['name' => 'create_exams', 'display_name' => 'Create Exams'],
                    ['name' => 'edit_exams', 'display_name' => 'Edit Exams'],
                    ['name' => 'delete_exams', 'display_name' => 'Delete Exams'],
                    ['name' => 'schedule_exams', 'display_name' => 'Schedule Exams'],
                    ['name' => 'approve_exam_applications', 'display_name' => 'Approve Exam Applications'],
                    ['name' => 'enter_marks', 'display_name' => 'Enter Marks'],
                    ['name' => 'override_marks', 'display_name' => 'Override Marks'],
                    ['name' => 'generate_marksheets', 'display_name' => 'Generate Marksheets'],
                    ['name' => 'view_results', 'display_name' => 'View Results'],
                ]
            ],
            [
                'name' => 'Document Management',
                'icon' => 'ti ti-file-text',
                'sort_order' => 5,
                'permissions' => [
                    ['name' => 'view_documents', 'display_name' => 'View Documents'],
                    ['name' => 'upload_documents', 'display_name' => 'Upload Documents'],
                    ['name' => 'edit_documents', 'display_name' => 'Edit Documents'],
                    ['name' => 'delete_documents', 'display_name' => 'Delete Documents'],
                    ['name' => 'approve_documents', 'display_name' => 'Approve Documents'],
                    ['name' => 'archive_documents', 'display_name' => 'Archive Documents'],
                ]
            ],
            [
                'name' => 'MoU Management',
                'icon' => 'ti ti-handshake',
                'sort_order' => 6,
                'permissions' => [
                    ['name' => 'view_mous', 'display_name' => 'View MoUs'],
                    ['name' => 'create_mous', 'display_name' => 'Create MoUs'],
                    ['name' => 'edit_mous', 'display_name' => 'Edit MoUs'],
                    ['name' => 'delete_mous', 'display_name' => 'Delete MoUs'],
                    ['name' => 'approve_mous', 'display_name' => 'Approve MoUs'],
                ]
            ],
            [
                'name' => 'Compliance & Audit',
                'icon' => 'ti ti-checklist',
                'sort_order' => 7,
                'permissions' => [
                    ['name' => 'view_compliance_dashboard', 'display_name' => 'View Compliance Dashboard'],
                    ['name' => 'view_audit_logs', 'display_name' => 'View Audit Logs'],
                    ['name' => 'export_audit_reports', 'display_name' => 'Export Audit Reports'],
                ]
            ],
            [
                'name' => 'Role & Permission Management',
                'icon' => 'ti ti-lock',
                'sort_order' => 8,
                'permissions' => [
                    ['name' => 'view_roles', 'display_name' => 'View Roles'],
                    ['name' => 'create_roles', 'display_name' => 'Create Roles'],
                    ['name' => 'edit_roles', 'display_name' => 'Edit Roles'],
                    ['name' => 'delete_roles', 'display_name' => 'Delete Roles'],
                    ['name' => 'assign_permissions', 'display_name' => 'Assign Permissions'],
                    ['name' => 'view_permissions', 'display_name' => 'View Permissions'],
                ]
            ],
            [
                'name' => 'User Management',
                'icon' => 'ti ti-user-cog',
                'sort_order' => 9,
                'permissions' => [
                    ['name' => 'view_users', 'display_name' => 'View Users'],
                    ['name' => 'create_users', 'display_name' => 'Create Users'],
                    ['name' => 'edit_users', 'display_name' => 'Edit Users'],
                    ['name' => 'delete_users', 'display_name' => 'Delete Users'],
                    ['name' => 'assign_roles', 'display_name' => 'Assign Roles'],
                ]
            ],
            [
                'name' => 'Reports & Exports',
                'icon' => 'ti ti-report',
                'sort_order' => 10,
                'permissions' => [
                    ['name' => 'view_reports', 'display_name' => 'View Reports'],
                    ['name' => 'export_reports', 'display_name' => 'Export Reports'],
                    ['name' => 'generate_custom_reports', 'display_name' => 'Generate Custom Reports'],
                ]
            ],
        ];

        // Create groups and permissions with checks
        foreach ($groups as $groupData) {
            // Create or update group
            $group = PermissionGroup::firstOrCreate(
                ['name' => $groupData['name']],
                [
                    'icon' => $groupData['icon'],
                    'sort_order' => $groupData['sort_order'],
                ]
            );

            foreach ($groupData['permissions'] as $permissionData) {
                // Create or update permission using custom Permission model
                Permission::firstOrCreate(
                    [
                        'name' => $permissionData['name'],
                        'guard_name' => 'web',
                    ],
                    [
                        'display_name' => $permissionData['display_name'],
                        'group_id' => $group->id,
                        'module' => $groupData['name'],
                        'sort_order' => $permissionData['sort_order'] ?? 0,
                    ]
                );
            }
        }

        // Get all permissions for super-admin
        $allPermissions = Permission::pluck('name')->toArray();

        // Create roles with checks using custom Role model
        $roles = [
            [
                'name' => 'super-admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access',
                'is_system' => true,
                'permissions' => $allPermissions
            ],
            [
                'name' => 'institute-admin',
                'display_name' => 'Institute Admin',
                'description' => 'Manage institute operations',
                'is_system' => true,
                'permissions' => [
                    'view_dashboard',
                    'view_students',
                    'verify_student_documents',
                    'approve_student_enrollment',
                    'view_exams',
                    'create_exams',
                    'edit_exams',
                    'schedule_exams',
                    'enter_marks',
                    'view_results',
                    'view_documents',
                    'upload_documents',
                    'view_mous',
                    'view_users',
                    'view_reports',
                    'export_reports',
                ]
            ],
            [
                'name' => 'accounts',
                'display_name' => 'Accounts',
                'description' => 'Manage financial transactions',
                'permissions' => [
                    'view_dashboard',
                    'view_students',
                    'approve_student_enrollment',
                    'view_exams',
                    'approve_exam_applications',
                    'view_reports',
                    'export_reports',
                ]
            ],
            [
                'name' => 'training-manager',
                'display_name' => 'Training Manager',
                'description' => 'Manage training operations',
                'permissions' => [
                    'view_dashboard',
                    'view_students',
                    'approve_student_enrollment',
                    'view_exams',
                    'approve_exam_applications',
                    'enter_marks',
                    'view_results',
                    'view_documents',
                    'upload_documents',
                    'view_reports',
                ]
            ],
            [
                'name' => 'hot',
                'display_name' => 'Head of Training',
                'description' => 'Oversee training department',
                'permissions' => [
                    'view_dashboard',
                    'view_students',
                    'view_exams',
                    'enter_marks',
                    'override_marks',
                    'view_results',
                    'view_documents',
                    'upload_documents',
                    'approve_documents',
                    'view_mous',
                    'approve_mous',
                    'view_compliance_dashboard',
                    'view_reports',
                    'export_reports',
                ]
            ],
            [
                'name' => 'bic',
                'display_name' => 'Base In-Charge',
                'description' => 'Manage base operations',
                'permissions' => [
                    'view_dashboard',
                    'view_students',
                    'approve_exam_applications',
                    'view_results',
                    'view_reports',
                ]
            ],
            [
                'name' => 'faculty',
                'display_name' => 'Faculty',
                'description' => 'Teaching staff',
                'permissions' => [
                    'view_dashboard',
                    'view_students',
                    'view_exams',
                    'enter_marks',
                    'view_results',
                ]
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Student access',
                'permissions' => [
                    'view_dashboard',
                    'view_exams',
                    'view_results',
                ]
            ],
        ];

        foreach ($roles as $roleData) {
            // Create or update role using custom Role model
            $role = Role::firstOrCreate(
                [
                    'name' => $roleData['name'],
                    'guard_name' => 'web',
                ],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_system' => $roleData['is_system'] ?? false,
                ]
            );

            // Sync permissions if they exist in the role data
            if (isset($roleData['permissions'])) {
                // Get only permissions that exist in the database
                $existingPermissions = Permission::whereIn('name', $roleData['permissions'])
                    ->pluck('name')
                    ->toArray();

                // Sync permissions
                if (!empty($existingPermissions)) {
                    $role->syncPermissions($existingPermissions);
                }
            }
        }

        // Assign super-admin role to first user (if exists and not already assigned)
        $user = User::first();
        if ($user && !$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }

        $this->command->info('Permissions and roles seeded successfully!');
    }
}
