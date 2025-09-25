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
        // Insert admin user
        $userId = Str::uuid();
        DB::table('users')->insertOrIgnore([
            'id' => $userId,
            'name' => 'Super Admin',
            'email' => 'admin@ybb-cms.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ambil role TenantOwner
        $role = DB::table('roles')->where('name', 'TenantOwner')->first();

        if ($role) {
            DB::table('model_has_roles')->insertOrIgnore([
                'role_id'    => $role->id,
                'model_type' => 'App\\Models\\User',
                'model_id'   => $userId,
            ]);
        }
    }
}
