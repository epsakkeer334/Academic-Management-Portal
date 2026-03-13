<?php

namespace Database\Seeders\admin;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // -----------------------------
        // Create Roles
        // -----------------------------
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // -----------------------------
        // Super Admin User
        // -----------------------------
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Sha-Shib Admin',
                'password' => Hash::make('password'),
            ]
        );

        if (! $superAdminUser->hasRole('super-admin')) {
            $superAdminUser->assignRole($superAdminRole);
        }


    }
}
