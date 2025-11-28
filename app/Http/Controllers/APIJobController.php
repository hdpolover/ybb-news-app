<?php

namespace App\Http\Controllers;

use App\Models\PtJob;
use Illuminate\Http\Request;

class APIJobController extends Controller
{
    public function list($tenant_id)
    {
        $jobs = PtJob::whereHas('post', function($q) use ($tenant_id)
        {
            $q->where('tenant_id', $tenant_id)
              ->where('status', 'published');
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($jobs);
    }

    public function read($tenant_id, $slug)
    {
        $job = PtJob::whereHas('post', function($q) use ($tenant_id, $slug)
        {
            $q->where('tenant_id', $tenant_id)
            ->where('slug', $slug)
            ->where('status', 'published');
        })
        ->first();

        return response()->json($job);
    }
}
