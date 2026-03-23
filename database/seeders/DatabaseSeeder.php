<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core admin seeders
        $this->call([
            AdminSeeder::class,
        ]);

        // Role/permission and sample users
        $this->call([
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\UsersRoleSeeder::class,
        ]);
    }
}
