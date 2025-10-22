<?php

namespace App\Http\Controllers;

use App\Models\Post;

class APINewsController extends Controller
{
    public function list($tenant_id)
    {
        $news = Post::with('author')->where('tenant_id', $tenant_id)->where('status', 'published')->orderBy('created_at', 'desc')->get();
        return response()->json($news);
    }

    public function read($tenant_id, $news_slug)
    {
        $news = Post::with('author')->where('tenant_id', $tenant_id)->where('slug', $news_slug)->first();
        return response()->json($news);
    }
}
