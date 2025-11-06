<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get tenants
        $beasiswa = DB::table('tenants')->where('domain', 'beasiswa.id')->first();
        $techjobs = DB::table('tenants')->where('domain', 'techjobs.com')->first();
        $ybb = DB::table('tenants')->where('domain', 'ybb.id')->first();

        // Create users
        $users = [
            [
                'id' => Str::uuid(),
                'name' => 'Budi Santoso',
                'email' => 'budi@beasiswa.id',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'bio' => 'Tenant Admin for Beasiswa Indonesia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Sarah Chen',
                'email' => 'sarah@techjobs.com',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'bio' => 'Tenant Admin for Tech Jobs Portal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Ahmad Rahman',
                'email' => 'ahmad@ybb.id',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'bio' => 'Tenant Admin for YBB - manages multiple tenants',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Lisa Editor',
                'email' => 'lisa@beasiswa.id',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'bio' => 'Content Editor for Beasiswa Indonesia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'John Author',
                'email' => 'john@techjobs.com',
                'password' => Hash::make('Admin@123'),
                'is_active' => true,
                'bio' => 'Content Author for Tech Jobs',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insertOrIgnore($user);
        }

        // Get user IDs
        $budiId = DB::table('users')->where('email', 'budi@beasiswa.id')->value('id');
        $sarahId = DB::table('users')->where('email', 'sarah@techjobs.com')->value('id');
        $ahmadId = DB::table('users')->where('email', 'ahmad@ybb.id')->value('id');
        $lisaId = DB::table('users')->where('email', 'lisa@beasiswa.id')->value('id');
        $johnId = DB::table('users')->where('email', 'john@techjobs.com')->value('id');

        // Create user-tenant relationships
        $userTenants = [
            // Budi - Tenant Admin for Beasiswa Indonesia (default)
            [
                'id' => Str::uuid(),
                'user_id' => $budiId,
                'tenant_id' => $beasiswa->id,
                'role' => 'tenant_admin',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Sarah - Tenant Admin for Tech Jobs (default)
            [
                'id' => Str::uuid(),
                'user_id' => $sarahId,
                'tenant_id' => $techjobs->id,
                'role' => 'tenant_admin',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Ahmad - Tenant Admin for YBB (default) and also has access to Beasiswa
            [
                'id' => Str::uuid(),
                'user_id' => $ahmadId,
                'tenant_id' => $ybb->id,
                'role' => 'tenant_admin',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'user_id' => $ahmadId,
                'tenant_id' => $beasiswa->id,
                'role' => 'editor',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Lisa - Editor for Beasiswa Indonesia
            [
                'id' => Str::uuid(),
                'user_id' => $lisaId,
                'tenant_id' => $beasiswa->id,
                'role' => 'editor',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // John - Author for Tech Jobs
            [
                'id' => Str::uuid(),
                'user_id' => $johnId,
                'tenant_id' => $techjobs->id,
                'role' => 'author',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($userTenants as $userTenant) {
            DB::table('user_tenants')->insertOrIgnore($userTenant);
        }

        // Assign roles using Spatie permissions
        $tenantAdminRole = DB::table('roles')->where('name', 'Tenant Admin')->first();
        $editorRole = DB::table('roles')->where('name', 'Editor')->first();
        $authorRole = DB::table('roles')->where('name', 'Author')->first();

        if ($tenantAdminRole) {
            DB::table('model_has_roles')->insertOrIgnore([
                ['role_id' => $tenantAdminRole->id, 'model_type' => 'App\\Models\\User', 'model_id' => $budiId],
                ['role_id' => $tenantAdminRole->id, 'model_type' => 'App\\Models\\User', 'model_id' => $sarahId],
                ['role_id' => $tenantAdminRole->id, 'model_type' => 'App\\Models\\User', 'model_id' => $ahmadId],
            ]);
        }

        if ($editorRole) {
            DB::table('model_has_roles')->insertOrIgnore([
                ['role_id' => $editorRole->id, 'model_type' => 'App\\Models\\User', 'model_id' => $lisaId],
            ]);
        }

        if ($authorRole) {
            DB::table('model_has_roles')->insertOrIgnore([
                ['role_id' => $authorRole->id, 'model_type' => 'App\\Models\\User', 'model_id' => $johnId],
            ]);
        }
    }
}

