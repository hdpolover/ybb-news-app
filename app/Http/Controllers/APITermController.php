<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;

class APITermController extends Controller
{
    // List kategori dengan jumlah post per tenant
    public function list($tenant_id)
    {
        $categories = Term::where('type', 'category')
            ->whereHas('posts', function($q) use ($tenant_id) {
                $q->where('tenant_id', $tenant_id)
                  ->where('status', 'published');
            })
            ->withCount(['posts' => function($q) use ($tenant_id) {
                $q->where('tenant_id', $tenant_id)
                  ->where('status', 'published');
            }])
            ->orderBy('posts_count', 'desc')
            ->take(10)
            ->get(['id','name','slug']);

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    // Detail kategori + post sesuai kategori untuk tenant tertentu
    public function read($tenant_id, $slug)
    {
        $category = Term::where('type', 'category')
            ->where('slug', $slug)
            ->whereHas('posts', function($q) use ($tenant_id) {
                $q->where('tenant_id', $tenant_id)
                  ->where('status', 'published');
            })
            ->with(['posts' => function($q) use ($tenant_id) {
                $q->where('tenant_id', $tenant_id)
                  ->where('status', 'published')
                  ->with(['author:id,name', 'terms:id,name,slug'])
                  ->orderBy('published_at', 'desc');
            }])
            ->firstOrFail(['id','name','slug']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'category' => $category->only(['id','name','slug']),
                'posts' => $category->posts
            ]
        ]);
    }
}
