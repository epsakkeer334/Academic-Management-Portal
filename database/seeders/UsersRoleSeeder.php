<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample users for each role
        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@example.com', 'role' => 'super-admin'],
            ['name' => 'Institute Admin', 'email' => 'instituteadmin@example.com', 'role' => 'institute-admin'],
            ['name' => 'Accounts User', 'email' => 'accounts@example.com', 'role' => 'accounts'],
            ['name' => 'Training Manager', 'email' => 'tm@example.com', 'role' => 'training-manager'],
            ['name' => 'Faculty User', 'email' => 'faculty@example.com', 'role' => 'faculty'],
            ['name' => 'Student User', 'email' => 'student@example.com', 'role' => 'student'],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'status' => true,
                ]
            );

            if (!$user->hasRole($u['role'])) {
                $user->assignRole($u['role']);
            }
        }


    }
}
