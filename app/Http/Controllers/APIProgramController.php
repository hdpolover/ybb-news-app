<?php

namespace App\Http\Controllers;

use App\Models\PtProgram;
use Illuminate\Http\Request;

class APIProgramController extends Controller
{
    public function list($tenant_id)
    {
        $programs = PtProgram::whereHas('post', function($q) use ($tenant_id)
        {
            $q->where('tenant_id', $tenant_id)
            ->where('status', 'published');
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($programs);
    }

    public function read($tenant_id, $slug)
    {
        $program = PtProgram::whereHas('post', function($q) use ($tenant_id, $slug)
        {
            $q->where('tenant_id', $tenant_id)
            ->where('slug', $slug)
            ->where('status', 'published');
        })
        ->first();

        return response()->json($program);
    }
}
