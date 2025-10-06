<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            TenantSeeder::class,
            UserSeeder::class,
            DemoContentSeeder::class,
            AdminsTableSeeder::class
        ]);
    }
}
