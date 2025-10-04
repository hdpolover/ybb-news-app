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
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
            'is_active' => true,
            'settings' => json_encode([
                'theme' => 'dark',
                'notifications' => true,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Contoh tambah admin biasa
        DB::table('admins')->insert([
            'id' => Str::uuid(),
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'settings' => json_encode([
                'theme' => 'light',
                'notifications' => false,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
