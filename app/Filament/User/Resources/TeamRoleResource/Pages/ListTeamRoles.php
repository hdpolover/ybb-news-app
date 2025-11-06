<?php

namespace App\Filament\User\Resources\TeamRoleResource\Pages;

use Filament\Pages\Page;

class ListTeamRoles extends Page
{
    protected static string $view = 'filament.user.pages.list-team-roles';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationLabel = 'Roles & Permissions';
    
    protected static ?string $navigationGroup = 'Settings';
    
    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Roles & Permissions';

    public function getHeading(): string
    {
        return 'Roles & Permissions';
    }

    public function getSubheading(): ?string
    {
        return 'View the permission matrix for each team role. Contact your platform administrator to request custom roles.';
    }

    public function getRoles(): array
    {
        return [
            [
                'name' => 'Tenant Admin',
                'slug' => 'tenant_admin',
                'color' => 'danger',
                'description' => 'Full administrative access to tenant settings, team management, and all content. Can configure domains, branding, and billing.',
                'permissions' => [
                    'Manage Settings' => true,
                    'Manage Team' => true,
                    'Publish Content' => true,
                    'Edit Others\' Content' => true,
                    'Delete Content' => true,
                    'Upload Media' => true,
                    'Manage Categories' => true,
                    'View Analytics' => true,
                ],
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'color' => 'warning',
                'description' => 'Can publish and manage all content types. Has access to team member management but cannot change tenant settings.',
                'permissions' => [
                    'Manage Settings' => false,
                    'Manage Team' => true,
                    'Publish Content' => true,
                    'Edit Others\' Content' => true,
                    'Delete Content' => true,
                    'Upload Media' => true,
                    'Manage Categories' => true,
                    'View Analytics' => true,
                ],
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'color' => 'success',
                'description' => 'Can create and publish their own content. Cannot edit or delete others\' content. Can upload media files.',
                'permissions' => [
                    'Manage Settings' => false,
                    'Manage Team' => false,
                    'Publish Content' => true,
                    'Edit Others\' Content' => false,
                    'Delete Content' => false,
                    'Upload Media' => true,
                    'Manage Categories' => false,
                    'View Analytics' => false,
                ],
            ],
            [
                'name' => 'Contributor',
                'slug' => 'contributor',
                'color' => 'gray',
                'description' => 'Can create content but cannot publish. All content requires review and approval by an Editor or Admin.',
                'permissions' => [
                    'Manage Settings' => false,
                    'Manage Team' => false,
                    'Publish Content' => false,
                    'Edit Others\' Content' => false,
                    'Delete Content' => false,
                    'Upload Media' => false,
                    'Manage Categories' => false,
                    'View Analytics' => false,
                ],
            ],
        ];
    }
}

