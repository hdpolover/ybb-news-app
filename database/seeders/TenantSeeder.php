<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tenants')->insertOrIgnore([
            'id' => Str::uuid(),
            'name' => 'Youth Beyond Borders Demo',
            'domain' => 'demo.ybb-cms.local',
            'description' => 'Demo tenant for YBB Multi-Tenant CMS showcasing opportunities and job management.',
            'meta_title' => 'Youth Beyond Borders - Opportunities & Career Development',
            'meta_description' => 'Discover scholarships, internships, fellowships, and job opportunities for youth worldwide.',
            'enabled_features' => json_encode(["programs", "jobs", "news", "seo", "ads", "newsletter"]),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
