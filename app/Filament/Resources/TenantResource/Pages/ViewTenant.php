<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Support\Enums\FontWeight;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addUser')
                ->label('Add User')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->form([
                    Forms\Components\Section::make('User Information')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique('users', 'email')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->required()
                                ->minLength(8)
                                ->same('password_confirmation'),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->label('Confirm Password')
                                ->password()
                                ->required()
                                ->dehydrated(false),
                            Forms\Components\Select::make('role')
                                ->label('Role')
                                ->options([
                                    'tenant_admin' => 'Tenant Admin',
                                    'editor' => 'Editor',
                                    'author' => 'Author',
                                    'contributor' => 'Contributor',
                                ])
                                ->default('author')
                                ->required()
                                ->helperText('Tenant Admin has full access, Editor can manage content, Author can create posts, Contributor can submit drafts'),
                            Forms\Components\Toggle::make('is_default')
                                ->label('Set as Default Tenant')
                                ->helperText('This tenant will be the default when the user logs in')
                                ->default(false),
                            Forms\Components\Textarea::make('bio')
                                ->label('Bio')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2),
                ])
                ->action(function (array $data): void {
                    // Create user
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'bio' => $data['bio'] ?? null,
                        'is_active' => true,
                    ]);

                    // Link user to this tenant
                    $user->tenants()->attach($this->record->id, [
                        'role' => $data['role'],
                        'is_default' => $data['is_default'] ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Assign role based on selection
                    $roleName = match($data['role']) {
                        'tenant_admin' => 'Tenant Admin',
                        'editor' => 'Editor',
                        'author' => 'Author',
                        'contributor' => 'Contributor',
                        default => 'Author',
                    };
                    $user->assignRole($roleName);

                    Notification::make()
                        ->success()
                        ->title('User Added')
                        ->body("User '{$user->name}' has been created and added to this tenant")
                        ->send();

                    // Refresh the page to show new user
                    $this->redirect(static::getUrl(['record' => $this->record]));
                }),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Tabs::make('Tenant Details')
                    ->tabs([
                        Components\Tabs\Tab::make('Overview')
                            ->schema([
                                Components\Section::make('Tenant Information')
                                    ->schema([
                                        Components\TextEntry::make('name')
                                            ->label('Tenant Name')
                                            ->size(Components\TextEntry\TextEntrySize::Large)
                                            ->weight(FontWeight::Bold),
                                        Components\TextEntry::make('status')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'active' => 'success',
                                                'pending' => 'warning',
                                                'suspended' => 'danger',
                                                default => 'gray',
                                            }),
                                        Components\TextEntry::make('description')
                                            ->columnSpanFull()
                                            ->placeholder('No description provided'),
                                    ])
                                    ->columns(2),

                                Components\Section::make('Statistics')
                                    ->schema([
                                        Components\TextEntry::make('users_count')
                                            ->label('Users')
                                            ->state(fn ($record) => $record->users()->count())
                                            ->icon('heroicon-o-users')
                                            ->color('info'),
                                        Components\TextEntry::make('posts_count')
                                            ->label('Posts')
                                            ->state(fn ($record) => $record->posts()->count())
                                            ->icon('heroicon-o-document-text')
                                            ->color('success'),
                                        Components\TextEntry::make('terms_count')
                                            ->label('Terms')
                                            ->state(fn ($record) => $record->terms()->count())
                                            ->icon('heroicon-o-tag')
                                            ->color('warning'),
                                        Components\TextEntry::make('media_count')
                                            ->label('Media Files')
                                            ->state(fn ($record) => $record->media()->count())
                                            ->icon('heroicon-o-photo')
                                            ->color('purple'),
                                    ])
                                    ->columns(4),

                                Components\Section::make('Dates')
                                    ->schema([
                                        Components\TextEntry::make('created_at')
                                            ->label('Created')
                                            ->dateTime()
                                            ->since(),
                                        Components\TextEntry::make('updated_at')
                                            ->label('Last Updated')
                                            ->dateTime()
                                            ->since(),
                                    ])
                                    ->columns(2),
                            ]),

                        Components\Tabs\Tab::make('Users')
                            ->badge(fn ($record) => $record->users()->count())
                            ->schema([
                                Components\Section::make()
                                    ->schema([
                                        Components\ViewEntry::make('users')
                                            ->label('')
                                            ->view('filament.infolists.users-list')
                                            ->state(fn ($record) => $record->users),
                                    ])
                                    ->hidden(fn ($record) => $record->users()->count() > 0),
                                Components\Section::make()
                                    ->schema([
                                        Components\RepeatableEntry::make('users')
                                            ->label('')
                                            ->schema([
                                                Components\TextEntry::make('name')
                                                    ->label('Name')
                                                    ->weight(FontWeight::Bold),
                                                Components\TextEntry::make('email')
                                                    ->label('Email')
                                                    ->icon('heroicon-o-envelope')
                                                    ->copyable()
                                                    ->copyMessage('Email copied')
                                                    ->copyMessageDuration(1500),
                                                Components\TextEntry::make('pivot.role')
                                                    ->label('Role')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'tenant_admin' => 'danger',
                                                        'editor' => 'warning',
                                                        'author' => 'info',
                                                        'contributor' => 'gray',
                                                        default => 'gray',
                                                    })
                                                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                                                Components\TextEntry::make('is_active')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),
                                                Components\TextEntry::make('pivot.created_at')
                                                    ->label('Added')
                                                    ->dateTime()
                                                    ->since(),
                                            ])
                                            ->columns(5)
                                            ->contained(false),
                                    ])
                                    ->hidden(fn ($record) => $record->users()->count() === 0),
                                Components\Section::make()
                                    ->schema([
                                        Components\TextEntry::make('empty_users')
                                            ->label('')
                                            ->default('No users yet. Add users to this tenant using the "Add User" button above.')
                                            ->extraAttributes(['class' => 'text-center text-gray-500']),
                                    ])
                                    ->hidden(fn ($record) => $record->users()->count() > 0),
                            ]),

                        Components\Tabs\Tab::make('Posts')
                            ->badge(fn ($record) => $record->posts()->count())
                            ->schema([
                                Components\Section::make()
                                    ->schema([
                                        Components\RepeatableEntry::make('posts')
                                            ->label('')
                                            ->schema([
                                                Components\TextEntry::make('title')
                                                    ->label('Title')
                                                    ->weight(FontWeight::Bold)
                                                    ->limit(50),
                                                Components\TextEntry::make('kind')
                                                    ->label('Type')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'news' => 'info',
                                                        'page' => 'gray',
                                                        'guide' => 'success',
                                                        'program' => 'warning',
                                                        'job' => 'purple',
                                                        default => 'gray',
                                                    }),
                                                Components\TextEntry::make('status')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'published' => 'success',
                                                        'draft' => 'gray',
                                                        'review' => 'warning',
                                                        'scheduled' => 'info',
                                                        'archived' => 'danger',
                                                        default => 'gray',
                                                    }),
                                                Components\TextEntry::make('created_at')
                                                    ->label('Created')
                                                    ->dateTime()
                                                    ->since(),
                                            ])
                                            ->columns(4)
                                            ->contained(false),
                                    ])
                                    ->hidden(fn ($record) => $record->posts()->count() === 0),
                                Components\Section::make()
                                    ->schema([
                                        Components\TextEntry::make('empty_posts')
                                            ->label('')
                                            ->default('No posts yet. This tenant has not created any posts. Tenant users can create posts from their panel.')
                                            ->extraAttributes(['class' => 'text-center text-gray-500']),
                                    ])
                                    ->hidden(fn ($record) => $record->posts()->count() > 0),
                            ]),

                        Components\Tabs\Tab::make('Terms')
                            ->badge(fn ($record) => $record->terms()->count())
                            ->schema([
                                Components\Section::make()
                                    ->schema([
                                        Components\RepeatableEntry::make('terms')
                                            ->label('')
                                            ->schema([
                                                Components\TextEntry::make('name')
                                                    ->label('Name')
                                                    ->weight(FontWeight::Bold),
                                                Components\TextEntry::make('taxonomy')
                                                    ->label('Type')
                                                    ->badge()
                                                    ->color(fn (string $state): string => match ($state) {
                                                        'category' => 'success',
                                                        'tag' => 'info',
                                                        'location' => 'warning',
                                                        'skill' => 'purple',
                                                        'industry' => 'gray',
                                                        default => 'gray',
                                                    }),
                                                Components\TextEntry::make('slug')
                                                    ->label('Slug')
                                                    ->color('gray')
                                                    ->copyable(),
                                                Components\TextEntry::make('created_at')
                                                    ->label('Created')
                                                    ->dateTime()
                                                    ->since(),
                                            ])
                                            ->columns(4)
                                            ->contained(false),
                                    ])
                                    ->hidden(fn ($record) => $record->terms()->count() === 0),
                                Components\Section::make()
                                    ->schema([
                                        Components\TextEntry::make('empty_terms')
                                            ->label('')
                                            ->default('No terms yet. This tenant has not created any categories, tags, or other taxonomies. Tenant users manage terms from their panel.')
                                            ->extraAttributes(['class' => 'text-center text-gray-500']),
                                    ])
                                    ->hidden(fn ($record) => $record->terms()->count() > 0),
                            ]),

                        Components\Tabs\Tab::make('Media')
                            ->badge(fn ($record) => $record->media()->count())
                            ->schema([
                                Components\Section::make()
                                    ->schema([
                                        Components\RepeatableEntry::make('media')
                                            ->label('')
                                            ->schema([
                                                Components\TextEntry::make('name')
                                                    ->label('File Name')
                                                    ->weight(FontWeight::Bold)
                                                    ->limit(40),
                                                Components\TextEntry::make('mime_type')
                                                    ->label('Type')
                                                    ->badge(),
                                                Components\TextEntry::make('size')
                                                    ->label('Size')
                                                    ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB'),
                                                Components\TextEntry::make('created_at')
                                                    ->label('Uploaded')
                                                    ->dateTime()
                                                    ->since(),
                                            ])
                                            ->columns(4)
                                            ->contained(false),
                                    ])
                                    ->hidden(fn ($record) => $record->media()->count() === 0),
                                Components\Section::make()
                                    ->schema([
                                        Components\TextEntry::make('empty_media')
                                            ->label('')
                                            ->default('No media files yet. This tenant has not uploaded any media files. Tenant users can upload media from their panel.')
                                            ->extraAttributes(['class' => 'text-center text-gray-500']),
                                    ])
                                    ->hidden(fn ($record) => $record->media()->count() > 0),
                            ]),

                        Components\Tabs\Tab::make('Settings')
                            ->schema([
                                Components\Section::make('Features')
                                    ->schema([
                                        Components\TextEntry::make('enabled_features')
                                            ->label('Enabled Features')
                                            ->badge()
                                            ->separator(',')
                                            ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                                            ->placeholder('No features enabled'),
                                    ]),

                                Components\Section::make('Branding')
                                    ->schema([
                                        Components\TextEntry::make('primary_color')
                                            ->label('Primary Color')
                                            ->placeholder('Default'),
                                        Components\TextEntry::make('secondary_color')
                                            ->label('Secondary Color')
                                            ->placeholder('Default'),
                                        Components\TextEntry::make('accent_color')
                                            ->label('Accent Color')
                                            ->placeholder('Default'),
                                    ])
                                    ->columns(3),

                                Components\Section::make('Email Configuration')
                                    ->schema([
                                        Components\TextEntry::make('email_from_name')
                                            ->label('From Name')
                                            ->placeholder('Not configured'),
                                        Components\TextEntry::make('email_from_address')
                                            ->label('From Address')
                                            ->placeholder('Not configured'),
                                    ])
                                    ->columns(2),

                                Components\Section::make('Legal & Privacy')
                                    ->schema([
                                        Components\TextEntry::make('gdpr_enabled')
                                            ->label('GDPR Compliance')
                                            ->badge()
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->formatStateUsing(fn ($state) => $state ? 'Enabled' : 'Disabled'),
                                        Components\TextEntry::make('ccpa_enabled')
                                            ->label('CCPA Compliance')
                                            ->badge()
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->formatStateUsing(fn ($state) => $state ? 'Enabled' : 'Disabled'),
                                        Components\TextEntry::make('privacy_policy_url')
                                            ->label('Privacy Policy URL')
                                            ->placeholder('Not set')
                                            ->url(fn ($state) => $state, true)
                                            ->openUrlInNewTab(),
                                        Components\TextEntry::make('terms_of_service_url')
                                            ->label('Terms of Service URL')
                                            ->placeholder('Not set')
                                            ->url(fn ($state) => $state, true)
                                            ->openUrlInNewTab(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
