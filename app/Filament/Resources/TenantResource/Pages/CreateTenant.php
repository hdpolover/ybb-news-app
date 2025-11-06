<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract admin data before creating tenant
        $this->adminData = [
            'name' => $data['admin_name'] ?? null,
            'email' => $data['admin_email'] ?? null,
            'password' => $data['admin_password'] ?? null,
            'bio' => $data['admin_bio'] ?? null,
        ];

        // Remove admin fields from tenant data
        unset($data['admin_name'], $data['admin_email'], $data['admin_password'], $data['admin_password_confirmation'], $data['admin_bio'], $data['create_admin']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Create admin user (now required)
        $user = User::create([
            'name' => $this->adminData['name'],
            'email' => $this->adminData['email'],
            'password' => Hash::make($this->adminData['password']),
            'bio' => $this->adminData['bio'] ?? null,
            'is_active' => true,
        ]);

        // Link user to tenant as tenant admin
        $user->tenants()->attach($this->record->id, [
            'role' => 'tenant_admin',
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign Tenant Admin role with all permissions
        $tenantAdminRole = \Spatie\Permission\Models\Role::where('name', 'Tenant Admin')
            ->where('guard_name', 'web')
            ->first();
        
        if ($tenantAdminRole) {
            // Ensure role has all permissions (in case seeder wasn't run)
            if ($tenantAdminRole->permissions()->count() === 0) {
                $allPermissions = \Spatie\Permission\Models\Permission::where('guard_name', 'web')->get();
                $tenantAdminRole->syncPermissions($allPermissions);
            }
            
            // Assign role to user
            $user->assignRole($tenantAdminRole);
        }

        Notification::make()
            ->success()
            ->title('Tenant Created with Admin')
            ->body("Admin user '{$user->name}' has been created with full permissions and linked to this tenant")
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Tenant Information')
                    ->description('Basic information about the tenant organization')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Tenant Name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('The organization or company name'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required()
                            ->helperText('Tenant status - only active tenants can access the system'),
                    ])->columns(2),

                Forms\Components\Section::make('Features')
                    ->description('Enable/disable features for this tenant')
                    ->schema([
                        Forms\Components\CheckboxList::make('enabled_features')
                            ->label('Enabled Features')
                            ->options([
                                'programs' => 'Programs Management',
                                'jobs' => 'Jobs Management',
                                'news' => 'News Management',
                                'seo' => 'SEO Settings',
                                'ads' => 'Ads Management',
                                'newsletter' => 'Newsletter Service',
                            ])
                            ->columns(3)
                            ->default(['programs', 'jobs', 'news']),
                    ]),

                Forms\Components\Section::make('Admin User')
                    ->description('Create an admin user for this tenant.')
                    ->schema([
                        Forms\Components\TextInput::make('admin_name')
                            ->label('Admin Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('admin_email')
                            ->label('Admin Email')
                            ->email()
                            ->required()
                            ->unique('users', 'email'),
                        Forms\Components\TextInput::make('admin_password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->same('admin_password_confirmation'),
                        Forms\Components\TextInput::make('admin_password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->required()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    protected ?array $adminData = null;
}
