<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get current tenant from session
        /** @var User|null $user */
        $user = Auth::guard('web')->user();
        
        if ($user && method_exists($user, 'tenants')) {
            $currentTenantId = session('current_tenant_id');
            
            if ($currentTenantId) {
                // Check if model has tenant_id column
                if (in_array('tenant_id', $model->getFillable()) || $model->getAttribute('tenant_id') !== null) {
                    $builder->where($model->getTable() . '.tenant_id', $currentTenantId);
                }
            }
        }
    }
}
