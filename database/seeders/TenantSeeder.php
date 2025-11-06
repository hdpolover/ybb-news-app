<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'id' => Str::uuid(),
                'name' => 'Beasiswa Indonesia',
                'domain' => 'beasiswa.id',
                'description' => 'Platform beasiswa terlengkap di Indonesia',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#1E40AF',
                'accent_color' => '#F59E0B',
                'meta_title' => 'Beasiswa Indonesia - Info Beasiswa Terlengkap',
                'meta_description' => 'Temukan berbagai informasi beasiswa dalam dan luar negeri',
                'email_from_name' => 'Beasiswa Indonesia',
                'email_from_address' => 'info@beasiswa.id',
                'enabled_features' => json_encode(["programs", "news", "seo", "newsletter"]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Tech Jobs Portal',
                'domain' => 'techjobs.com',
                'description' => 'Find the best tech jobs in Southeast Asia',
                'primary_color' => '#10B981',
                'secondary_color' => '#059669',
                'accent_color' => '#8B5CF6',
                'meta_title' => 'Tech Jobs Portal - Best Tech Careers',
                'meta_description' => 'Discover amazing tech job opportunities',
                'email_from_name' => 'Tech Jobs',
                'email_from_address' => 'jobs@techjobs.com',
                'enabled_features' => json_encode(["jobs", "news", "ads"]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Youth Breaking Barriers',
                'domain' => 'ybb.id',
                'description' => 'Empowering youth through opportunities',
                'primary_color' => '#EF4444',
                'secondary_color' => '#DC2626',
                'accent_color' => '#F59E0B',
                'meta_title' => 'YBB - Youth Breaking Barriers',
                'meta_description' => 'Programs and opportunities for Indonesian youth',
                'email_from_name' => 'YBB Foundation',
                'email_from_address' => 'info@ybb.id',
                'enabled_features' => json_encode(["programs", "jobs", "news", "seo", "newsletter"]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($tenants as $tenant) {
            DB::table('tenants')->insertOrIgnore($tenant);
        }
    }
}
