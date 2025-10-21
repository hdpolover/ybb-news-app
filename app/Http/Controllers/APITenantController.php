<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;


class APITenantController extends Controller
{
    public function info($tenant_id)
    {
        $tenant = Tenant::find($tenant_id);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        return response()->json($tenant);
    }
}
