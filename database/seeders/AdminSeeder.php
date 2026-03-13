<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\admin\{RoleSeeder, PermissionSeeder};


class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,

        ]);
    }
}
