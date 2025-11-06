<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\SetTenantContext;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->authGuard('web')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                \App\Filament\User\Widgets\ContentStatsWidget::class,
                \App\Filament\User\Widgets\QuickActionsWidget::class,
                \App\Filament\User\Widgets\RecentActivityWidget::class,
            ])
            ->navigationGroups([
                'Content',
                'Opportunities',
                'Marketing',
                'Settings',
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetTenantContext::class, // Add tenant context middleware
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(function () {
                        /** @var User|null $user */
                        $user = Auth::guard('web')->user();
                        $currentTenant = $user?->tenants()->find(session('current_tenant_id'));
                        return $currentTenant ? "Tenant: {$currentTenant->name}" : 'Select Tenant';
                    })
                    ->icon('heroicon-o-building-office-2')
                    ->url(function () {
                        return route('filament.app.pages.switch-tenant');
                    })
                    ->visible(function () {
                        /** @var User|null $user */
                        $user = Auth::guard('web')->user();
                        return $user && $user->tenants()->count() > 0;
                    }),
            ]);
    }
}
