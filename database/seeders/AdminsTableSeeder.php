<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->insert([
            'id' => Str::uuid(),
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'superadmin',
            'is_active' => true,
            'settings' => json_encode([
                'theme' => 'dark',
                'notifications' => true,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
