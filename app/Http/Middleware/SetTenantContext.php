<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        // Initialize tenant ID
        $currentTenantId = null;

        // Only set tenant context for regular users, not admins
        if ($user && method_exists($user, 'defaultTenant')) {
            // Get current tenant from session or use default
            $currentTenantId = session('current_tenant_id');
            
            if (!$currentTenantId) {
                // Set default tenant if not set
                $defaultTenant = $user->defaultTenant();
                if ($defaultTenant) {
                    session(['current_tenant_id' => $defaultTenant->id]);
                    $currentTenantId = $defaultTenant->id;
                }
            }

            // Verify user has access to current tenant
            if ($currentTenantId && !$user->hasAccessToTenant($currentTenantId)) {
                // User doesn't have access, redirect to first accessible tenant
                $firstTenant = $user->tenants()->first();
                if ($firstTenant) {
                    session(['current_tenant_id' => $firstTenant->id]);
                    $currentTenantId = $firstTenant->id;
                } else {
                    // User has no tenants, abort
                    abort(403, 'You are not assigned to any tenant.');
                }
            }
        }
        
        // Always share current tenant ID with views (use empty string if null to prevent Blade errors)
        // Force to string to ensure @js() directive receives a valid value
        $sharedTenantId = $currentTenantId !== null ? (string)$currentTenantId : '';
        view()->share('currentTenantId', $sharedTenantId);

        return $next($request);
    }
}

