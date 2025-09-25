<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'TenantOwner' => ['*'], // semua permission
            'Admin' => [
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
                'manage_settings'
            ],
            'Editor' => [
                'view_posts',
                'create_posts',
                'edit_posts',
                'publish_posts',
                'view_programs',
                'create_programs',
                'edit_programs',
                'publish_programs',
                'view_jobs',
                'create_jobs',
                'edit_jobs',
                'publish_jobs'
            ],
            'Author' => [
                'view_posts',
                'create_posts',
                'edit_posts',
                'view_programs',
                'create_programs',
                'edit_programs',
                'view_jobs',
                'create_jobs',
                'edit_jobs'
            ],
            'SEO' => ['manage_seo', 'view_analytics'],
            'Moderator' => ['view_posts', 'delete_posts', 'view_jobs', 'delete_jobs'],
            'Analyst' => ['view_analytics'],
        ];

        foreach ($map as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if (!$role) continue;

            if (in_array('*', $perms)) {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($perms);
            }
        }
    }
}
