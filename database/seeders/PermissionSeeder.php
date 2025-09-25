<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_posts',
            'create_posts',
            'edit_posts',
            'delete_posts',
            'publish_posts',
            'view_programs',
            'create_programs',
            'edit_programs',
            'delete_programs',
            'publish_programs',
            'view_jobs',
            'create_jobs',
            'edit_jobs',
            'delete_jobs',
            'publish_jobs',
            'view_media',
            'upload_media',
            'delete_media',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_ads',
            'create_ads',
            'edit_ads',
            'delete_ads',
            'manage_seo',
            'view_analytics',
            'manage_settings',
            'manage_tenant',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }
    }
}
